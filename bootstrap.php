<?
// php configuration
@ini_set("display_errors", "On");
@ini_set("display_startup_errors", "On");

// define own contstants
define('PIMUNIT_ROOT', __DIR__);

// pimcore constants
define('PIMCORE_CONFIGURATION_SYSTEM', __DIR__ . '/fixtures/website/var/config/system.xml' );
define('PIMCORE_ADMIN', true);

// load pimcore
require_once __DIR__ . '/../pimcore/config/startup.php';
$pimcore = new Pimcore( );
$pimcore->initConfiguration();
$pimcore->setSystemRequirements();
$pimcore->initAutoloader();
$pimcore->initLogger();
$pimcore->initPlugins();

// allow autololoading in pimcore namespace
set_include_path(get_include_path().PATH_SEPARATOR.__DIR__.'/lib');

// set custom view renderer
$pimcoreViewHelper = new Pimcore_Controller_Action_Helper_ViewRenderer ( );
Zend_Controller_Action_HelperBroker::addHelper ( $pimcoreViewHelper );
                
// run plugins
Pimcore_API_Plugin_Broker::getInstance ()->preDispatch ();

