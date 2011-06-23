<?

// php configuration
@ini_set("display_errors", "On");
@ini_set("display_startup_errors", "On");

// define own contstants
define('PIMUNIT_ROOT', __DIR__);
define('PIMUNIT_ROOT_PROC', PIMUNIT_ROOT.'/var/tmp/'.getmypid().'/');
define('PIMUNIT_WEBSITE_PATH', PIMUNIT_ROOT_PROC.'/var/tmp/website');
define('PIMCORE_CONFIGURATION_SYSTEM', PIMUNIT_ROOT . '/../../website/var/config/system.xml' );
define("PIMCORE_ASSET_DIRECTORY", PIMUNIT_ROOT_PROC . "/var/assets");
define("PIMCORE_VERSION_DIRECTORY", PIMUNIT_ROOT_PROC . "/var/versions");
define("PIMCORE_WEBDAV_TEMP", PIMUNIT_ROOT_PROC . "/var/webdav");
define("PIMCORE_LOG_DEBUG", PIMUNIT_ROOT_PROC . "/var/log/debug.log");
define("PIMCORE_LOG_MAIL_TEMP", PIMUNIT_ROOT_PROC . "/var/log/mail");
define("PIMCORE_TEMPORARY_DIRECTORY", PIMUNIT_ROOT_PROC . "/var/tmp");
define("PIMCORE_CACHE_DIRECTORY", PIMUNIT_ROOT_PROC . "/var/cache");
define("PIMCORE_CLASS_DIRECTORY", PIMUNIT_ROOT_PROC . "/var/classes");
define("PIMCORE_BACKUP_DIRECTORY", PIMUNIT_ROOT_PROC . "/var/backup");
define("PIMCORE_RECYCLEBIN_DIRECTORY", PIMUNIT_ROOT_PROC . "/var/recyclebin");
define("PIMCORE_SYSTEM_TEMP_DIRECTORY", PIMUNIT_ROOT_PROC . "/var/system");

// pimcore constants
define('PIMCORE_ADMIN', true);

// disable the writing the Cache at the end of the Request
register_shutdown_function(function () {

    Pimcore_Resource_Mysql::get()->deleteMockDb();

    $cleanup = new Pimcore_Test_Cleanup();
    $cleanup->rrmdir(PIMUNIT_ROOT_PROC);
    die();
});

// load pimcore
require_once __DIR__ . '/../../pimcore/config/startup.php';
$pimcore = new Pimcore( );
$pimcore->initConfiguration();
$pimcore->setSystemRequirements();
$pimcore->initAutoloader();
$pimcore->initLogger();
$pimcore->initModules();
$pimcore->initPlugins();

// allow autololoading in pimcore namespace
set_include_path(get_include_path().PATH_SEPARATOR.__DIR__.'/lib');

// Change the Database Driver
$dbClass = Zend_Registry::get("pimcore_config_system")->database;
$reflector = new ReflectionProperty(get_class($dbClass), '_allowModifications');
$reflector->setAccessible(true);
$reflector->setValue($dbClass, true);
Zend_Registry::get("pimcore_config_system")->database->adapter = 'Pimunit';

// load external libs
require_once PIMUNIT_ROOT.'/lib/Yaml/sfYamlParser.php';

