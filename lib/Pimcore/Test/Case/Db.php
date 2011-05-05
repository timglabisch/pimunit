<?
abstract class Pimcore_Test_Case_Db extends Pimcore_Test_Case_Abstract {

    var $dbname = null;

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
    protected function getDb()
    {
        return Pimcore_Resource_Mysql::get();
    }

    public function setUpDatabase()
    {
        // drop the old database
        $initQuery = array(
            'DROP DATABASE '.$this->getDbName(),
            'CREATE DATABASE '.$this->getDbName().' CHARACTER SET utf8',
            'USE '.$this->getDbName(),
            file_get_contents(PIMCORE_PATH . '/modules/install/mysql/install.sql')
        );

        $this->getDb()->exec(implode(';', $initQuery));
    }

    public function setUpFiles()
    {
       // $this->flushIOCache();
        $cleanup = new Pimcore_Test_Cleanup();
        $cleanup->cleanUp();
    }

    public function flushIOCache()
    {
        Pimcore_Model_Cache::write();
    }

    public function setUp()
    {
        parent::setUp();

        // clear database
        $this->setUpDatabase();

        // clear cache
        $this->setUpFiles();
    }

}
