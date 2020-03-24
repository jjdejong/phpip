# Introduction #

This part of the application allows to manage calls to renew IP titles. It consists of two workflows, the first to manage calls, payment and receipts, the second to manage invoicing. Calls are done via email.

# Usage #

The access is from the menu `Matters->Manage renewals`.

Different steps are accessible. At each step, specific action buttons listed below the row of step buttons are proposed. At each step, a contextual list of renewal tasks is displayed. Actions act on selected rows.

## First call ##

The available action is to send first calls. After sending, tasks are transferred to step `Reminder`. The cost values are issued from the `fee` and `cost` columns of the `fees` table, or from the `fee_reduced` and `cost_reduced` columns when the classifier "SME status" is present in in the matter.

## Reminder ##

The available actions are:
- `Send reminder` to send reminder calls. Only the subject of the message is changed, the content is still the same as the first call.
- `Send last reminder` to send a reminder just before the due date. After sending, tasks are tagged with "grace period". The cost values are issued from the `fee` and `cost` columns of the `fees` table, or from the `fee_reduced` and `cost_reduced` columns when the classifier "SME status" is present in the matter, but the `fees` or `fee_reduced` value is changed with the `fee_factor` from the configuration.
- `Register order` to mark that instructions have been received to pay. Tasks are then transferred to the `Payment` step.
- `Abandon` to mark that instructions have been received to abandon the case. Tasks are then transferred to the `Abandoned` step and marked as done.
- `Lapsed` to mark that the Office has declared the case as lapsed, without received instructions. Tasks are then transferred to the `Closed` step.

## Payment ##

- `Generate order` produces an XML file that can be uploaded to office portals;
- `Clear as paid` is used when the payment or the order to pay is achieved with external means or with the previous XML file. This action is used also when an order has been generated. Tasks are added in both cases to the `Invoicing` step and the `Receipts` step.

## Receipts  ##

- `Receipt received` tracks the receipts emitted by the Office. Tasks are transferred to `Receipts received`.

## Receipts received ##

- `Send receipts` tracks the receipts sent to the client. Tasks are transferred to `Closed`.

## Abandoned ##

- `Lapse` tracks lapse communications from the Office.

## Lapsed ##

- `Send lapse communication` tracks that the communication from the Office has been sent to the client. There are no more actions and the case is in the `closed` step.

## Invoicing ##

- `Invoice` generates invoices, tasks being grouped by client, using Dolibarr. Tasks are transferred to the `Invoiced` step. The cost values are taken from the `fees` and `cost` columns of the `fees` table, or from the `fee_reduced` and `cost_reduced` columns when the classifier "SME status" is present in the matter. When `grace period` is checked and the due date is not past, `fee_factor` is applied. When the due date is past the done date, the cost values are taken from the `fees_sup` and `cost_sup` columns of the `fees` table, or from the `fee_sup_reduced` and `cost_sup_reduced` columns when the classifier "SME status" is present in the matter.

## Invoiced ##

This step displays the list of already invoiced tasks. There are no other actions.

# Settings #

## General ##

Some settings are included in the file `config/renewal.php`. The syntax is that of a PHP array. The array is subdivided in several sections
- `general`:
 - `paginate` set the number of lines in the page listing renewals.
- `api`: used to specify access to the Dolibarr API, allowing invoicing. If another tool is used for invoicing, the `invoice` method of RenewalController.php is to be changed.
- `validity`: configure indications about the validity of the call.
 - `instruct_before`: in days, the delay before the earliest due date of the call;
  - `fee_factor`: factor applied to fees for late instructions;
  - `before`: in days before the due date, delay to consider instructions as late,
  - `before_last`: in days before the due date, delay to ask for last limit instruction.
- `xml`: used to configure xml files for orders according to EPO-batch-payment.dtd https://www.epo.org/law-practice/legal-texts/official-journal/2015/etc/se3/p75.html. The setting has `header` and `footer`, and fees will be inserted between them. It has also `EP_Deposit` and `FR_Deposit` tags to include the Office deposit account to debit. The following items will be replaced:
 - NAME: the user name
 - TRANSACTION: the date
 - DEPOSIT: the account according to the office/country
 - TOTAL: the total fee amount in the transaction
 - COUNT: the number of transactions
Note that the payment portals of different offices will ignore some of these fields.

## Email templates ##

Laravel's emailing backend is used for sending emails. Refer to Laravel configuration according to your infrastructure for settings.

The message bodies of the emails are blade templates stored in `resources/views/email`. `firstcall.blade.php` is used for first call and reminders. `lastcall.blade.php` is used for last reminder just before the grace period. `warncall.blade.php` is used for reminders during the grace period.

They use the variables:
- `$dest` for greetings;
- `$instruction_date`
- `$renewals` to iterate for renewals;
- `$validity_date`
- `total_ht`: total without VAT;
- `total`: total with VAT.
