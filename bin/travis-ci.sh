# install pimcore and handel current directory as Plugin Directory 
mkdir /tmp/.pimunit
cp -R . /tmp/.pimunit
rm -rf .??*
git clone https://github.com/pimcore/pimcore .
mv plugins_example plugins
mkdir plugins/Pimsolr
mv website_example website
mv /tmp/.pimunit/* plugins/Pimsolr/
chmod -R 777 website/var

# install Pimunit
mkdir plugins/Pimunit
hg clone https://bitbucket.org/timg/pimunit plugins/Pimunit
chmod -R 777 /plugins/Pimunit/var
