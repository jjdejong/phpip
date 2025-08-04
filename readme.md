# Introduction #

phpIP is a web tool for managing an IP rights portfolio, especially patents. It is intended to satisfy most needs of an IP law firm. The tool was designed to be flexible and simple to use. It is based on an Apache-MySQL-PHP framework.

There are many IP rights management tools out there. They are all proprietary and pretty expensive. It is not the cost, in fact, that led us to designing our own system, because the design resources we spent could equate to the cost of a couple of years license and maintenance fees of existing systems. We found that existing systems are overkill for our needs, because they are designed to satisfy the needs of a majority – hence they have more features than what each individual user needs, so they are very complex to use, yet not all specific needs of the individual user are satisfied. So the user needs to adapt to the system, whereas it should be the other way round.

Since we are patent attorneys and don't have resources for selling and maintaining our software, yet would like others to benefit from it, and hopefully contribute, we decided to open source it. This is an important step in reaching the goal of creating a tool adapted to the user's specific needs. We also designed phpIP to be extremely flexible, so that, hopefully, most users will be able to configure it (and not redesign it) to fit their needs.

Head for the [Wiki](https://github.com/jjdejong/phpip/wiki) for further information.

# New features

## 2025-08-04 Countries

Implemented tranlations for country names. 

Added a UI for managing countries, allowing the edition of renewal parameters (be careful!) and setting preselections for national phases.

Run `php artisan migrate`.

## 2025-04-18 Implemented localization for names stored in tables (event names, roles, categories, types, etc.)

Big change. You need to run `git pull; composer install; php artisan migrate`. 

For further information, read this comprehensive [guide](LOCALIZATION.md).

## 2025-03-19 Implemented localization for the UI in English, French and German

The locale set for parameter APP_LOCALE of the `.env` file is applied to the UI. This can be "en_US", "en_GB", "fr" or "de". The _US and _GB variants differ in the date formats. 

The tables containing names for the events/tasks, roles, categories, types, etc. have not been localized yet, since this requires a different approach. 

It is intended that each user can set their preferred locale in their profile, whereby a same installation can be used in diifferent languages. A user profile form has been implemented for this, but it is not fully functional yet.

## 2025-01-17 Added flexible fields for tasks in the document merge function

Now the due date of any task defined in the rules table can be included in the document template for merging. The format is {Task Name}_{Detail}_Due_Date, where "Task Name" and "Detail" are the values as provided in the rules table. Eg. ${Request_Examination_Due_Date}. Thank you @AxelDeneu.

## 2024-12-30 Created a renewal sync script for the [Renewr](https://www.renewr.io/) renewal services

By the same token, scheduled scripts such as this renewal sync script and the weekly due tasks reminder email have been moved to Artisan commands using the Laravel scheduling functionality. See [here](https://github.com/jjdejong/phpip/wiki/Renewal-Management#renewr) for further details.

## 2024-08-02 Updated the old application structure to that of Laravel 11

This involved deleting, moving and cleaning many files, yet the functionality should not change. See commit `bf00718`.

The authorization mechanism has been improved, using gates rather than policies. See commit `a4c4764`.

## 2024-01-05 A significant upgrade of the autocompletion functionality

Navigation and selection in the suggestion lists can now be performed with the keyboard.

More foolproof.

Many bugfixes.

## 2023-11-16 A significant upgrade of the back-end and front-end infrastructures

Upgraded to Laravel 10 for the back-end.

Upgraded to Bootstrap 5 for the front-end.

Removed all dependencies to jQuery by rewriting many functions that depended on it, especially the autocompletion functionality.

## 2023-02-09 Automatic family import from Open Patent Services (OPS)
 
OPS provide a REST API for accessing world-wide patent information. We have integrated this service to automatically import a whole patent family into phpIP by just providing one of the publication numbers in the family.

The tool is available through the menu `Matters->Create family from OPS`
 
Check the dedicated [Wiki section](https://github.com/jjdejong/phpip/wiki/Automatic-patent-family-import-from-Open-Patent-Services-(OPS)).

## 2021-01-08 Document drag-and-drop merge functionality

Use your favorite DOCX templates to merge them with the data of a matter displayed in phpIP by simple drag-and-drop.

Check the dedicated [Wiki section](https://github.com/jjdejong/phpip/wiki/Templates-(email-and-documents)#document-template-usage).

## 2019-12-08 Renewal process management tool

This tool manages renewal watching, first calls, reminders, payments and invoicing of renewals. Emails are created for each step for a client's portfolio. The emails may be sent automatically or to oneself as a template for resending.

Check the dedicated [Wiki article](https://github.com/jjdejong/phpip/wiki/Renewal-Management).
