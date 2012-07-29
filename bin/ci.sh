
# commandline args
usage()
{
cat << EOF
usage: $0 options

This script is for automating Pimcore Tests.

OPTIONS:
	--website		enable flag if current root is the website.
				website is enabled by default. 
	--plugin		enable this flag if current root is a Plugin.
	--plugin-name		sets the name of the Plugin.
				default is Plugin.
	--mysql-user		sets the mysql user.
				default is ci.
	--mysql-pass		sets the mysql password.
				default is an empty password.
	--mysql-host		sets the mysql host.
				default is 127.0.0.1.
	--mysql-port		sets the mysql port.
				default is 3306.
	--mysql-database	sets the mysql database.
				default is CI_Pimcore.
	--pimcore-git-repo	pimcore is cloned from this repo.
				default is https://github.com/pimcore/pimcore
	--pimcore-git-branch	sets the pimcore branch.
				default is master.
	--pimunit-git-repo	pimunit is cloned from this repo.
				default is https://github.com/timglabisch/pimunit
	--pimunit-git-branch	sets the pimunit branch.
				default is master.
EOF
}

if [ $? != 0 ]
then
	usage
	exit 1
fi

# define defaults
MYSQL_USERNAME='ci'
MYSQL_PASSWORD=''
MYSQL_HOST='127.0.0.1'
MYSQL_PORT='3306'
MYSQL_DATABASE='CI_Pimcore'
PLUGIN_NAME='Plugin'
PIMCORE_INSTALL_TYPE='Website'
PIMCORE_GIT_REPOSIOTRY="https://github.com/pimcore/pimcore"
PIMCORE_GIT_REPOSIOTRY_BRANCH="master"
PIMUNIT_GIT_REPOSIOTRY="https://github.com/timglabisch/pimunit"
PIMUNIT_GIT_REPOSIOTRY_BRANCH="master"
PIMUNIT_INSTALL=1;
PIMCORE_DATABASE_DRIVER="Pdo_Mysql"
ENV_PHP_CONFIGURE=0

while true ; do
	case "$1" in
		--website) set PIMCORE_INSTALL_TYPE="Website"; shift 1;;
		--plugin) set PIMCORE_INSTALL_TYPE="Plugin"; shift 1;;
		--plugin-name) set PLUGIN_NAME=$2; shift 2;;
		--mysql-user) set MYSQL_USERNAME=$2; shift 2;;
		--mysql-pass) set MYSQL_PASSWORD=$2; shift 2;;
		--mysql-host) set MYSQL_HOST=$2; shift 2;;
		--mysql-port) set MYSQL_PORT=$2; shift 2;;
		--mysql-database) set MYSQL_DATABASE=$2; shift 2;;
		--pimcore-git-repo) set PIMCORE_GIT_REPOSIOTRY=$2; shift 2;;
		--pimcore-git-branch) set PIMCORE_GIT_REPOSIOTRY_BRANCH=$2; shift 2;;
		--pimunit-git-repo) set PIMUNIT_GIT_REPOSIOTRY=$2; shift 2;;
		--pimunit-git-branch) set PIMUNIT_GIT_REPOSIOTRY_BRANCH=$2; shift 2;;
		--pimunit-skip-install) set PIMUNIT_INSTALL=0; shift 1;;
		--pimcore-database-driver) set PIMCORE_DATABASE_DRIVER=$2; shift 2;;
		--travis-ci) set ENV_PHP_CONFIGURE=1; set MYSQL_USERNAME="root"  shift 1;;
		--) shift; break;;
		*) shift; break;;
	esac
done

echo "# install pimcore and handel current directory as Plugin/Website Directory and move everything to an temporary Directory"
if [ -d /tmp/pimcore ]; then
	rm -rf /tmp/pimcore
fi
if [ -d /tmp/pimcore_plugin ]; then
	rm -rf /tmp/pimcore_plugin
fi
mkdir /tmp/pimcore
mkdir /tmp/pimcore_plugin
cp -R * /tmp/pimcore_plugin
rm -rf *

echo "# install pimcore"
git clone -b $PIMCORE_GIT_REPOSIOTRY_BRANCH $PIMCORE_GIT_REPOSIOTRY /tmp/pimcore
cp -R /tmp/pimcore/pimcore pimcore
cp -R /tmp/pimcore/website_example website
cp /tmp/pimcore/index.php index.php
mkdir plugins

echo "# db"
mysql --host=$MYSQL_HOST --user=$MYSQL_USER --password=$MYSQL_PASSWORD --port=$MYSQL_PORT -e "drop database if exists $MYSQL_DATABASE;"
mysql --host=$MYSQL_HOST --user=$MYSQL_USER --password=$MYSQL_PASSWORD --port=$MYSQL_PORT -e "create database $MYSQL_DATABASE;"
mysql --host=$MYSQL_HOST --user=$MYSQL_USER --password=$MYSQL_PASSWORD --port=$MYSQL_PORT --force --one-database $MYSQL_DATABASE < pimcore/modules/install/mysql/install.sql

if [ $PIMCORE_INSTALL_TYPE = "Plugin" ]; then
	echo "# install Pimcore Plugin"
	mkdir plugins/$PLUGIN_NAME
	cp -R /tmp/pimcore_plugin plugins/$PLUGIN_NAME
fi

if [ $PIMCORE_INSTALL_TYPE = "Website" ]; then
	echo "# install Pimcore Website"
	rm -rf website/*
	cp -R /tmp/pimcore_plugin website/
fi

echo "# copy configurations"
cp plugins/Pimunit/bin/fixtures/config/system.xml website/var/config/system.xml
cp plugins/Pimunit/bin/fixtures/config/extensions.xml website/var/config/extensions.xml

echo "# customize configuration"
sed -i "s/%PIMCORE_DATABASE_DRIVER%/$PIMCORE_DATABASE_DRIVER/g" website/var/config/system.xml
sed -i "s/%MYSQL_HOST%/$MYSQL_HOST/g" website/var/config/system.xml
sed -i "s/%MYSQL_USERNAME%/$MYSQL_USERNAME/g" website/var/config/system.xml
sed -i "s/%MYQL_PASSWORD%/$MYQL_PASSWORD/g" website/var/config/system.xml
sed -i "s/%MYSQL_DATABASE%/$MYSQL_DATABASE/g" website/var/config/system.xml
sed -i "s/%MYSQL_PORT%/$MYSQL_PORT/g" website/var/config/system.xml

echo "# configure file Permissions"
chmod -R 777 website/var

if [ $ENV_PHP_CONFIGURE = 1 ]; then
	echo "# configure php"
	echo "# enable short open tags"
	cat `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"` | sed -e "s/short_open_tag = Off/short_open_tag = On/ig" > `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`

	echo "# disable magic quotes"
	cat `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"` | sed -e "s/magic_quotes_gpc = On/magic_quotes_gpc = Off/ig" > `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
	echo "magic_quotes_gpc = Off" > `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
fi

if [ $PIMUNIT_INSTALL = 1 ]; then
	echo "# install Pimunit"
	mkdir plugins/Pimunit
	git clone -b $PIMUNIT_GIT_REPOSIOTRY_BRANCH $PIMUNIT_GIT_REPOSIOTRY plugins/Pimunit
	chmod -R 777 plugins/Pimunit/var
fi
