# install pimcore and handel current directory as Plugin Directory 
mkdir /tmp/pimcore
mkdir /tmp/pimcore_plugin
cp -R * /tmp/pimcore_plugin
rm -rf *

echo "# install pimcore"
git clone https://github.com/pimcore/pimcore /tmp/pimcore
cp -R /tmp/pimcore/pimcore pimcore
cp -R /tmp/pimcore/website_example website
cp /tmp/pimcore/index.php index.php
mkdir plugins
chmod -R 777 website/var

echo "# install Pimunit"
mkdir plugins/Pimunit
git clone https://github.com/timglabisch/pimunit plugins/Pimunit
chmod -R 777 plugins/Pimunit/var

echo "# configure php"
echo "# enable short open tags"
cat `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"` | sed -e "s/short_open_tag = Off/short_open_tag = On/ig" > `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`

echo "# disable magic quotes"
cat `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"` | sed -e "s/magic_quotes_gpc = On/magic_quotes_gpc = Off/ig" > `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
echo "magic_quotes_gpc = Off" > `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`

echo "# db"
mysql -e 'create database pimcore;'
cp plugins/Pimunit/bin/travis-ci/config/system.xml website/var/config/system.xml

# install basic database
mysql --force --one-database pimcore < pimcore/modules/install/mysql/install.sql

echo "# activate Pimunit"
cp plugins/Pimunit/bin/travis-ci/config/extensions.xml website/var/config/extensions.xml

echo "# install Pimcore Plugin"
mkdir plugins/Plugin
cp -R /tmp/pimcore_plugin plugins/Plugin
