#!/usr/bin/php

<?php

$ini = parse_ini_file('email-tasks.ini');
 // Connect to database
$db = new mysqli($ini['mysql_host'], $ini['mysql_user'], $ini['mysql_pwd'], $ini['mysql_db'], NULL, $ini['mysql_socket']);
if ($db->connect_errno) {
    echo "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error;
    exit;
}

// Message subject
$sbj = '[phpIP] - Tasks due in the next 30 days';
// Message body
$msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title></title>
  </head>
  <body>
    <table border="1" cellspacing="0" cellpadding="5">
      <tr>
        <th>Ref</th>
        <th>Cat</th>
        <th>Task</th>
        <th>Due</th>
        <th>Resp.</th>
      </tr>';

$query_tasks = "SELECT matter_id, CONCAT_WS('', CONCAT_WS('-', CONCAT_WS('/', concat(caseref, country), origin), type_code), idx) AS Ref,
category, concat_ws(' - ', name, detail) AS task, due_date, responsible
FROM task_list
WHERE dead = 0
AND done = 0
AND code != 'REN'
AND due_date < now() + INTERVAL 30 day
ORDER BY due_date";
$result = $db->query($query_tasks);
if (!$result) {
	echo "Invald query: (" . $db->errno . ") " . $db->error;
}

while ( $row = $result->fetch_assoc() ) {
	$msg .= "
      <tr>
        <td><a href=\"$ini[phpip_url]/$row[matter_id]\">$row[Ref]</a></td>
      	<td>$row[category]</td>
        <td>$row[task]</td>
        <td>$row[due_date]</td>
        <td>$row[responsible]</td>
      <tr>";
}

$msg .= '
    </table>
  </body>
</html>';

mail($ini['email_to'], $sbj, $msg, ['From' => $ini['email_from'], 'Bcc' => $ini['email_bcc'], 'Content-Type' => 'text/html'], "-f" . $ini['email_from']);
//Uncomment below and comment above for debug
//echo $msg;
//mail($ini['email_from', $sbj, $msg, ['From' => $ini['email_from'], 'Content-Type' => 'text/html'], "-f" . $ini['email_from']);
