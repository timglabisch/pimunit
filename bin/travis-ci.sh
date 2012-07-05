# install pimcore and handel current directory as Plugin Directory 
mkdir /tmp/pimcore
mkdir /tmp/pimcore_plugin
cp -R * /tmp/pimcore_plugin
rm -rf *

# install pimcore
git clone https://github.com/pimcore/pimcore /tmp/pimcore
cp -R /tmp/pimcore/pimcore pimcore
cp -R /tmp/pimcore/website_example website
cp /tmp/pimcore/index.php index.php
mkdir plugins
chmod -R 777 website/var

# install Pimunit
mkdir plugins/Pimunit
hg clone https://bitbucket.org/timg/pimunit plugins/Pimunit
chmod -R 777 /plugins/Pimunit/var

# install Pimcore Plugin
mkdir plugins/Plugin
cp /tmp/pimcore_plugin plugins/Plugin