<?

// php configuration
@ini_set("display_errors", "On");
@ini_set("display_startup_errors", "On");

// define own contstants
define('PIMUNIT_ROOT', __DIR__);
define('PIMUNIT_WEBSITE_PATH', PIMUNIT_ROOT.'/tests/fixtures/website');
define('PIMCORE_CONFIGURATION_SYSTEM', __DIR__ . '/tests/fixtures/website/var/config/system.xml' );
define("PIMCORE_ASSET_DIRECTORY", PIMUNIT_WEBSITE_PATH . "/var/assets");
define("PIMCORE_VERSION_DIRECTORY", PIMUNIT_WEBSITE_PATH . "/var/versions");
define("PIMCORE_WEBDAV_TEMP", PIMUNIT_WEBSITE_PATH . "/var/webdav");
define("PIMCORE_LOG_DEBUG", PIMUNIT_WEBSITE_PATH . "/var/log/debug.log");
define("PIMCORE_LOG_MAIL_TEMP", PIMUNIT_WEBSITE_PATH . "/var/log/mail");
define("PIMCORE_TEMPORARY_DIRECTORY", PIMUNIT_WEBSITE_PATH . "/var/tmp");
define("PIMCORE_CACHE_DIRECTORY", PIMUNIT_WEBSITE_PATH . "/var/cache");
define("PIMCORE_CLASS_DIRECTORY", PIMUNIT_WEBSITE_PATH . "/var/classes");
define("PIMCORE_BACKUP_DIRECTORY", PIMUNIT_WEBSITE_PATH . "/var/backup");
define("PIMCORE_RECYCLEBIN_DIRECTORY", PIMUNIT_WEBSITE_PATH . "/var/recyclebin");
define("PIMCORE_SYSTEM_TEMP_DIRECTORY", PIMUNIT_WEBSITE_PATH . "/var/system");

// pimcore constants
define('PIMCORE_ADMIN', true);

// disable the writing the Cache at the end of the Request
register_shutdown_function(function () {
    die();
});

// load pimcore
require_once __DIR__ . '/../../pimcore/config/startup.php';
$pimcore = new Pimcore( );
$pimcore->initConfiguration();
$pimcore->setSystemRequirements();
$pimcore->initAutoloader();
$pimcore->initLogger();
$pimcore->initPlugins();

// allow autololoading in pimcore namespace
set_include_path(get_include_path().PATH_SEPARATOR.__DIR__.'/lib');

// set timezone
#date_default_timezone_set(Zend_Registry::get("pimcore_config_system")->general->timezone);

// disable cache
//Pimcore_Model_Cache::disable();


