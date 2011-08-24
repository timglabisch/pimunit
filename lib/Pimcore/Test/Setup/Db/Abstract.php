<?php

class Pimcore_Test_Setup_Db_Abstract {

    /**
     * @return mixed|Zend_Db_Adapter_Abstract
     */

    /** @var Pimcore_Test_Icleanup !inject */
    public $cleanup;
    
    protected function getDb() {
        return Pimcore_Resource_Mysql::get();
    }

    public function setUpDatabase() {
        $this->getDb()->initPimcore();
    }

    public function setUpFiles()
    {
        $this->cleanup->cleanUp();
    }
    
}
