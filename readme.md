A port of phpIP from Zend Framework 1 to Laravel is ongoing here.

The back-end for operating v2 is identical to that for v1 (Apache, PHP, MySQL, and a virtual host setup pointing to the `public` sub-folder...). See the [v1 instructions](https://github.com/jjdejong/phpip/wiki/Installing). 

# To get going

* Clone the `phpip-v2` Git repository to a folder, say `phpip-v2`.
* Install [composer](https://getcomposer.org/), then run `composer install` in the `phpip-v2` folder.
* Create an `.env` file with your database credentials (copy and tailor the provided `.env.example` file).
* Run `php artisan key:generate; php artisan config:clear` (a command-line php is required).

## Start a new database
* Run `php artisan migrate --seed`
This creates a blank database with basic configuration. You're ready to go with the credentials phpipuser/changeme.

* For adding sample data, run `php artisan db:seed --class=SampleSeeder`.

## Migrate an existing v1 installation.
* Copy the `phpip` MySQL schema to a new one (say `phpipv2`).
* Upgrade the `phpipv2` schema with the script provided in `/doc/scripts`.

Then you need to update the `password` field of your users. Logins are based on the `login` and `password` fields in the `actor` table only (they are no loger replicated in the MySQL users table). Authorizations will be implemented through the `default_role` field of the users - set this field to "DBA" to get full permissions in the future.

The passwords are hashed with _bcrypt_ instead of _md5_, and don't use a user-provided salt. So you need to change all the md5+salt passwords of v1 to _bcrypt_ ones. You can use the password reset functionality of the UI or change the password hashes manually in the `actor` table with a bcrypt hash. You can generate a bcrypt hash using the command `php -r 'echo password_hash("your password",PASSWORD_BCRYPT) . "\n";'`.

To fire a quick test, run `php artisan serve`, and point your browser to http://localhost:8000.
