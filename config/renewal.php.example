<?php

return [
  "general" => [
    "paginate" => 30,
    // Set to "user" if emails should be sent to the connected user
    "mail_recipient" => "client",
    // Set to 0 to exclude VAT values
    "vat_column" => 1,
    // Use or hide receipt management tabs. Set to 0 to hide
    "receipt_tabs" => 1
  ],
  "validity" => [
    "instruct_before" => 30,
    "fee_factor" => 1.2,
    "before" => 8,
    "before_last" => 2
  ],
  "invoice" => [
    // Set to "dolibarr" if you are using Dolibarr for invoices
    "backend" => "none",
    "vat_rate" => 0.2,
    "default_fee" => 145,
    // Captions for CVS export when Dolibarr is not used. Use in the order of the Task::renewals() output
    "captions" => [
      'task.id',
      'task.detail',
      'task.due_date',
      'task.done',
      'task.done_date',
      'matter_id',
      'cost',
      'fee',
      'cost_reduced',
      'fee_reduced',
      'cost_sup',
      'fee_sup',
      'cost_sup_reduced',
      'fee_sup_reduced',
      'task.trigger_id',
      'category',
      'matter.caseref',
      'matter.uid',
      'matter.country',
      'country_FR',
      'country_EN',
      'country_DE',
      'matter.origin',
      'small_entity',
      'fil_date',
      'fil_num',
      'grt_date',
      'event_name',
      'event_date',
      'number',
      'applicant_name',
      'client_name',
      'client_address',
      'client_country',
      'ren_discount',
      'client_id',
      'client_ref',
      'client_email',
      'client_language',
      'responsible',
      'short_title',
      'title',
      'pub_num',
      'task.step',
      'task.grace_period',
      'task.invoice_step',
      'expire_date'
    ],
  ],
  "api" => [
    'dolibarr_url' => 'http://localhost/dolibarr/htdocs/api/index.php',
    'DOLAPIKEY' => 'your Dolibarr key',
    'fk_account' => 2
  ],
  "description" => [
    'fr' => [
      'line1' => '%s : Annuité du titre n°%s',
      'filed' => ' déposé le ',
      'granted' => ' délivré le ',
      'line2' => 'Votre référence : %s',
      'line3' => 'Sujet : %s',
    ],
    'en' => [
      'line1' => '%s: Annuity of No %s',
      'filed' => ' filed on ',
      'granted' => ' granted on ',
      'line2' => 'Your ref: %s',
      'line3' => 'Subject: %s',
    ],
  ],
  "xml" => [
    'body' => '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE batch-payment SYSTEM "batch-payment-v2-00.dtd" >
<batch-payment dtd-version="" date-produced="" ro="">
  <header>
  	<sender>
  		<name>' . env('COMPANY_NAME', 'NAME') . '</name>
  		<registered-number></registered-number>
  	</sender>
    <send-date></send-date>
  	<mode-of-payment payment-type="deposit">
  		<deposit-account>
  			<account-no>DEPOSIT</account-no>
  		</deposit-account>
  	</mode-of-payment>
  	<payment-reference-id>TRANSACTION</payment-reference-id>
  </header>
  <detail>
  </detail>
  <trailer>
    <mode-of-payment payment-type="deposit">
  		<deposit-account>
  			<account-no>DEPOSIT</account-no>
  		</deposit-account>
  	</mode-of-payment>
  	<batch-pay-total-amount currency="EUR">TOTAL</batch-pay-total-amount>
  	<total-records>COUNT</total-records>
  </trailer>
</batch-payment>',
    'EP_deposit' => 'Your EPO account',
    'FR_deposit' => 'Your INPI account',
  ],
];
