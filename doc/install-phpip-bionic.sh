#!/bin/bash
# Uncomment if user should not be prompted for a MySQL password
# export DEBIAN_FRONTEND=noninteractive
echo "
********************************
Updating Ubuntu
********************************"
sudo add-apt-repository universe
apt update
apt -y upgrade
echo "
********************************
Installing Apache, MySQL, PHP 
********************************"
apt -y install lamp-server^ php7.2-simplexml php7.2-mbstring unzip git-core composer
# sed -i "s/^#application\/x-httpd-php/application\/x-httpd-php/" /etc/mime.types
echo "CREATE DATABASE phpip; GRANT ALL PRIVILEGES ON phpip.* TO phpip@localhost IDENTIFIED BY 'phpip';" | mysql
a2enmod rewrite
echo "127.0.0.1    phpip.local" >> /etc/hosts
echo "
********************************
Getting phpIP from GitHub
********************************"
cd /var/www/html
git clone https://github.com/jjdejong/phpip-v2.git phpip
cd phpip
composer install
cp .env.example .env
php artisan key:generate
php artisan config:clear
cp doc/phpip.conf /etc/apache2/sites-enabled/
service apache2 reload
echo "
********************************
Installing database
********************************"
php artisan migrate --seed
echo "Install finished. If you want to populate the database with sample data run
php artisan db:seed --class=SampleSeeder
Go to http://phpip.local and login with the credentials phpipuser:changeme"