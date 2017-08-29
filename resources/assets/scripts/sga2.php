#!/usr/bin/php

<?php
$opts = [ 'ssl' => array('verify_peer'=>false, 'verify_peer_name'=>false) ];
$params = [ 'encoding' => 'UTF-8', 'soap_version' => SOAP_1_2, 'stream_context' => stream_context_create($opts) ];
$client = new SoapClient('https://client.anaqua.com/WebServices/WebService_12.04/', $params);
$sga2 = parse_ini_file('sga2.ini');
//print_r($sga2); break;
$db = new mysqli('localhost', $sga2['mysql_user'], $sga2['mysql_pwd'], $sga2['mysql_db'], NULL, '/tmp/mysql.sock'); // Connect to database
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
$row = $result->fetch_assoc();
*/

$clientcase=NULL;		// reference to a group of patent (String START)
$refcli=NULL;			// single patent reference (String START)
$uid=NULL;				// your internal reference (String STRICT)
$refsga2=NULL;			// reference SGA2 (String START)
$country=NULL;			// country code (String STRICT)
$div=NULL;				// division (String STRICT)
$orig=NULL;				// origin of the patent e.g. WO for a PCT, EP for European... (String STRICT)
$title=NULL;			// title of the patent (String ANY)
$nature=NULL;			// patent, design... (String STRICT)
$mandate=NULL;			// mandate type NONE|OFF|ON|WAIT (String STRICT)
$apd_start=NULL;		// applcation date (Date)
$apd_end=NULL;			// applcation date (Date)
$ap=NULL;				// applcation number (String START)
$pd_start=NULL;			// publication date (Date)
$pd_end=NULL;			// publication date (Date)
$pn=NULL;				// publication number / patent number (String START)
$entity=NULL;			// may be SMALL or LARGE (String STRICT)
$updtime_start = NULL; //date('Y-m-d', time() - (30*86400)); // -30 days or $row['lastupdate'] or YYYY-MM-DD;
$updtime_end = NULL; //date('Y-m-d');
$duedate_start=NULL;	// duedate (Date)
$duedate_end=NULL;		// duedate (Date)
$receipt_start=NULL;	// receipt date (Date)
$receipt_end=NULL;		// receipt date (Date)
$limit_offset=NULL;		// starting position
$limit_count=NULL;		// maximum number of records
$sort_order=NULL;		// Tag_name-{a|d}

$result = $client->PgetCalendar($sga2['aqs_user'], $sga2['aqs_pwd'], $clientcase, $refcli, $uid, $refsga2, $country, $div, $orig, $title, $nature, $mandate, $apd_start, $apd_end, $ap, $pd_start, $pd_end, $pn, $entity, $updtime_start, $updtime_end, $duedate_start, $duedate_end, $receipt_start, $receipt_end, $limit_offset, $limit_count, $sort_order);

$xml = new SimpleXMLElement($result);
//print_r($xml); exit;

$updated = 0;		// counts updated annuities
$inserted = 0;		// counts inserted annuities
$unrecognized = 0;	// counts unrecognized patents
$patsprocessed = 0;	// total patents processed
$annsprocessed = 0;	// total annuities processed
$ambiguous = 0;		// counts patents that have multiple matches


