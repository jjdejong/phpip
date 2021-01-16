#!/usr/bin/php

<?php
$opts = ['ssl' => array('verify_peer' => false, 'verify_peer_name' => false)];
$params = ['encoding' => 'UTF-8', 'soap_version' => SOAP_1_2, 'stream_context' => stream_context_create($opts)];
$client = new SoapClient('https://client.anaqua.com/WebServices/WebService_12.04/', $params);
$aqs = parse_ini_file('aqs.ini');
$db = new mysqli($aqs['mysql_host'], $aqs['mysql_user'], $aqs['mysql_pwd'], $aqs['mysql_db'], null, $aqs['mysql_socket']); // Connect to database
if ($db->connect_errno) {
    echo "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error;
    exit;
}

/* // Get last update date
$q = "SELECT max(updated) AS lastupdate FROM task WHERE code='REN' AND notes != ''";
$result = $db->query($q);
if (!$result) {
    echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
}
$myRenewal = $result->fetch_assoc();
*/

$clientcase = null;		// reference to a group of patent (String START)
$refcli = null;			// single patent reference (String START)
$uid = null;				// your internal reference (String STRICT)
$refaqs = null;			// reference AQS (String START)
$country = null;			// country code (String STRICT)
$div = null;				// division (String STRICT)
$orig = null;				// origin of the patent e.g. WO for a PCT, EP for European... (String STRICT)
$title = null;			// title of the patent (String ANY)
$nature = null;			// patent, design... (String STRICT)
$mandate = null;			// mandate type NONE|OFF|ON|WAIT (String STRICT)
$apd_start = null;		// applcation date (Date)
$apd_end = null;			// applcation date (Date)
$ap = null;				// applcation number (String START)
$pd_start = null;			// publication date (Date)
$pd_end = null;			// publication date (Date)
$pn = null;				// publication number / patent number (String START)
$entity = null;			// may be SMALL or LARGE (String STRICT)
$updtime_start = null; //date('Y-m-d', time() - (30*86400)); // -30 days or $myRenewal->lastupdate or YYYY-MM-DD;
$updtime_end = null; //date('Y-m-d');
$duedate_start = null;	// duedate (Date)
$duedate_end = null;		// duedate (Date)
$receipt_start = null;	// receipt date (Date)
$receipt_end = null;		// receipt date (Date)
$limit_offset = null;		// starting position
$limit_count = null;		// maximum number of records
$sort_order = null;		// Tag_name-{a|d}

$result = $client->PgetCalendar($aqs['aqs_user'], $aqs['aqs_pwd'], $clientcase, $refcli, $uid, $refaqs, $country, $div, $orig, $title, $nature, $mandate, $apd_start, $apd_end, $ap, $pd_start, $pd_end, $pn, $entity, $updtime_start, $updtime_end, $duedate_start, $duedate_end, $receipt_start, $receipt_end, $limit_offset, $limit_count, $sort_order);

$xml = new SimpleXMLElement($result);
//print_r($xml); exit;

$updated = 0;		// counts updated annuities
$inserted = 0;		// counts inserted annuities
$unrecognized = 0;	// counts unrecognized patents
$patsprocessed = 0;	// total patents processed
$annsprocessed = 0;	// total annuities processed
$ambiguous = 0;		// counts patents that have multiple matches


