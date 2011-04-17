<?
// php configuration
@ini_set("display_errors", "On");
@ini_set("display_startup_errors", "On");

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

// add test Namespace
$includePaths = array(get_include_path(), __DIR__ . '/lib');
set_include_path(implode(PATH_SEPARATOR, $includePaths));
$autoloader = Zend_Loader_Autoloader::getInstance();

// set custom view renderer
$pimcoreViewHelper = new Pimcore_Controller_Action_Helper_ViewRenderer ( );
Zend_Controller_Action_HelperBroker::addHelper ( $pimcoreViewHelper );
                
// run plugins
Pimcore_API_Plugin_Broker::getInstance ()->preDispatch ();