foreach ($xml->PATENT as $patent) {
	$patsprocessed++;
	$UID = $patent->UID;
	$refsga2 = $patent->REFSGA2;
	$caseref = $patent->REFCLI;
	$country = $patent->COUNTRY;
	$orig = $patent->ORIG; 
	$div = $patent->DIV;
	//$mandate = $patent->MANDATE;
  
	if ($UID != '') { 
		// Check case with SGA2's UID
		$q = "SELECT caseref, country, ifnull(origin,'') as origin, concat(ifnull(type_code,''), ifnull(idx,'')) as 'div', actor_ref
		FROM matter, matter_actor_lnk
		WHERE matter.id=matter_actor_lnk.matter_id
		AND matter_actor_lnk.role='ANN'
		AND matter.id='$UID'";
		$result = $db->query($q);
		if (!$result) {
			echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
		}
		$row = $result->fetch_assoc();
		if (strpos($caseref, $row['caseref']) === FALSE) {
			echo "\r\nWARNING: REFCLI=$caseref ($refsga2-$country-$orig-$div) does not match UID=$UID";
			$unrecognized++;
			//This case is OK but the reference needs to be checked
		}
		if ($row['country'] != $country) {
			echo "\r\nCOUNTRY=$country ($caseref) does not match UID=$UID";
			$unrecognized++;
			continue; // This case is wrong, go to next
		}
		/*if ($row['origin'] != $orig) {
			echo "\r\nORIG=$orig ($caseref$country-$orig) does not match UID=$UID";
			$unrecognized++;
			continue;
		}*/
		/*if ($row['div'] != $div) {
			echo "\r\nDIV=$div ($caseref$country-$div) does not match UID=$UID";
			$unrecognized++;
			continue;
		}*/
	} else { // No UID, try to find a unique ID with country, caseref, origin, type and annuity count
		$q = "SELECT matter.id, actor_ref
		FROM matter, matter_actor_lnk
		WHERE matter.id=matter_actor_lnk.matter_id
		AND matter_actor_lnk.role='ANN'
		AND country='$country'
		AND caseref='$caseref'
		AND ifnull(origin,'')='$orig'
		AND if(type_code IS NULL, 1, 2) + ifnull(idx, 0) = CAST('$div' AS UNSIGNED)";
		// AND concat(ifnull(type_code,''), ifnull(idx,''))='$div'";
		$result = $db->query($q);
		if (!$result) {
			echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
		}
	
		$row = $result->fetch_assoc();
		if ($row2 = $result->fetch_assoc()) {
			echo "\r\nSGA2 case $refsga2-$country-$orig-$div ($caseref) has multiple matches - ignored";
			$ambiguous++;
			continue;
		}
		$UID = $row['id'];
		/*if ($UID != '') {
			echo "\r\nSGA2 case $refsga2-$country-$orig-$div ($caseref) had no UID, identified it as $UID";
		}*/
	}
	if ($UID == '') { // No matching id found
		echo "\r\nCould not find SGA2's $refsga2-$country-$orig-$div ($caseref$country-$orig-$div ?)";
		$unrecognized++;
		continue; // Patent not found in phpIP, go to next
	}
	
	if ($row['actor_ref'] != $refsga2.$country.'-'.$orig.'-'.$div) { // Case found and SGA² ref needs updating
		$q = "UPDATE matter_actor_lnk SET actor_ref='$refsga2$country-$orig-$div'
		WHERE matter_ID='$UID'
		AND role='ANN'";
		$result = $db->query($q);	
		if (!$result) {
			echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
		}
	}
	
	foreach ($patent->EVENTS->EVENT as $event) { // Repeat for each annuity of the patent
		$annsprocessed++;
		$year = $event->YEAR;
		$duedate = $event->DUEDATE; // Anniversary date (not end of month)
		$datepaid = $event->DATE_PAID;
		if ( $datepaid == '1970-01-01' ) 
			continue; // Skip irrelevant date
		$receiptdate = $event->RECEIPT;
		$invoiced = $event->INVOICED_COST;
		$estimated = $event->ESTIMATED_COST;
		$currency = $event->CURRENCY;
		$cancelled = $event->CANCELLED;
		
		
		// Identify annuity to update with SGA2 info
		$q = "SELECT task.id, cost, task.notes, task.done_date, task.due_date FROM task, event
		WHERE task.trigger_id=event.id
		AND task.code='REN'
		AND event.matter_id='$UID'
		AND CAST(task.detail AS UNSIGNED)='$year'";
		$result = $db->query($q);
		if (!$result) {
			echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
		}
		$row = $result->fetch_assoc();
	
		if ($row['id'] != '') { // The annuity event is present
			$somethingupdated = '';
			if ($duedate != $row['due_date']) { // Due date is wrong
				$q = "UPDATE task SET due_date='$duedate'
				WHERE id='$row[id]'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "due date $duedate";
			}
			// Update payment details
			if ($invoiced == '' && $datepaid != '') {
				$invoiced = $estimated; // Do this when there is a paid date but no invoiced cost
				if ($invoiced == '')
					$invoiced = 0;
			}
			if ($datepaid != '' && !$cancelled && ($row['notes'] != 'Invoiced by SGA2' || $invoiced != $row['cost'] || $datepaid != $row['done_date'])) { // Paid date provided, event not up to date
				$q = "UPDATE task SET done_date='$datepaid', done=1, notes='Invoiced by SGA2', currency='$currency', cost='$invoiced'
				WHERE id='$row[id]'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query for invoiced cost in $UID: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "invoiced cost $invoiced";		
			} elseif ($estimated != '' && $invoiced == '' && $row['notes'] != 'Estimated') { // Estimate provided, not invoiced: event not up to date - update cost with estimate
				$q = "UPDATE task SET cost='$estimated', currency='$currency', notes='Estimated'
				WHERE id='$row[id]'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query for estimated cost in $UID: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "cost estimate $estimated";
			
			} elseif ($datepaid != '' && $row['notes'] != 'Invoiced by SGA2') { // Paid but no cost provided - update without cost information
				$q = "UPDATE task SET done_date='$datepaid', done=1
				WHERE id='$row[id]'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "paid date $datepaid without cost";			
			} elseif ($cancelled && $row['notes'] != 'Cancelled') { // Payment cancelled or unnecessary
				$q = "UPDATE task SET done=1, notes='Cancelled'
				WHERE id='$row[id]'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (" . $db->errno . ") " . $db->error;
				} else $somethingupdated = "cancelled";
			}
	  
			if ($somethingupdated != '') {
				echo "\r\nUpdated $somethingupdated for annuity $year in $UID ($caseref-$country)";
				$updated++;
			}
		} else { // The annuity is not present, create it (same data as for update above), with due date from SGA2 (!= real due date)
			$somethingupdated = '';
		  
			// First find the trigger event depending on the country
			if (in_array($country, array("US","JP","KR","TW"))) {
				$q = "SELECT id from event
				WHERE matter_id='$UID'
				AND code='GRT'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				}
				$row = $result->fetch_assoc();
				$trigger_id = $row['id'];
			} else { 
				$q = "SELECT id from event
				WHERE matter_id='$UID'
				AND code='FIL'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				}
				$row = $result->fetch_assoc();
				$trigger_id = $row['id'];
			}
		  
			if ($trigger_id == '') { // No trigger event found
				echo "\r\nCould not find trigger event for renewal $year ($duedate) in $caseref$country-$orig-$div - Aborted";
				continue;
			}
		  
			if ($invoiced != '' && $datepaid !='') { // Cost provided - insert with costs
				$q = "INSERT INTO task (code,detail,done_date,due_date,done,currency,cost,notes,trigger_id) VALUES ('REN','$year','$datepaid','$duedate',1,'$currency','$invoiced',
				'Invoiced by SGA2','$trigger_id')";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "invoiced cost $invoiced";
			} elseif ($estimated !='' && $datepaid =='') { // Estimate provided
				$q = "INSERT INTO task (code,detail,due_date,cost,notes,trigger_id)
				VALUES ('REN','$year','$duedate','$estimated','Estimated','$trigger_id')";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "estimated cost $estimated";			
			} elseif ($invoiced =='' && $datepaid !='') { // No costs provided but paid
				$q = "INSERT INTO task (code,detail,done_date,due_date,done,trigger_id)
				VALUES ('REN','$year','$datepaid','$duedate',1,'$trigger_id')";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "paid on $datepaid but not invoiced";		
			} elseif ($invoiced != '' && $datepaid == '') { // Invoiced but no payment date
				$q = "INSERT INTO task (code,detail,due_date,currency,cost,notes,trigger_id)
				VALUES ('REN','$year','$duedate','$currency','$invoiced',
				'Invoiced by SGA2','$trigger_id')";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "invoiced cost $invoiced (but no payment date)";
			} elseif ($cancelled == '1') { // Payment cancelled or unnecessary
				$q = "INSERT INTO task (code,detail,due_date,done,notes,trigger_id)
				VALUES ('REN','$year','$duedate',1,'Cancelled','$trigger_id')";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "cancelled";
			}
		  
			if ($somethingupdated != '') {
				echo "\r\nInserted annuity $year with $somethingupdated in $UID ($caseref$country-$orig-$div)";
				$inserted++;
			}
		}
	}

	//if ($updated > 0) exit; // Uncomment for debug
	//if ($inserted > 0) exit; // Uncomment for debug
	//if ($ambiguous > 0) exit; // Uncomment for debug
}

echo "\r\nAnnuities updated: $updated, inserted: $inserted, among processed: $annsprocessed\r\nPatents not recognized: $unrecognized, ambiguous: $ambiguous, total processed: $patsprocessed\r\n";
?>