foreach ($xml->PATENT as $AQSpatent) {
    $patsprocessed++;

    if ($AQSpatent->UID != '') {
        // Check case with AQS's UID
        $q = "SELECT caseref, country, ifnull(origin, '') as origin, concat(ifnull(type_code, ''), ifnull(idx, '')) as 'div', actor_ref, alt_ref
		FROM matter JOIN matter_actor_lnk
		ON matter.id = matter_actor_lnk.matter_id
		WHERE matter_actor_lnk.role = 'ANN'
		AND matter.id = '$AQSpatent->UID'";
        $result = $db->query($q);
        if (!$result) {
            echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
        }
        if ($result->num_rows == 0) {
            echo "\r\nWARNING: no data for UID $AQSpatent->UID";
            continue;
        }
        $myRenewal = $result->fetch_object();
        if (strpos($myRenewal->caseref . $myRenewal->alt_ref, trim($AQSpatent->REFCLI)) === false) {
            // This case is OK but the reference needs to be checked
            echo "\r\nWARNING: REFCLI $AQSpatent->REFCLI does not match $myRenewal->caseref or $myRenewal->alt_ref for UID $AQSpatent->UID";
            $unrecognized++;
        }
        if ($myRenewal->country != $AQSpatent->COUNTRY) {
            // This case is wrong, go to next
            echo "\r\nERROR: COUNTRY $AQSpatent->COUNTRY does not match $myRenewal->country for UID $AQSpatent->UID";
            $unrecognized++;
            continue;
        }
        /*if ($myRenewal->origin != $AQSpatent->ORIG) {
            echo "\r\nORIG = $AQSpatent->ORIG ($AQSpatent->REFCLI$AQSpatent->COUNTRY-$AQSpatent->ORIG) does not match UID = $AQSpatent->UID";
            $unrecognized++;
            continue;
        }*/
        /*if ($myRenewal->div != $AQSpatent->DIV) {
            echo "\r\nDIV = $AQSpatent->DIV ($AQSpatent->REFCLI$AQSpatent->COUNTRY-$AQSpatent->DIV) does not match UID = $AQSpatent->UID";
            $unrecognized++;
            continue;
        }*/
        $result->close();
    } else {
        // No UID, try to find a unique ID with country, caseref, origin, type and annuity count
        $q = "SELECT matter.id, actor_ref
		FROM matter, matter_actor_lnk
		WHERE matter.id = matter_actor_lnk.matter_id
		AND matter_actor_lnk.role = 'ANN'
		AND country = '$AQSpatent->COUNTRY'
		AND (caseref = '$AQSpatent->REFCLI' OR alt_ref = '$AQSpatent->REFCLI')
		AND ifnull(origin, '') = '$AQSpatent->ORIG'
		AND if(type_code IS NULL, 1, 2) + ifnull(idx, 0) = CAST('$AQSpatent->DIV' AS UNSIGNED)";
        $result = $db->query($q);
        if (!$result) {
            echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
        }
        if ($result->num_rows == 0) {
            echo "\r\nWARNING: no data for AQS case $AQSpatent->REFSGA2-$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV";
            $unrecognized++;
            continue;
        }
        if ($result->num_rows > 1) {
            echo "\r\nWARNING: AQS case $AQSpatent->REFSGA2-$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV ($AQSpatent->REFCLI) has multiple matches - ignored";
            $ambiguous++;
            continue;
        }
        $myRenewal = $result->fetch_object();
        $AQSpatent->UID = $myRenewal->id ?? '';
        /*if ($AQSpatent->UID != '') {
            echo "\r\nAQS case $AQSpatent->REFSGA2-$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV ($AQSpatent->REFCLI) had no UID, identified it as $AQSpatent->UID";
        }*/
        $result->close();
    }

    if ($myRenewal->actor_ref != $AQSpatent->REFSGA2 . $AQSpatent->COUNTRY . '-' . $AQSpatent->ORIG . '-' . $AQSpatent->DIV) { // Case found and SGA² ref needs updating
        $q = "UPDATE matter_actor_lnk SET actor_ref = '$AQSpatent->REFSGA2$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV', updated_at = Now(), updater = 'AQS'
		WHERE matter_ID = '$AQSpatent->UID'
		AND role = 'ANN'";
        $res = $db->query($q);
        if (!$res) {
            echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
        }
    }

    foreach ($AQSpatent->EVENTS->EVENT as $renewal) {
        // Repeat for each annuity of the patent
        $annsprocessed++;
        if ($renewal->DATE_PAID == '1970-01-01') {
            continue; // Skip irrelevant date
        }

        // Identify annuity to update with AQS info
        $q = "SELECT task.id, task.cost, task.fee, task.currency, task.notes, task.done_date, task.due_date, task.invoice_step
        FROM task JOIN event ON task.trigger_id = event.id
		WHERE task.code = 'REN'
		AND event.matter_id = '$AQSpatent->UID'
		AND CAST(task.detail AS UNSIGNED) = '$renewal->YEAR'";
        $result = $db->query($q);
        if (!$result) {
            echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
        }
        $myRenewal = $result->fetch_object();

        if (isset($myRenewal->id)) {
            // The annuity event is present
            $set = [];
            if ($renewal->DUEDATE != $myRenewal->due_date) {
                $set[] = "due_date = '$renewal->DUEDATE'";
            }
            if ($renewal->CURRENCY && $myRenewal->currency != $renewal->CURRENCY) {
                $set[] = "currency = '$renewal->CURRENCY'";
            }
            $cost = ($renewal->INVOICED_COST ?? $renewal->ESTIMATED_COST) - $aqs['aqs_fee'];
            if (round($cost, 2) != round($myRenewal->cost, 2)) {
                $set[] = "cost = $cost";
                if (!$renewal->INVOICED_COST) {
                    $set[] = "notes = 'Estimated'";
                } else {
                    $set[] = "notes = 'Invoiced by AQS'";
                }
            }
            if ($cost > 1000 - $aqs['aqs_fee']) {
                $fee = round($aqs['our_fee'] + $aqs['aqs_fee'] + 0.15 * $cost, 2);
            } else {
                $fee = round($aqs['our_fee'] + $aqs['aqs_fee'] + (0.2 - (0.05 / 1000) * $cost) * $cost, 2);
            }
            if ($fee != $myRenewal->fee) {
                $set[] = "fee = $fee";
            }
            if ($renewal->DATE_PAID && $renewal->DATE_PAID != $myRenewal->done_date) {
                $set[] = "done_date = '$renewal->DATE_PAID'";
                $set[] = "step = -1";
                if (!$myRenewal->invoice_step) {
                    $set[] = "invoice_step = 1";
                }
            }
            if ($renewal->CANCELLED && $myRenewal->notes != 'Cancelled') {
                // Payment cancelled or unnecessary
                $set[] = "notes = 'Cancelled'";
            }
            if ($set) {
                $q = "UPDATE task SET " . implode(', ', $set) . ", updated_at = Now(), updater = 'AQS' WHERE id = '$myRenewal->id'";
                $result = $db->query($q);
                if (!$result) {
                    echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
                }
                echo "\r\nUpdated " . implode(', ', $set) . " for annuity $renewal->YEAR in $AQSpatent->UID ($AQSpatent->REFCLI-$AQSpatent->COUNTRY)";
                $updated++;
            }
        } else {
            // The annuity is not present, create it (same data as for update above), with due date from AQS (!= real due date)
            $somethingupdated = '';
            // First find the trigger event depending on the country
            if (in_array($AQSpatent->COUNTRY, ["US", "JP", "KR", "TW"])) {
                $q = "SELECT id from event
				WHERE matter_id = '$AQSpatent->UID'
				AND code = 'GRT'";
            } else {
                $q = "SELECT id from event
				WHERE matter_id = '$AQSpatent->UID'
				AND code = 'FIL'";
            }
            $result = $db->query($q);
            if (!$result) {
                echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
            }
            $myRenewal = $result->fetch_object();
            if (!$myRenewal) {
                // No trigger event found
                echo "\r\nWARNING: Could not find trigger event for renewal $renewal->YEAR ($renewal->DUEDATE) in $AQSpatent->REFCLI$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV - Aborted";
                continue;
            }
            $trigger_id = $myRenewal->id;
            $cost = ($renewal->INVOICED_COST ?? $renewal->ESTIMATED_COST) - $aqs['aqs_fee'];
            if ($cost > 1000 - $aqs['aqs_fee']) {
                $fee = round($aqs['our_fee'] + $aqs['aqs_fee'] + 0.15 * $cost, 2);
            } else {
                $fee = round($aqs['our_fee'] + $aqs['aqs_fee'] + (0.2 - (0.05 / 1000) * $cost) * $cost, 2);
            }
            if ($renewal->INVOICED_COST && $renewal->DATE_PAID) { // Cost provided - insert with costs
                $q = "INSERT INTO task (code, detail, done_date, due_date, currency, cost, fee, notes, trigger_id, step, invoice_step, created_at, creator, updated_at)
				VALUES ('REN', '$renewal->YEAR', '$renewal->DATE_PAID', '$renewal->DUEDATE', '$renewal->CURRENCY', $cost, $fee, 'Invoiced by AQS', $trigger_id, -1, 1, Now(), 'AQS', Now())";
                $result = $db->query($q);
                if (!$result) {
                    echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
                } else {
                    $somethingupdated = "invoiced cost $renewal->INVOICED_COST";
                }
            } elseif ($renewal->ESTIMATED_COST && !$renewal->DATE_PAID) {
                // Estimate provided
                $q = "INSERT INTO task (code, detail, due_date, cost, fee, notes, trigger_id, created_at, creator, updated_at)
				VALUES ('REN', '$renewal->YEAR', '$renewal->DUEDATE', $cost, $fee, 'Estimated', '$trigger_id', Now(), 'AQS', Now())";
                $result = $db->query($q);
                if (!$result) {
                    echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
                } else {
                    $somethingupdated = "estimated cost $renewal->ESTIMATED_COST";
                }
            } elseif (!$renewal->INVOICED_COST && $renewal->DATE_PAID) {
                // No costs provided but paid
                $q = "INSERT INTO task (code, detail, done_date, due_date, trigger_id, step, invoice_step, created_at, creator, updated_at)
				VALUES ('REN', '$renewal->YEAR', '$renewal->DATE_PAID', '$renewal->DUEDATE', '$trigger_id', -1, 1, Now(), 'AQS', Now())";
                $result = $db->query($q);
                if (!$result) {
                    echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
                } else {
                    $somethingupdated = "paid on $renewal->DATE_PAID but not invoiced";
                }
            } elseif ($renewal->INVOICED_COST && !$renewal->DATE_PAID) {
                // Invoiced but no payment date
                $q = "INSERT INTO task (code, detail, due_date, currency, cost, fee, notes, trigger_id, created_at, creator, updated_at)
				VALUES ('REN', '$renewal->YEAR', '$renewal->DUEDATE', '$renewal->CURRENCY', $cost, $fee, 'Invoiced by AQS', '$trigger_id', Now(), 'AQS', Now())";
                $result = $db->query($q);
                if (!$result) {
                    echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
                } else {
                    $somethingupdated = "invoiced cost $renewal->INVOICED_COST (but no payment date)";
                }
            } elseif ($renewal->CANCELLED) {
                // Payment cancelled or unnecessary
                $q = "INSERT INTO task (code, detail, due_date, notes, trigger_id, created_at, creator, updated_at)
				VALUES ('REN', '$renewal->YEAR', '$renewal->DUEDATE', 'Cancelled', '$trigger_id', Now(), 'AQS', Now())";
                $result = $db->query($q);
                if (!$result) {
                    echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
                } else {
                    $somethingupdated = "cancelled";
                }
            }

            if ($somethingupdated) {
                echo "\r\nInserted annuity $renewal->YEAR with $somethingupdated in $AQSpatent->UID ($AQSpatent->REFCLI$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV)";
                $inserted++;
            }
        }
    }

    //if ($updated > 0) exit; // Uncomment for debug
    //if ($inserted > 0) exit; // Uncomment for debug
    //if ($ambiguous > 0) exit; // Uncomment for debug
}

echo "\r\nAnnuities updated: $updated, inserted: $inserted, among processed: $annsprocessed\r\nPatents not recognized: $unrecognized, ambiguous: $ambiguous, total processed: $patsprocessed\r\n";
