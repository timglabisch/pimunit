<?
class Pimcore_Test_Case_Db extends Pimcore_Test_Case_Abstract {

    var $dbname = null;

    protected function getDbName()
    {
        if($this->dbname == null)
        {
             // check the Database suffix
            $this->dbname = Zend_Registry::get ( "pimcore_config_system" )->database->params->dbname;

            // assert that the database suffix is _test !
            if (substr ( $this->dbname, strlen ( $this->dbname ) - 5 ) != '_test')
               die( 'the testdatabase must have \'_test\' as suffix!' );
        }

        return $this->dbname;
    }

    /**
     * @return mixed|Zend_Db_Adapter_Abstract
     */
    protected function getDb()
    {
        return Pimcore_Resource_Mysql::get();
    }

    public function setUp()
    {
        parent::setUp();

        // drop the old database
        $initQuery = array(
            'DROP DATABASE '.$this->getDbName(),
            'CREATE DATABASE '.$this->getDbName().' CHARACTER SET utf8',
            'USE '.$this->getDbName(),
            file_get_contents(PIMCORE_PATH . '/modules/install/mysql/install.sql')
        );

        $this->getDb()->exec(implode(';', $initQuery));
    }

}
