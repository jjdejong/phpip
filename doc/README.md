The back-end for operating is a web server (Apache, PHP, MySQL, and a virtual host setup pointing to the `public` sub-folder...). You need MySQL version 5.7 or greater (or MariaDB 10.2 or greater), supporting virtual columns.

These instructions are provided for Ubuntu Server editions. They have been tested with 18.04 (Bionic). You may need to adapt them for other Linux distributions.

_NOTE that the user interface has only been developed and tested with recent FireFox releases (60 is known to not work). There may be artifacts with other web browsers - share your issues with us._

# 0. Automated installation #

A fully unattended installation script is available for a _fresh_ install of a vanilla Ubuntu Bionic server. Download it on your server, make it executable, and run it with sudo:
```
wget https://github.com/jjdejong/phpip-v2/raw/master/doc/install-phpip-bionic.sh
chmod a+x install-phpip-bionic.sh
sudo ./install-phpip-bionic.sh 
```
You may then skip the rest of the below instructions up to the "Upgrading" section. If something doesn't work, check the below details.

# 1. Installing the required packages #

Make sure your software is up to date and you have the universe repository enabled. In a console, type:
```
sudo add-apt-repository universe
sudo apt update
sudo apt upgrade
```

## 1.1. Apache, PHP and MySQL #

Install these and other needed dependencies (Git and Composer) as follows:
    
In a console, type:

```
sudo apt install lamp-server^ php7.2-simplexml php7.2-mbstring unzip git-core composer
```
Create the database `phpip` accessible with all privileges by user `phpip` with password `phpip` (change that in production!):
```
echo "CREATE DATABASE phpip; GRANT ALL PRIVILEGES ON phpip.* TO phpip@localhost IDENTIFIED BY 'phpip';" | sudo mysql
```
(This command assumes that mysql has been freshly installed with default options, where no password is required for root when running mysql with sudo.)

## 1.2. phpIP #

The code can be installed anywhere with the virtual server approach, but it makes sense to install it in `/var/www/html/phpip`. Create the folder and change its owner to yourself so that you don't need to use `sudo` to work there:
```
sudo mkdir /var/www/html/phpip
sudo chown <your login> /var/www/html/phpip
```
Clone the `phpip-v2` Git repository to the folder `/var/www/html/phpip`:
```
git clone https://github.com/jjdejong/phpip-v2.git /var/www/html/phpip
```
Install Laravel's dependencies:
```
cd /var/www/html/phpip
composer install
```
Create an `.env` file with your database credentials. You can copy the provided `.env.example` file (and tailor it later):
```
cp .env.example .env
```
Generate a fresh Laravel configuration:
```
php artisan key:generate
php artisan config:clear
```
Set some write permissions for the web server:
```
chmod -R g+rw storage
chmod -R g+rw bootstrap/cache
chgrp -R www-data storage
chgrp -R www-data bootstrap/cache
```

# 2. Configuring Apache #

## 2.1 Quick start and check #

To run a quick test, point your browser to `http://localhost/phpip/public`.

You should see a cover page with a login link. You won't get past that, because no tables or users have been installed yet in MySQL.

## 2.2 Virtual host in Apache #

This is maybe the most complex configuration section.

  * In the console, type:
```
sudo nano /etc/apache2/sites-enabled/phpip.conf
```
  * Paste the following in nano's edit window:
```
<VirtualHost *:80>
    ServerName phpip.local
    DocumentRoot /var/www/html/phpip/public
    ErrorLog /var/log/phpip-apache2/error.log
    CustomLog /var/log/apache2/phpip-access.log combined
</VirtualHost>
<Directory /var/www/html/phpip/public>
    Options Indexes MultiViews FollowSymLinks
    DirectoryIndex index.php
    AllowOverride All
    Order allow,deny
    Allow from all
</Directory>
```
Enable mod\_rewrite in Apache:
```
sudo a2enmod rewrite
```
Save and reload Apache:
```
sudo systemctl restart apache2
```
You then need to create a DNS entry mapping name "phpip.local", i.e. the value of parameter **ServerName** in the above **VirtualHost** definition, to the IP address of your server. If this is obscure to you, the simplest is to add the following line in the "hosts" file of the workstations that will access phpIP:
```
<your server's IP address>    phpip.local
```
On Windows workstations, the "hosts" file is usually in:
<font color='blue'>c:\windows\system32\drivers\etc\hosts</font>

On Macs and Linux workstations, it is located in <font color='blue'>/etc/hosts</font>.

Now point your browser to http://phpip.local.

You should see the cover page and login link again. You still won't get past that, because you have no database yet.

# 3. Database

## 3.1 Starting a new database

* Run `php artisan migrate --seed`
This creates a blank database with basic configuration. You're ready to go with the credentials `phpipuser:changeme`.

* For playing around with sample data, further run `php artisan db:seed --class=SampleSeeder`.

## 3.2 Migrating an existing v1 installation

### Migrate the v1 database

* Backup the `phpip` MySQL schema.
* Upgrade the `phpip` schema with the script provided in `/database/migrations/sql`.
* Apply further updates to the tables using Laravel's "migration" process, i.e. run `php artisan migrate` from the root folder - this will apply any new script present in `database/migrations` since last running that command.

### Migrate the passwords

You need to update the `password` field of your users. Logins are based on the `login` and `password` fields in the `actor` table only (they are no longer replicated in the MySQL users table). Authorizations will be implemented through the `default_role` field of the users.

The passwords are hashed with _bcrypt_ instead of _md5_, and don't use a user-provided salt. So you need to change all the md5+salt passwords of v1 to _bcrypt_ ones. 

* **Using the password reset functionality of the UI:** make sure your users have emails and configure your mail service in the `.env` file. If you have no mail service, set `MAIL_DRIVER=log`. In the login screen, use the "Reset password" function. A reset mail will be sent with a link to change the password. If you have no mail service the reset mail body is logged in `/storage/logs/<latest file>`. Copy the reset link you find there in your browser, and you're done.
* **Or changing the password hashes manually** in the `actor` table with a bcrypt hash. You can generate a bcrypt hash using the command `php -r 'echo password_hash("your password",PASSWORD_BCRYPT) . "\n";'`.

To fire a quick test, run `php artisan serve`, and point your browser to http://localhost:8000.

## 3.3 Upgrading
The software is under development, so many changes can occur. To stay up to date, dont forget to regularly pull the new commits by running:
* `git pull` and
* `composer install`

The database structure may be updated too, so you need to apply the new migration scripts in `database/migrations`. Just run `php artisan migrate` in the root folder, which will apply the latest scripts.
