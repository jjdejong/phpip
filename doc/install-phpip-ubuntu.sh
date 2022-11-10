#!/bin/bash
echo "
********************************
Updating Ubuntu
********************************"
# Uncomment below for Ubuntu 20.04
#add-apt-repository ppa:ondrej/php -y
apt update
apt -y upgrade

echo "
********************************
Installing Apache, MySQL, PHP
********************************"
apt install -y apache2 apache2-utils
systemctl enable apache2
a2enmod rewrite

apt -y install mysql-server mysql-client
systemctl enable mysql

apt -y install php php-common libapache2-mod-php php-cli php-mysql php-json php-readline php-xml php-curl php-zip php-mbstring

apt -y install unzip git-core composer

echo "CREATE DATABASE phpip DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER phpip@localhost IDENTIFIED BY 'phpip'; GRANT ALL PRIVILEGES ON phpip.* TO phpip@localhost; SET GLOBAL log_bin_trust_function_creators = 1;" | mysql

echo "127.0.0.1    phpip.local" >> /etc/hosts

echo "
********************************
Getting phpIP from GitHub
********************************"
cd /var/www/html
git clone https://github.com/jjdejong/phpip.git phpip
cd phpip
composer install
cp .env.example .env
php artisan key:generate
php artisan config:clear
cp doc/phpip.conf /etc/apache2/sites-enabled/
chmod -R g+rw storage
chmod -R g+rw bootstrap/cache
chgrp -R www-data storage
chgrp -R www-data bootstrap/cache
service apache2 reload

echo "
********************************
Installing database
********************************"
php artisan migrate --seed

echo "
********************************
Install finished. If you want to populate the database with sample data run
the following commands: 
cd /var/www/html/phpip
php artisan db:seed --class=SampleSeeder

Go to http://phpip.local and login with the credentials phpipuser:changeme
********************************"
