
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
MYSQL_USER='ci'
MYSQL_PW=''
MYSQL_HOST='127.0.0.1'
MYSQL_PORT='3306'
MYSQL_DATABASE='CI_Pimcore'
PLUGIN_NAME='Plugin'
PIMCORE_INSTALL_TYPE='Website'
PIMCORE_GIT_REPOSIOTRY="https://github.com/pimcore/pimcore"
PIMCORE_GIT_REPOSIOTRY_BRANCH="master"
PIMUNIT_GIT_REPOSIOTRY="https://github.com/timglabisch/pimunit"
PIMUNIT_GIT_REPOSIOTRY_BRANCH="master"

while true ; do
	case "$1" in
		--website) set PIMCORE_INSTALL_TYPE="Website"; shift 1;;
		--plugin) set PIMCORE_INSTALL_TYPE="Plugin"; shift 1;;
		--plugin-name) set PLUGIN_NAME=$2; shift 2;;
		--mysql-user) set MYSQL_USER=$2; shift 2;;
		--mysql-pass) set MYSQL_PW=$2; shift 2;;
		--mysql-host) set MYSQL_HOST=$2; shift 2;;
		--mysql-port) set MYSQL_PORT=$2; shift 2;;
		--mysql-database) set MYSQL_DATABASE=$2; shift 2;;
		--pimcore-git-repo) set PIMCORE_GIT_REPOSIOTRY=$2; shift 2;;
		--pimcore-git-branch) set PIMCORE_GIT_REPOSIOTRY_BRANCH=$2; shift 2;;
		--pimunit-git-repo) set PIMUNIT_GIT_REPOSIOTRY=$2; shift 2;;
		--pimunit-git-branch) set PIMUNIT_GIT_REPOSIOTRY_BRANCH=$2; shift 2;;
		--) shift; break;;
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
git clone $PIMCORE_GIT_REPOSIOTRY /tmp/pimcore
git checkout -b $PIMCORE_GIT_REPOSIOTRY_BRANCH /tmp/pimcore
cp -R /tmp/pimcore/pimcore pimcore
cp -R /tmp/pimcore/website_example website
cp /tmp/pimcore/index.php index.php
mkdir plugins

echo "# install Pimunit"
mkdir plugins/Pimunit
git clone $PIMUNIT_GIT_REPOSIOTRY plugins/Pimunit
git checkout -b $PIMUNIT_GIT_REPOSIOTRY_BRANCH plugins/Pimunit

echo "# db"
mysql -e "drop database if exists $MYSQL_DATABASE;"
mysql -e "create database $MYSQL_DATABASE;"
mysql --force --one-database $MYSQL_DATABASE < pimcore/modules/install/mysql/install.sql

if [ PIMCORE_INSTALL_TYPE = "Plugin" ]; then
	echo "# install Pimcore Plugin"
	mkdir plugins/$PLUGIN_NAME
	cp -R /tmp/pimcore_plugin plugins/$PLUGIN_NAME
fi

if [ PIMCORE_INSTALL_TYPE = "Website" ]; then
	echo "# install Pimcore Website"
	rm -rf website/*
	cp -R /tmp/pimcore_plugin website/
fi

echo "# copy configurations"
cp plugins/Pimunit/bin/travis-ci/config/system.xml website/var/config/system.xml
cp plugins/Pimunit/bin/travis-ci/config/extensions.xml website/var/config/extensions.xml

echo "# configure file Permissions"
chmod -R 777 website/var
chmod -R 777 plugins/Pimunit/var
