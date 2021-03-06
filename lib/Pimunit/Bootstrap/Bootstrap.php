<?php
class Pimunit_Bootstrap_Bootstrap implements Pimunit_iBootstrap {

    /** @var \de\any\iDi !inject */
    public $di;

    /** @var Pimunit_Startup_iConstants !inject */
    public $config;
    public $backupIncludePath;

    public function run($rootDir) {
        $this->config->setPimunitRoot($rootDir);
        $this->configurePhp();
        $this->includeStartupFiles();
        $this->definePimcoreConstants();        
        $this->initShutdownFunction();
        $this->initPimcore();
        $this->changeDatabaseDriver();
        $this->initExternalLibs();
    }

    protected function initExternalLibs() {
        require_once $this->config->getPimunitRoot().'/lib/Yaml/sfYamlParser.php';
    }

    protected function initShutdownFunction() {
        $di = $this->di;
        register_shutdown_function(function () use ($di) {
            Pimcore_Resource_Mysql::getConnection()->deleteMockDb();
            $di->get('Pimcore_Test_Icleanup')->rrmdir($di->get('Pimunit_Startup_iConstants')->getPimunitProc());
            die();
        });
    }

    protected function includeStartupFiles() {
        $includePaths = array(
            $this->config->getPimunitRoot().'/lib',
            $this->config->getPimunitRoot().'/../../website/var/classes'
        );

        set_include_path(get_include_path().PATH_SEPARATOR.implode(PATH_SEPARATOR, $includePaths));
    }

    protected function configurePhp() {
        @ini_set("display_errors", "On");
        @ini_set("display_startup_errors", "On");
    }

    protected function definePimcoreConstants() {
        define('PIMUNIT_ROOT', $this->config->getPimunitRoot());
        define('PIMUNIT_ROOT_PROC', $this->config->getPimunitProc());
        define('PIMUNIT_WEBSITE_PATH', $this->config->getPimunitWebsiteDirectory());
        define('PIMCORE_CONFIGURATION_SYSTEM', $this->config->getConfigurationSystemFile());
        define("PIMCORE_ASSET_DIRECTORY", $this->config->getAssetDirectory());
        define("PIMCORE_VERSION_DIRECTORY", $this->config->getVersionDirectory());
        define("PIMCORE_WEBDAV_TEMP", $this->config->getWebdavTempDirectory());
        define("PIMCORE_LOG_DEBUG", $this->config->getLogDebugDirectory());
        define("PIMCORE_LOG_MAIL_TEMP", $this->config->getLogMailTempDirectory());
        define("PIMCORE_TEMPORARY_DIRECTORY", $this->config->getTempDirectory());
        define("PIMCORE_CACHE_DIRECTORY", $this->config->getCacheDirectory());
        define("PIMCORE_CLASS_DIRECTORY", $this->config->getClassDirectory());
        define("PIMCORE_BACKUP_DIRECTORY", $this->config->getBackupDirectory());
        define("PIMCORE_RECYCLEBIN_DIRECTORY", $this->config->getRecyclebinDirectory());
        define("PIMCORE_SYSTEM_TEMP_DIRECTORY", $this->config->getSystemTempDirectory());
        define('PIMCORE_ADMIN', true);
    }

    protected function backupIncludePath() {
        $this->backupIncludePath = get_include_path();
    }

    public function restoreDeletedInIncludePath() {
        if(!$this->backupIncludePath)
            return false;

        set_include_path(implode(PATH_SEPARATOR, array_merge(explode(PATH_SEPARATOR, $this->backupIncludePath), explode(PATH_SEPARATOR, get_include_path()))));
    }

    protected function initPimcore() {
        $this->backupIncludePath();
        require_once $this->config->getPimunitRoot() . '/../../pimcore/config/startup.php';
        $this->restoreDeletedInIncludePath();

        $pimcore = new Pimcore( );
        $pimcore->initConfiguration();
        $pimcore->setSystemRequirements();
        $pimcore->initAutoloader();

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);

        $pimcore->initLogger();
        $pimcore->initModules();
        $pimcore->initPlugins();
    }

    protected function changeDatabaseDriver() {
        $dbClass = Zend_Registry::get("pimcore_config_system")->database->params;
        $reflector = new ReflectionProperty(get_class($dbClass), '_allowModifications');
        $reflector->setAccessible(true);
        $reflector->setValue($dbClass, true);

        Zend_Registry::get("pimcore_config_system")->database->params->adapterNamespace = 'Pimunit_Db_Adapter_Standard';
    }
}
