<?php
$actor_role = array(
  array('code' => 'AGT','name' => 'Primary Agent','display_order' => '20','shareable' => '0','show_ref' => '1','show_company' => '0','show_rate' => '0','show_date' => '0'),
  array('code' => 'AGT2','name' => 'Secondary Agent','display_order' => '22','shareable' => '0','show_ref' => '1','show_company' => '0','show_rate' => '0','show_date' => '0','notes' => 'Usually the primary agent\'s agent'),
  array('code' => 'ANN','name' => 'Annuity Agent','display_order' => '21','shareable' => '0','show_ref' => '1','show_company' => '0','show_rate' => '0','show_date' => '0','notes' => 'Agent in charge of renewals. -Client handled- is a special agent who, when added, will delete any renewals in the matter'),
  array('code' => 'APP','name' => 'Applicant','display_order' => '3','shareable' => '1','show_ref' => '1','show_company' => '0','show_rate' => '0','show_date' => '0','notes' => 'Assignee in the US, i.e. the owner upon filing'),
  array('code' => 'CLI','name' => 'Client','display_order' => '1','shareable' => '1','show_ref' => '1','show_company' => '0','show_rate' => '1','show_date' => '0','notes' => 'The client we take instructions from and who we invoice. DO NOT CHANGE OR DELETE: this is also a database user role'),
  array('code' => 'CNT','name' => 'Contact','display_order' => '30','shareable' => '1','show_ref' => '1','show_company' => '1','show_rate' => '0','show_date' => '0','notes' => 'Client\'s contact person'),
  array('code' => 'DEL','name' => 'Delegate','display_order' => '31','shareable' => '1','show_ref' => '0','show_company' => '0','show_rate' => '0','show_date' => '0','notes' => 'Another user allowed to manage the case'),
  array('code' => 'FAGT','name' => 'Former Agent','display_order' => '23','shareable' => '0','show_ref' => '1','show_company' => '0','show_rate' => '0','show_date' => '0'),
  array('code' => 'FOWN','name' => 'Former Owner','display_order' => '5','shareable' => '0','show_ref' => '0','show_company' => '0','show_rate' => '0','show_date' => '1','notes' => 'To keep track of ownership history'),
  array('code' => 'INV','name' => 'Inventor','display_order' => '10','shareable' => '1','show_ref' => '0','show_company' => '1','show_rate' => '0','show_date' => '0'),
  array('code' => 'LCN','name' => 'Licensee','display_order' => '127','shareable' => '0','show_ref' => '0','show_company' => '0','show_rate' => '0','show_date' => '0'),
  array('code' => 'OFF','name' => 'Patent Office','display_order' => '127','shareable' => '0','show_ref' => '0','show_company' => '0','show_rate' => '0','show_date' => '0'),
  array('code' => 'OPP','name' => 'Opposing Party','display_order' => '127','shareable' => '0','show_ref' => '0','show_company' => '0','show_rate' => '0','show_date' => '0'),
  array('code' => 'OWN','name' => 'Owner','display_order' => '4','shareable' => '0','show_ref' => '1','show_company' => '0','show_rate' => '1','show_date' => '1','notes' => 'Use if different than applicant'),
  array('code' => 'PAY','name' => 'Payor','display_order' => '2','shareable' => '1','show_ref' => '0','show_company' => '0','show_rate' => '1','show_date' => '0','notes' => 'The actor who pays'),
  array('code' => 'PTNR','name' => 'Partner','display_order' => '127','shareable' => '1','show_ref' => '0','show_company' => '0','show_rate' => '0','show_date' => '0'),
  array('code' => 'TRA','name' => 'Translator','display_order' => '127','shareable' => '0','show_ref' => '0','show_company' => '0','show_rate' => '0','show_date' => '1'),
  array('code' => 'WRI','name' => 'Writer','display_order' => '127','shareable' => '1','show_ref' => '0','show_company' => '0','show_rate' => '0','show_date' => '0','notes' => 'Person who follows the case')
);
