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
$myPatent = $result->fetch_assoc();
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
$updtime_start = NULL; //date('Y-m-d', time() - (30*86400)); // -30 days or $myPatent['lastupdate'] or YYYY-MM-DD;
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


foreach ($xml->PATENT as $AQSpatent) {
	$patsprocessed++;
  
	if ($AQSpatent->UID != '') { 
		// Check case with SGA2's UID
		$q = "SELECT caseref, country, ifnull(origin,'') as origin, concat(ifnull(type_code,''), ifnull(idx,'')) as 'div', actor_ref
		FROM matter, matter_actor_lnk
		WHERE matter.id=matter_actor_lnk.matter_id
		AND matter_actor_lnk.role='ANN'
		AND matter.id='$AQSpatent->UID'";
		$result = $db->query($q);
		if (!$result) {
			echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
		}
		$myPatent = $result->fetch_assoc();
		if (strpos($AQSpatent->REFCLI, $myPatent['caseref']) === FALSE) {
			echo "\r\nWARNING: REFCLI=$AQSpatent->REFCLI ($AQSpatent->REFSGA2-$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV) does not match UID=$AQSpatent->UID";
			$unrecognized++;
			//This case is OK but the reference needs to be checked
		}
		if ($myPatent['country'] != $AQSpatent->COUNTRY) {
			echo "\r\nCOUNTRY=$AQSpatent->COUNTRY ($AQSpatent->REFCLI) does not match UID=$AQSpatent->UID";
			$unrecognized++;
			continue; // This case is wrong, go to next
		}
		/*if ($myPatent['origin'] != $AQSpatent->ORIG) {
			echo "\r\nORIG=$AQSpatent->ORIG ($AQSpatent->REFCLI$AQSpatent->COUNTRY-$AQSpatent->ORIG) does not match UID=$AQSpatent->UID";
			$unrecognized++;
			continue;
		}*/
		/*if ($myPatent['div'] != $AQSpatent->DIV) {
			echo "\r\nDIV=$AQSpatent->DIV ($AQSpatent->REFCLI$AQSpatent->COUNTRY-$AQSpatent->DIV) does not match UID=$AQSpatent->UID";
			$unrecognized++;
			continue;
		}*/
	} else { // No UID, try to find a unique ID with country, caseref, origin, type and annuity count
		$q = "SELECT matter.id, actor_ref
		FROM matter, matter_actor_lnk
		WHERE matter.id=matter_actor_lnk.matter_id
		AND matter_actor_lnk.role='ANN'
		AND country='$AQSpatent->COUNTRY'
		AND caseref='$AQSpatent->REFCLI'
		AND ifnull(origin,'')='$AQSpatent->ORIG'
		AND if(type_code IS NULL, 1, 2) + ifnull(idx, 0) = CAST('$AQSpatent->DIV' AS UNSIGNED)";
		// AND concat(ifnull(type_code,''), ifnull(idx,''))='$AQSpatent->DIV'";
		$result = $db->query($q);
		if (!$result) {
			echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
		}
	
		$myPatent = $result->fetch_assoc();
		if ($myPatent2 = $result->fetch_assoc()) {
			echo "\r\nSGA2 case $AQSpatent->REFSGA2-$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV ($AQSpatent->REFCLI) has multiple matches - ignored";
			$ambiguous++;
			continue;
		}
		$AQSpatent->UID = $myPatent['id'];
		/*if ($AQSpatent->UID != '') {
			echo "\r\nSGA2 case $AQSpatent->REFSGA2-$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV ($AQSpatent->REFCLI) had no UID, identified it as $AQSpatent->UID";
		}*/
	}
	if ($AQSpatent->UID == '') { // No matching id found
		echo "\r\nCould not find SGA2's $AQSpatent->REFSGA2-$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV ($AQSpatent->REFCLI$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV ?)";
		$unrecognized++;
		continue; // Patent not found in phpIP, go to next
	}
	
	if ($myPatent['actor_ref'] != $AQSpatent->REFSGA2.$AQSpatent->COUNTRY.'-'.$AQSpatent->ORIG.'-'.$AQSpatent->DIV) { // Case found and SGA² ref needs updating
		$q = "UPDATE matter_actor_lnk SET actor_ref='$AQSpatent->REFSGA2$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV'
		WHERE matter_ID='$AQSpatent->UID'
		AND role='ANN'";
		$result = $db->query($q);	
		if (!$result) {
			echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
		}
	}
	
	foreach ($AQSpatent->EVENTS->EVENT as $renewal) { // Repeat for each annuity of the patent
		$annsprocessed++;
		if ( $renewal->DATE_PAID == '1970-01-01' ) 
			continue; // Skip irrelevant date
		
		// Identify annuity to update with SGA2 info
		$q = "SELECT task.id, cost, task.notes, task.done_date, task.due_date FROM task, event
		WHERE task.trigger_id=event.id
		AND task.code='REN'
		AND event.matter_id='$AQSpatent->UID'
		AND CAST(task.detail AS UNSIGNED)='$renewal->YEAR'";
		$result = $db->query($q);
		if (!$result) {
			echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
		}
		$myPatent = $result->fetch_assoc();
	
		if ($myPatent['id'] != '') { // The annuity event is present
			$somethingupdated = '';
			if ($renewal->DUEDATE != $myPatent['due_date']) { // Due date is wrong
				$q = "UPDATE task SET due_date='$renewal->DUEDATE'
				WHERE id='$myPatent[id]'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "due date $renewal->DUEDATE";
			}
			// Update payment details
			if ($renewal->INVOICED_COST == '' && $renewal->DATE_PAID != '') {
				$renewal->INVOICED_COST = $renewal->ESTIMATED_COST; // Do this when there is a paid date but no invoiced cost
				if ($renewal->INVOICED_COST == '')
					$renewal->INVOICED_COST = 0;
			}
			if ($renewal->DATE_PAID != '' && !$renewal->CANCELLED && ($myPatent['notes'] != 'Invoiced by SGA2' || $renewal->INVOICED_COST != $myPatent['cost'] || $renewal->DATE_PAID != $myPatent['done_date'])) { // Paid date provided, event not up to date
				$q = "UPDATE task SET done_date='$renewal->DATE_PAID', done=1, notes='Invoiced by SGA2', currency='$renewal->CURRENCY', cost='$renewal->INVOICED_COST'
				WHERE id='$myPatent[id]'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query for invoiced cost in $AQSpatent->UID: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "invoiced cost $renewal->INVOICED_COST";		
			} elseif ($renewal->ESTIMATED_COST != '' && $renewal->INVOICED_COST == '' && $myPatent['notes'] != 'Estimated') { // Estimate provided, not invoiced: event not up to date - update cost with estimate
				$q = "UPDATE task SET cost='$renewal->ESTIMATED_COST', currency='$renewal->CURRENCY', notes='Estimated'
				WHERE id='$myPatent[id]'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query for estimated cost in $AQSpatent->UID: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "cost estimate $renewal->ESTIMATED_COST";
			
			} elseif ($renewal->DATE_PAID != '' && $myPatent['notes'] != 'Invoiced by SGA2') { // Paid but no cost provided - update without cost information
				$q = "UPDATE task SET done_date='$renewal->DATE_PAID', done=1
				WHERE id='$myPatent[id]'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "paid date $renewal->DATE_PAID without cost";			
			} elseif ($renewal->CANCELLED && $myPatent['notes'] != 'Cancelled') { // Payment cancelled or unnecessary
				$q = "UPDATE task SET done=1, notes='Cancelled'
				WHERE id='$myPatent[id]'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (" . $db->errno . ") " . $db->error;
				} else $somethingupdated = "cancelled";
			}
	  
			if ($somethingupdated != '') {
				echo "\r\nUpdated $somethingupdated for annuity $renewal->YEAR in $AQSpatent->UID ($AQSpatent->REFCLI-$AQSpatent->COUNTRY)";
				$updated++;
			}
		} else { // The annuity is not present, create it (same data as for update above), with due date from SGA2 (!= real due date)
			$somethingupdated = '';
		  
			// First find the trigger event depending on the country
			if (in_array($AQSpatent->COUNTRY, array("US","JP","KR","TW"))) {
				$q = "SELECT id from event
				WHERE matter_id='$AQSpatent->UID'
				AND code='GRT'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				}
				$myPatent = $result->fetch_assoc();
				$trigger_id = $myPatent['id'];
			} else { 
				$q = "SELECT id from event
				WHERE matter_id='$AQSpatent->UID'
				AND code='FIL'";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				}
				$myPatent = $result->fetch_assoc();
				$trigger_id = $myPatent['id'];
			}
		  
			if ($trigger_id == '') { // No trigger event found
				echo "\r\nCould not find trigger event for renewal $renewal->YEAR ($renewal->DUEDATE) in $AQSpatent->REFCLI$AQSpatent->COUNTRY-$AQSpatent->ORIG-$AQSpatent->DIV - Aborted";
				continue;
			}
		  
			if ($renewal->INVOICED_COST != '' && $renewal->DATE_PAID !='') { // Cost provided - insert with costs
				$q = "INSERT INTO task (code,detail,done_date,due_date,done,currency,cost,notes,trigger_id) VALUES ('REN','$renewal->YEAR','$renewal->DATE_PAID','$renewal->DUEDATE',1,'$renewal->CURRENCY','$renewal->INVOICED_COST',
				'Invoiced by SGA2','$trigger_id')";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "invoiced cost $renewal->INVOICED_COST";
			} elseif ($renewal->ESTIMATED_COST !='' && $renewal->DATE_PAID =='') { // Estimate provided
				$q = "INSERT INTO task (code,detail,due_date,cost,notes,trigger_id)
				VALUES ('REN','$renewal->YEAR','$renewal->DUEDATE','$renewal->ESTIMATED_COST','Estimated','$trigger_id')";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "estimated cost $renewal->ESTIMATED_COST";			
			} elseif ($renewal->INVOICED_COST =='' && $renewal->DATE_PAID !='') { // No costs provided but paid
				$q = "INSERT INTO task (code,detail,done_date,due_date,done,trigger_id)
				VALUES ('REN','$renewal->YEAR','$renewal->DATE_PAID','$renewal->DUEDATE',1,'$trigger_id')";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "paid on $renewal->DATE_PAID but not invoiced";		
			} elseif ($renewal->INVOICED_COST != '' && $renewal->DATE_PAID == '') { // Invoiced but no payment date
				$q = "INSERT INTO task (code,detail,due_date,currency,cost,notes,trigger_id)
				VALUES ('REN','$renewal->YEAR','$renewal->DUEDATE','$renewal->CURRENCY','$renewal->INVOICED_COST',
				'Invoiced by SGA2','$trigger_id')";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "invoiced cost $renewal->INVOICED_COST (but no payment date)";
			} elseif ($renewal->CANCELLED == '1') { // Payment cancelled or unnecessary
				$q = "INSERT INTO task (code,detail,due_date,done,notes,trigger_id)
				VALUES ('REN','$renewal->YEAR','$renewal->DUEDATE',1,'Cancelled','$trigger_id')";
				$result = $db->query($q);    
				if (!$result) {
					echo "\r\nInvalid query: (error " . $db->errno . ") " . $db->error;
				} else $somethingupdated = "cancelled";
			}
		  
			if ($somethingupdated != '') {
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
?>
