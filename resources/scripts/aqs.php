#!/usr/bin/php

<?php
$aqs = parse_ini_file('aqs.ini');
$jwt_payload = '{"sub":"OMNIPAT","qsh":"/me/patents","iat":' . time() . ',"exp":' . time() + 1000 . '}';

function base64UrlEncode($data)
{
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

$jwt_signature = base64UrlEncode(hash_hmac('sha256', $aqs['jwt_header_encoded'] . '.' . base64UrlEncode($jwt_payload), $aqs['jwt_secret'], true));

$bearer_token = $aqs['jwt_header_encoded'] . '.' . base64UrlEncode($jwt_payload) . '.' . $jwt_signature;
//echo "Token: ". $bearer_token. PHP_EOL;
//exit;

$headers = [
  'Accept: application/xml',
  'Authorization: Bearer ' . $bearer_token
];

// Or for stream implementation
// $opts = [
//   'http' => [
//     'method' => "GET",
//     'header' =>
//       "Accept: application/json\n".
//       "Authorization: Bearer $bearer_token"
//   ]
// ];
// $context = stream_context_create($opts);
// $data = file_get_contents($aqs['url'], false, $context);

$curlOpts = [
  CURLOPT_HTTPHEADER => $headers,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_FAILONERROR => true
];

$ch = curl_init($aqs['url']);
curl_setopt_array($ch, $curlOpts);
$data = curl_exec($ch);
if (curl_errno($ch)) {
  echo curl_error($ch);
  exit;
}
curl_close($ch);

$xml = new SimpleXMLElement($data);

$db = new mysqli($aqs['mysql_host'], $aqs['mysql_user'], $aqs['mysql_pwd'], $aqs['mysql_db'], null, $aqs['mysql_socket']); // Connect to database
if ($db->connect_errno) {
  echo "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error;
  exit;
}

// Get Anaqua Services ID in phpIP or exit
$q = "SELECT id FROM actor WHERE name LIKE 'Anaqua%'";
$r = $db->query($q);
if ($r->num_rows == 0) {
  echo "\nERROR: no Anaqua Services in actor table";
  exit;
}
$aqs_id = $r->fetch_object()->id;

/* // Get last update date
$q = "SELECT max(updated) AS lastupdate FROM task WHERE code='REN' AND notes != ''";
$result = $db->query($q);
if (!$result) {
    echo "\nInvalid query: (error " . $db->errno . ") " . $db->error;
}
$myRenewal = $result->fetch_assoc();
*/

$updated = 0;       // counts updated annuities
$inserted = 0;      // counts inserted annuities
$unrecognized = 0;  // counts unrecognized patents
$patsprocessed = 0; // total patents processed
$annsprocessed = 0; // total annuities processed
$ambiguous = 0;     // counts patents that have multiple matches

$mandateOn = $xml->xpath('/response/item[mandate="ON"]');
foreach ($mandateOn as $AQSpatent) {
  if (!$AQSpatent->events) {
    // Patent has no renewals - skip
    continue;
  }
  $patsprocessed++;

  if ($AQSpatent->uniqueClientId) {
    // Check case with AQS's UID
    $q = "SELECT caseref, country, ifnull(origin, '') as origin, concat(ifnull(type_code, ''), ifnull(idx, '')) as 'div', actor_ref, alt_ref
		    FROM matter JOIN matter_actor_lnk
        ON matter.id = matter_actor_lnk.matter_id
		    WHERE matter_actor_lnk.actor_id = $aqs_id
		    AND matter.id = $AQSpatent->uniqueClientId";
    $result = $db->query($q);
    if (!$result) {
      echo "\nInvalid query: (error " . $db->errno . ") " . $db->error;
    }
    if ($result->num_rows == 0) {
      echo "\nWARNING: no data for UID: $AQSpatent->uniqueClientId | serviceProviderId: $AQSpatent->serviceProviderId. AQS may have been removed from case";
      $unrecognized++;
      continue;
    }
    $myRenewal = $result->fetch_object();
    if (strpos($myRenewal->caseref . $myRenewal->alt_ref, trim($AQSpatent->clientReference)) === false) {
      // This case is OK but the reference needs to be checked
      echo "\nWARNING: REFCLI $AQSpatent->clientReference does not match $myRenewal->caseref or $myRenewal->alt_ref for UID $AQSpatent->uniqueClientId";
      $unrecognized++;
    }
    if ($myRenewal->country != $AQSpatent->country) {
      // This case is wrong, go to next
      echo "\nERROR: COUNTRY $AQSpatent->country does not match $myRenewal->country for UID $AQSpatent->uniqueClientId";
      $unrecognized++;
      continue;
    }
    /*if ($myRenewal->origin != $AQSpatent->origin) {
        echo "\nORIG = $AQSpatent->origin ($AQSpatent->clientReference$AQSpatent->country-$AQSpatent->origin) does not match UID = $AQSpatent->uniqueClientId";
          $unrecognized++;
          continue;
      }*/
    /*if ($myRenewal->div != $AQSpatent->applicationType) {
        echo "\nDIV = $AQSpatent->applicationType ($AQSpatent->clientReference$AQSpatent->country-$AQSpatent->applicationType) does not match UID = $AQSpatent->uniqueClientId";
        $unrecognized++;
        continue;
      }*/
    $result->close();
  } else {
    // No UID, try to find a unique ID with country, caseref, origin, type and annuity count
    $q = "SELECT matter.id, actor_ref FROM matter
        JOIN matter_actor_lnk ON matter.id = matter_actor_lnk.matter_id
		    WHERE matter_actor_lnk.actor_id = $aqs_id
		    AND country = '$AQSpatent->country'
		    AND (caseref = '$AQSpatent->clientReference' OR alt_ref = '$AQSpatent->clientReference')
		    AND ifnull(origin, '') = '$AQSpatent->origin'
		    AND if(type_code IS NULL, 1, 2) + ifnull(idx, 0) = CAST('$AQSpatent->complement' AS UNSIGNED)";
    $result = $db->query($q);
    if (!$result) {
      echo "\nInvalid query: (error " . $db->errno . ") " . $db->error;
    }
    if ($result->num_rows == 0) {
      echo "\nWARNING: no data in our database for serviceProviderId: $AQSpatent->serviceProviderId";
      $unrecognized++;
      continue;
    }
    if ($result->num_rows > 1) {
      echo "\nWARNING: AQS case $AQSpatent->serviceProviderId ($AQSpatent->clientReference) has multiple matches - ignored";
      $ambiguous++;
      continue;
    }
    $myRenewal = $result->fetch_object();
    $AQSpatent->uniqueClientId = $myRenewal->id ?? '';
    /*if ($AQSpatent->uniqueClientId != '') {
        echo "\nAQS case $AQSpatent->serviceProviderFamilyReference-$AQSpatent->country-$AQSpatent->origin-$AQSpatent->applicationType ($AQSpatent->clientReference) had no UID, identified it as $AQSpatent->uniqueClientId";
      }*/
    $result->close();
  }

  if ($myRenewal->actor_ref != $AQSpatent->serviceProviderFamilyReference . $AQSpatent->country . '-' . $AQSpatent->origin . '-' . $AQSpatent->applicationType) {
    // Case found and SGA² ref needs updating
    $q = "UPDATE matter_actor_lnk SET actor_ref = '$AQSpatent->serviceProviderFamilyReference$AQSpatent->country-$AQSpatent->origin-$AQSpatent->applicationType', updated_at = Now(), updater = 'AQS'
		    WHERE matter_id = $AQSpatent->uniqueClientId
		    AND actor_id = $aqs_id";
    $res = $db->query($q);
    if (!$res) {
      echo "\nInvalid query: (error " . $db->errno . ") " . $db->error;
    }
  }

  foreach ($AQSpatent->events->event as $renewal) {
    // Repeat for each annuity of the patent
    if ($renewal->eventName != 'AN') {
      continue; // Skip non-renewal event
    }
    if ($renewal->paymentDate == '1970-01-01') {
      continue; // Skip irrelevant date
    }

    $annsprocessed++;

    // Identify annuity to update with AQS info
    $q = "SELECT task.id, task.cost, task.fee, task.currency, task.notes, task.done_date, task.due_date, task.invoice_step
        FROM task JOIN event ON task.trigger_id = event.id
		    WHERE task.code = 'REN'
		    AND event.matter_id = $AQSpatent->uniqueClientId
		    AND CAST(task.detail AS UNSIGNED) = $renewal->year";
    $result = $db->query($q);
    if (!$result) {
      echo "\nInvalid query: (error " . $db->errno . ") " . $db->error;
    }
    $myRenewal = $result->fetch_object();
    $serviceProviderFee = $aqs['aqs_fee'];

    if (isset($myRenewal->id)) {
      // The annuity event is present
      $set = [];
      if ($renewal->dueDate != $myRenewal->due_date) {
        $set[] = "due_date = '$renewal->dueDate'";
      }
      if ($renewal->clientCurrency && $myRenewal->currency != $renewal->clientCurrency) {
        $set[] = "currency = '$renewal->clientCurrency'";
      }
      if (isset($renewal->invoicedFees->total)) {
        $serviceProviderFee = $renewal->invoicedFees->serviceProvider;
        $cost = $renewal->invoicedFees->total - $serviceProviderFee;
      } elseif (isset($renewal->estimatedFees->total)) {
        $serviceProviderFee = $renewal->estimatedFees->serviceProvider;
        $cost = $renewal->estimatedFees->total - $serviceProviderFee;
      } else {
        $cost = 0;
      }
      if (is_null($myRenewal->cost)) {
        $myRenewal->cost = 0;
      }
      if (round($cost, 2) != round($myRenewal->cost, 2)) {
        $set[] = "cost = $cost";
        if (isset($renewal->invoicedFees->total)) {
          $set[] = "notes = 'Invoiced by AQS'";
        } else {
          $set[] = "notes = 'Estimated'";
        }
      }
      if ($cost > 1000 - $serviceProviderFee) {
        $fee = round($aqs['our_fee'] + $serviceProviderFee + 0.15 * $cost, 2);
      } elseif ($cost != 0) {
        $fee = round($aqs['our_fee'] + $serviceProviderFee + (0.2 - (0.05 / 1000) * $cost) * $cost, 2);
      } else {
        $fee = 0;
      }
      if ($fee != $myRenewal->fee) {
        $set[] = "fee = $fee";
      }
      if (strlen($renewal->paymentDate) > 0 && $renewal->paymentDate != $myRenewal->done_date) {
        $set[] = "done_date = '$renewal->paymentDate'";
        $set[] = "step = -1";
        if (!$myRenewal->invoice_step) {
          $set[] = "invoice_step = 1";
        }
      }
      if ($renewal->cancelled == '1' && $myRenewal->notes != 'Cancelled') {
        // Payment cancelled or unnecessary
        $set[] = "notes = 'Cancelled'";
      }
      if ($set) {
        $q = "UPDATE task SET " . implode(', ', $set) . ", updated_at = Now(), updater = 'AQS' WHERE id = '$myRenewal->id'";
        $result = $db->query($q);
        if (!$result) {
          echo "\nInvalid query: (error " . $db->errno . ") " . $db->error;
        }
        echo "\nUpdated " . implode(', ', $set) . " for annuity $renewal->year in $AQSpatent->uniqueClientId ($AQSpatent->clientReference-$AQSpatent->country)";
        $updated++;
      }
    } else {
      // The annuity is not present, create it (same data as for update above), with due date from AQS (!= real due date)
      $somethingupdated = '';
      // First find the trigger event depending on the country
      if (in_array($AQSpatent->country, ["US", "JP", "KR", "TW"])) {
        $q = "SELECT id from event
				    WHERE matter_id = '$AQSpatent->uniqueClientId'
				    AND code = 'GRT'";
      } else {
        $q = "SELECT id from event
				    WHERE matter_id = '$AQSpatent->uniqueClientId'
				    AND code = 'FIL'";
      }
      $result = $db->query($q);
      if (!$result) {
        echo "\nInvalid query: (error " . $db->errno . ") " . $db->error;
      }
      $myRenewal = $result->fetch_object();
      if (!$myRenewal) {
        // No trigger event found
        echo "\nWARNING: Could not find trigger event for renewal $renewal->year ($renewal->dueDate) in $AQSpatent->clientReference$AQSpatent->country-$AQSpatent->origin-$AQSpatent->applicationType - Aborted";
        continue;
      }
      $trigger_id = $myRenewal->id;
      if (isset($renewal->invoicedFees->total)) {
        $serviceProviderFee = $renewal->invoicedFees->serviceProvider;
        $cost = $renewal->invoicedFees->total - $serviceProviderFee;
      } else {
        $serviceProviderFee = $renewal->estimatedFees->serviceProvider;
        $cost = $renewal->estimatedFees->total - $serviceProviderFee;
      }
      if ($cost > 1000 - $serviceProviderFee) {
        $fee = round($aqs['our_fee'] + $serviceProviderFee + 0.15 * $cost, 2);
      } elseif ($cost != 0) {
        $fee = round($aqs['our_fee'] + $serviceProviderFee + (0.2 - (0.05 / 1000) * $cost) * $cost, 2);
      } else {
        $fee = 0;
      }
      if (isset($renewal->invoicedFees->total) && strlen($renewal->paymentDate) > 0) { // Cost provided - insert with costs
        $q = "INSERT INTO task (code, detail, done_date, due_date, currency, cost, fee, notes, trigger_id, step, invoice_step, created_at, creator, updated_at)
				    VALUES ('REN', '$renewal->year', '$renewal->paymentDate', '$renewal->dueDate', '$renewal->clientCurrency', $cost, $fee, 'Invoiced by AQS', $trigger_id, -1, 1, Now(), 'AQS', Now())";
        $result = $db->query($q);
        if (!$result) {
          echo "\nInvalid query: (error " . $db->errno . ") " . $db->error;
        } else {
          $somethingupdated = "invoiced cost $renewal->invoicedFees->total";
        }
      } elseif (isset($renewal->estimatedFees->total) && strlen($renewal->paymentDate) == 0) {
        // Estimate provided
        $q = "INSERT INTO task (code, detail, due_date, cost, fee, notes, trigger_id, created_at, creator, updated_at)
				    VALUES ('REN', '$renewal->year', '$renewal->dueDate', $cost, $fee, 'Estimated', '$trigger_id', Now(), 'AQS', Now())";
        $result = $db->query($q);
        if (!$result) {
          echo "\nInvalid query: (error " . $db->errno . ") " . $db->error;
        } else {
          $somethingupdated = "estimated cost $cost";
        }
      } elseif (strlen($renewal->invoicedFees->total) == 0 && strlen($renewal->paymentDate) > 0) {
        // No costs provided but paid
        $q = "INSERT INTO task (code, detail, done_date, due_date, trigger_id, step, invoice_step, created_at, creator, updated_at)
				    VALUES ('REN', '$renewal->year', '$renewal->paymentDate', '$renewal->dueDate', '$trigger_id', -1, 1, Now(), 'AQS', Now())";
        $result = $db->query($q);
        if (!$result) {
          echo "\nInvalid query: (error " . $db->errno . ") " . $db->error;
        } else {
          $somethingupdated = "paid on $renewal->paymentDate but not invoiced";
        }
      } elseif (isset($renewal->invoicedFees->total) && strlen($renewal->paymentDate) == 0) {
        // Invoiced but no payment date
        $q = "INSERT INTO task (code, detail, due_date, currency, cost, fee, notes, trigger_id, created_at, creator, updated_at)
				    VALUES ('REN', '$renewal->year', '$renewal->dueDate', '$renewal->clientCurrency', $cost, $fee, 'Invoiced by AQS', '$trigger_id', Now(), 'AQS', Now())";
        $result = $db->query($q);
        if (!$result) {
          echo "\nInvalid query: (error " . $db->errno . ") " . $db->error;
        } else {
          $somethingupdated = "invoiced cost $cost (but no payment date)";
        }
      } elseif ($renewal->cancelled == '1') {
        // Payment cancelled or unnecessary
        $q = "INSERT INTO task (code, detail, due_date, notes, trigger_id, created_at, creator, updated_at)
				    VALUES ('REN', '$renewal->year', '$renewal->dueDate', 'Cancelled', '$trigger_id', Now(), 'AQS', Now())";
        $result = $db->query($q);
        if (!$result) {
          echo "\nInvalid query: (error " . $db->errno . ") " . $db->error;
        } else {
          $somethingupdated = "cancelled";
        }
      }
      if ($somethingupdated) {
        echo "\nInserted annuity $renewal->year with $somethingupdated in $AQSpatent->uniqueClientId ($AQSpatent->clientReference$AQSpatent->country-$AQSpatent->origin-$AQSpatent->applicationType)";
        $inserted++;
      }
    }
  }

  // Uncomment for debug
  // if ($updated > 0) {
  //   print_r($renewal);
  //   exit;
  // }
  //if ($inserted > 0) exit; // Uncomment for debug
  //if ($ambiguous > 0) exit; // Uncomment for debug
}

echo "\nAnnuities updated: $updated, inserted: $inserted, among processed: $annsprocessed\nPatents not recognized: $unrecognized, ambiguous: $ambiguous, total processed: $patsprocessed\n";
