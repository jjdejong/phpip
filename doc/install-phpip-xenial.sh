#!/bin/bash
# Uncomment if user should not be prompted for a MySQL password
# export DEBIAN_FRONTEND=noninteractive
echo "
********************************
Updating Ubuntu
********************************"
apt-get update
apt-get -y upgrade
echo "
********************************
Installing Apache, MySQL, PHP and Zend Framework. 
You will be prompted for a new MySQL password - provide it and remember it
********************************"
apt-get -y install lamp-server^ zend-framework-bin git-core
sed -i "s/^#application\/x-httpd-php/application\/x-httpd-php/" /etc/mime.types
a2enmod rewrite
echo "127.0.0.1    phpip.local" >> /etc/hosts
sed -i "s/^; include/include/" /etc/php/7.0/mods-available/zend-framework.ini
phpenmod zend-framework
echo "
********************************
Getting phpIP from GitHub
********************************"
cd /var/www
git clone https://github.com/jjdejong/phpip.git
cp phpip/install/conf/phpip.conf /etc/apache2/sites-enabled/
service apache2 reload
mv phpip/application/configs/application.ini.example phpip/application/configs/application.ini
echo "
********************************
Installing database.
When prompted for a password, enter the MySQL password defined earlier
********************************"
mysql -u root -p < phpip/install/phpip_skel-current.sql