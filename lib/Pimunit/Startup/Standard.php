<?php
class Pimunit_Startup_Standard implements Pimunit_iStartup {

    function registerConstants() {

    }

    function beforeInitPimcore() {
        // disable the writing the Cache at the end of the Request
        register_shutdown_function(function () {

            Pimcore_Resource_Mysql::get()->deleteMockDb();

            $cleanup = new Pimcore_Test_Cleanup();
            $cleanup->rrmdir(PIMUNIT_ROOT_PROC);
            die();
        });
    }

    function loadPimcore() {
        // load pimcore
        require_once PIMUNIT_ROOT . '/../../pimcore/config/startup.php';
        $pimcore = new Pimcore( );
        $pimcore->initConfiguration();
        $pimcore->setSystemRequirements();
        $pimcore->initAutoloader();
        $pimcore->initLogger();
        $pimcore->initModules();
        $pimcore->initPlugins();
    }

}
 
