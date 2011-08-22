<?
use de\any\di;
use de\any\di\binder\parser\xml;

// load dependency injection
require_once __DIR__.'/lib/Any/di.php';
require_once __DIR__.'/lib/Any/DI/binder/parser/xml.php';

$xml = new xml(file_get_contents(__DIR__.'/dependencies.xml'));

$repository = new \de\any\di\binder\repository();
$repository->addBindings($bindings = $xml->getBindings());

$di = new di();
$di->setBinderRepository($repository);

// update include paths
$includePaths = array(
    __DIR__.'/lib',
    __DIR__.'/../../website/var/classes'
);

set_include_path(get_include_path().PATH_SEPARATOR.implode(PATH_SEPARATOR, $includePaths));

require_once __DIR__.'/lib/Pimunit/iStartup.php';
require_once __DIR__.'/lib/Pimunit/Startup/Standard.php';
require_once __DIR__.'/lib/Pimunit/Startup/iConstants.php';
require_once __DIR__.'/lib/Pimunit/Startup/Constants/Standard.php';

define('PIMUNIT_ROOT', __DIR__);

// php configuration
@ini_set("display_errors", "On");
@ini_set("display_startup_errors", "On");

// define Pimunit Root
$di->get('Pimunit_Startup_iConstants')->setPimunitRoot(__DIR__);

$startupConstants = $di->get('Pimunit_Startup_iConstants');

// define own contstants
define('PIMUNIT_ROOT_PROC', $startupConstants->getPimunitProc());
define('PIMUNIT_WEBSITE_PATH', $startupConstants->getPimunitWebsiteDirectory());
define('PIMCORE_CONFIGURATION_SYSTEM', $startupConstants->getConfigurationSystemFile());
define("PIMCORE_ASSET_DIRECTORY", $startupConstants->getAssetDirectory());
define("PIMCORE_VERSION_DIRECTORY", $startupConstants->getVersionDirectory());
define("PIMCORE_WEBDAV_TEMP", $startupConstants->getWebdavTempDirectory());
define("PIMCORE_LOG_DEBUG", $startupConstants->getLogDebugDirectory());
define("PIMCORE_LOG_MAIL_TEMP", $startupConstants->getLogMailTempDirectory());
define("PIMCORE_TEMPORARY_DIRECTORY", $startupConstants->getTempDirectory());
define("PIMCORE_CACHE_DIRECTORY", $startupConstants->getCacheDirectory());
define("PIMCORE_CLASS_DIRECTORY", $startupConstants->getClassDirectory());
define("PIMCORE_BACKUP_DIRECTORY", $startupConstants->getBackupDirectory());
define("PIMCORE_RECYCLEBIN_DIRECTORY", $startupConstants->getRecyclebinDirectory());
define("PIMCORE_SYSTEM_TEMP_DIRECTORY", $startupConstants->getSystemTempDirectory());

// pimcore constants
define('PIMCORE_ADMIN', true);

$di->get('Pimunit_iStartup')->beforeInitPimcore();
$di->get('Pimunit_iStartup')->loadPimcore();


// Change the Database Driver
$dbClass = Zend_Registry::get("pimcore_config_system")->database;
$reflector = new ReflectionProperty(get_class($dbClass), '_allowModifications');
$reflector->setAccessible(true);
$reflector->setValue($dbClass, true);
Zend_Registry::get("pimcore_config_system")->database->adapter = 'Pimunit';

// load external libs
require_once PIMUNIT_ROOT.'/lib/Yaml/sfYamlParser.php';

