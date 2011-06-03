<?php

abstract class Pimcore_Test_Setup_Db_Abstract {

    public function getDbName()
    {
        if($this->dbname == null)
            $this->setDbName(Zend_Registry::get ( "pimcore_config_system" )->database->params->dbname);

        return $this->dbname;
    }

    public function setDbName($dbname)
    {
        if (substr ( $dbname, strlen ( $dbname ) - 5 ) != '_test')
            throw new Pimcore_Test_Case_Db_Exception( 'the testdatabase must have \'_test\' as suffix!' );

        $this->dbname = $dbname;
    }

    /**
     * @return mixed|Zend_Db_Adapter_Abstract
     */
    protected function getDb() {
        return Pimcore_Resource_Mysql::get();
    }

    public function setUpDatabase()
    {
        // drop the old database
        $initQuery = array(
            'DROP DATABASE IF EXISTS '.$this->getDbName(),
            'CREATE DATABASE '.$this->getDbName().' CHARACTER SET utf8',
            'USE '.$this->getDbName(),
            file_get_contents(PIMCORE_PATH . '/modules/install/mysql/install.sql')
        );

        $this->getDb()->exec(implode(';', $initQuery));

         if(Pimcore_Version::$revision >= 1154) {
            $this->getDb()->exec(file_get_contents(__DIR__.'/Sql/1157.sql'));
        }
    }

    public function setUpFiles()
    {
       // $this->flushIOCache();
        $cleanup = new Pimcore_Test_Cleanup();
        $cleanup->cleanUp();
    }
    
}
