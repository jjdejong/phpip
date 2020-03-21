# Introduction #
This part of the application allow to manage calls to renew industrial property titles. It consists of two workflows, the first to manage calls, payment and receipts, the second to manage invoicing. Calls are done via email.

# Usage #
The access is from the menu `Matters/Manage renewals`.
Different steps are accessible. At each step, specific action buttons listed below the row of step buttons are proposed. At each step, a contextual list of renewal tasks is displayed. Actions act on selected rows.
## First call ##
The available action is to send first calls. After sending, tasks are transferred to step `Reminder`. The price values are issued from `fee` and `cost` columns from `fees` table, or from `fee_reduced` and `cost_reduced` columns according to the presence of the classifier "SME status" recorded in the matter.
## Reminder ##
The available actions are:
- `Send reminder` to send reminder calls. Only the subject of the message is changed, the content is still the same as the first call.
- `Send last reminder` to send reminder just before the due date. After sending, tasks are tagged with "grace period" tick. The price values are issued from `fee` and `cost` columns from `fees` table, or from `fee_reduced` and `cost_reduced` columns according to the presence of the classifier "SME status" recorded in the matter, but `fees` or `fee_reduced` value is changed with the `fee_factor` from the configuration.
- `Register order` to mark that instructions has been received to pay. Tasks are then transferred to `Payment` step.
- `Abandon` to mark that instructions has been given to abandon the case. Tasks are then transferred to `Abandoned` step and marked as done.
- `Lapsed` to mark that office has declared that the case is lapsed, without received instructions. Tasks are then transferred to `Closed` step.

## Payment ##
- `Generate order` allows to download a XML file which can be uploaded on office portal;
- `Clear as paid` is to use when payment or order to pay are done, with external means or with the previous XML file. This action is to do also when order has been generated. Tasks are added in both cases to `Invoicing` step and `Receipts` step.

## Receipts  ##
- `Receipt received` allows to register that the receipt has been emitted by the office. Tasks are transferred to `Receipts received`.

## Receipts received ##
- `Send receipts` allows to register that the receipt has been sent to the client. Tasks are transferred to `Closed`.

## Abandoned ##
- `Lapse` is used to register that the abandoned case has now received a communication from the office that the case has lapsed.

## Lapsed ##
- `Send lapse communication` is used to register that the communication from the office has been sent. There is no more action to do and the case is in `closed` step.

## Invoicing ##
- `Invoice` generates invoices, tasks being grouped by client, using Dolibarr. Tasks are transferred to `Invoiced` step. The price values are issued from `fees` and `cost` columns from `fees` table, or from `fee_reduced` and `cost_reduced` columns according to the presence of the classifier "SME status" recorded in the matter. When `grace period` is ticked and the due date is not over, `fee_factor` is applied. When the due date is over the done date, the price values are issued from `fees_sup` and `cost_sup` columns from `fees` table, or from `fee_sup_reduced` and `cost_sup_reduced` columns according to the presence of the classifier "SME status" recorded in the matter.

## Invoiced ##
This step displays the list of already invoiced tasks. There is no more action to do.

# Logs #
Each action is registered. Logs can be viewed in logs/ page. Lines can be filtered by job, client, date, casenumber or user.

# Settings #
## General ##
Some settings are included in the file `config/renewal.php`. The syntax is the one of an array in PHP. The array is subdivided in several sections
- `general`:
 - `paginate` set the number of lines in the page listing renewals.
- `api`: used to specify access to Dolibarr application, allowing invoicing. If other tool is wanted for invoicing, the function `invoice` in RenewalController.php is to hack.
- `validity`: configure indications about the validity of the call.
 - `instruct_before`: in days, the delay before the earliest due date of the call;
  - `fee_factor`: factor applied to fees for late instructions;
  - `before`: in days before the due date, delay to consider instructions as late,
  - `before_last`: in days before the due date, delay to ask for last limit instruction.
- `xml`: used to configure xml files for orders according to EPO-batch-payment.dtd https://www.epo.org/law-practice/legal-texts/official-journal/2015/etc/se3/p75.html . The setting has `header` and `footer`, and fees will be inserted between them. It has also `EP_Deposit` and `FR_Deposit` to include the deposit account near the office which will be debited. The following items will be replaced:
 - NAME: the user name
 - TRANSACTION: the date
 - DEPOSIT: the account according to the office/country
 - TOTAL: the amount of fees in the transaction
 - COUNT: the number of transaction
Note that the payment portals of different offices will ignore some of these fields.

## Email templates ##
The Laravel's emailing backend is used for sending emails. Report to Laravel configuration according to your infrastructure for settings.
The message of the emails are blade templates stored in `resources/views/email`. `firstcall.blade.php` is used for first call and reminders. `lastcall.blade.php` is used for last reminder just before the grace period. `warncall.blade.php` is used for reminders in grace period.
They use the variables:
- `$dest` for salutations;
- `$instruction_date`
- `$renewals` to iterate for renewals;
- `$validity_date`
- `total_ht`: total without VAT;
- `total`: total with VAT.
