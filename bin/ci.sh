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
PIMCORE_INSTALL_TYPE='Plugin'
PIMCORE_GIT_REPOSIOTRY="https://github.com/pimcore/pimcore"
PIMCORE_GIT_REPOSIOTRY_BRANCH="master"
PIMUNIT_GIT_REPOSIOTRY="https://github.com/timglabisch/pimunit"
PIMUNIT_GIT_REPOSIOTRY_BRANCH="master"
PIMUNIT_INSTALL=1;
PIMCORE_DATABASE_DRIVER="Pdo_Mysql"
ENV_PHP_CONFIGURE=0

# make options available in the bash
TEMP=`getopt -o :: --long website,plugin,plugin-name:,mysql-user:,mysql-pass:,mysql-host:,mysql-port:,mysql-database:,mysql-database:,pimcore-git-repository:,pimcore-git-branch:,pimunit-git-repo:,pimunit-git-branch:,pimunit-skip-install,pimcore-database-driver:,travis-ci \
     -n 'example.bash' -- "$@"`

if [ $? != 0 ] ; then echo "Terminating..." >&2 ; exit 1 ; fi

eval set -- "$TEMP"

while true ; do
	case "$1" in
		--website) export PIMCORE_INSTALL_TYPE="Website"; shift 1;;
		--plugin) export PIMCORE_INSTALL_TYPE="Plugin"; shift 1;;
		--plugin-name) export PLUGIN_NAME=$2; shift 2;;
		--mysql-user) export MYSQL_USERNAME=$2; shift 2;;
		--mysql-pass) export MYSQL_PASSWORD=$2; shift 2;;
		--mysql-host) export MYSQL_HOST=$2; shift 2;;
		--mysql-port) export MYSQL_PORT=$2; shift 2;;
		--mysql-database) export MYSQL_DATABASE=$2; shift 2;;
		--pimcore-git-repository) export PIMCORE_GIT_REPOSIOTRY=$2; shift 2;;
		--pimcore-git-branch) export PIMCORE_GIT_REPOSIOTRY_BRANCH=$2; shift 2;;
		--pimunit-git-repo) export PIMUNIT_GIT_REPOSIOTRY=$2; shift 2;;
		--pimunit-git-branch) export PIMUNIT_GIT_REPOSIOTRY_BRANCH=$2; shift 2;;
		--pimunit-skip-install) export PIMUNIT_INSTALL=0; shift 1;;
		--pimcore-database-driver) export PIMCORE_DATABASE_DRIVER=$2; shift 2;;
		--travis-ci) export ENV_PHP_CONFIGURE=1; export MYSQL_USERNAME="root"; shift 1;;
		--) shift; break;;
	esac
done

# real skript starts here x)
if [ $ENV_PHP_CONFIGURE = 1 ]; then
	echo "# configure php"
	echo "# enable short open tags"
	cat `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"` | sed -e "s/short_open_tag = Off/short_open_tag = On/ig" > `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`

	echo "# disable magic quotes"
	cat `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"` | sed -e "s/magic_quotes_gpc = On/magic_quotes_gpc = Off/ig" > `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
	echo "magic_quotes_gpc = Off" > `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
fi

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
mysql --host=$MYSQL_HOST --user=$MYSQL_USERNAME --password=$MYSQL_PASSWORD --port=$MYSQL_PORT -e "drop database if exists $MYSQL_DATABASE;"
mysql --host=$MYSQL_HOST --user=$MYSQL_USERNAME --password=$MYSQL_PASSWORD --port=$MYSQL_PORT -e "create database $MYSQL_DATABASE;"
mysql --host=$MYSQL_HOST --user=$MYSQL_USERNAME --password=$MYSQL_PASSWORD --port=$MYSQL_PORT --force --one-database $MYSQL_DATABASE < pimcore/modules/install/mysql/install.sql

if [ $PIMCORE_INSTALL_TYPE = "Plugin" ]; then
	echo "# install Pimcore Plugin"
	mkdir plugins/$PLUGIN_NAME
	cp -R /tmp/pimcore_plugin/* plugins/$PLUGIN_NAME
fi

if [ $PIMCORE_INSTALL_TYPE = "Website" ]; then
	echo "# install Pimcore Website"
	rm -rf website/*
	cp -R /tmp/pimcore_plugin website
fi

if [ $PIMUNIT_INSTALL = 1 ]; then
	echo "# install Pimunit"
	mkdir plugins/Pimunit
	git clone -b $PIMUNIT_GIT_REPOSIOTRY_BRANCH $PIMUNIT_GIT_REPOSIOTRY plugins/Pimunit
	chmod -R 777 plugins/Pimunit/var
fi

echo "# copy and customize configuration"
cp plugins/Pimunit/bin/fixtures/config/system.xml website/var/config/system.xml
cp plugins/Pimunit/bin/fixtures/config/extensions.xml website/var/config/extensions.xml
sed -i "s/%PIMCORE_DATABASE_DRIVER%/$PIMCORE_DATABASE_DRIVER/g" website/var/config/system.xml
sed -i "s/%MYSQL_HOST%/$MYSQL_HOST/g" website/var/config/system.xml
sed -i "s/%MYSQL_USERNAME%/$MYSQL_USERNAME/g" website/var/config/system.xml
sed -i "s/%MYQL_PASSWORD%/$MYQL_PASSWORD/g" website/var/config/system.xml
sed -i "s/%MYSQL_DATABASE%/$MYSQL_DATABASE/g" website/var/config/system.xml
sed -i "s/%MYSQL_PORT%/$MYSQL_PORT/g" website/var/config/system.xml

echo "# configure file Permissions"
chmod -R 777 website/var
