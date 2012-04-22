<?php

class Pimunit_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql {

    private $originalConfig;
    private $dbConnection;

    /** @var Pimunit_Db_iSqlbuilder !inject */
    public $sqlBuilder;

    /** @return \PDO */
    public function getConnection() {

        if(!$this->dbConnection)
            return parent::getConnection();

        return $this->dbConnection;
    }

    public function setConnection($connection) {
        $this->dbConnection = $connection;
    }

    /**
     * make sure that this function is called as shutdown method
     */
    public function deleteMockDb() {
        $this->verifIsMockDb();
        $this->getConnection()->exec('DROP DATABASE '.$this->_config['dbname']);
    }

    public function verifIsMockDb() {
        if (substr ( $this->_config['dbname'], strlen ( $this->_config['dbname'] ) - 5 ) != '_test')
            throw new Exception('isn\'t Mock Database');
    }

    public function __construct($config) {
        Pimcore_Test_Case::$di->justInject($this);

        $this->setOriginalConfig($config);

        if(!isset($config['dbname']))
            throw new Exception('no Database?');

        $config['dbname'] =  'pimunit_'.str_replace('-','_', $config['dbname'].'_'.getmypid().'_test');

        // create the database
        $db = new PDO('mysql:host='.$config['host'], $config['username'], $config['password']);
        $q = 'CREATE DATABASE '.$config['dbname'].' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;';
        $db->exec($q);
        unset($db);
        
        parent::__construct($config);
        $this->verifIsMockDb();
    }

    function tables2Copy() {
        $origConfig = $this->getOriginalConfig();
        $origDb = $origConfig['dbname'];

        $tables = $this->fetchAll('
            SELECT TABLE_NAME FROM
            INFORMATION_SCHEMA.TABLES
            WHERE
            TABLE_SCHEMA = "'.$origDb.'"
            AND TABLE_TYPE = "BASE TABLE"
            AND
            (
                TABLE_NAME REGEXP \'object\_.+\_[0-9]+$\'
            )
        ');

        foreach($tables as $v) {
            $buffer[] = $v['TABLE_NAME'];
        }

        return $buffer;
    }

    public function initPimcore() {

        /*
         * in version 1.4.5 there is a bug in the install script, so tree_locks isnt dropped
         * by defult
         */

        $sql = 'DROP TABLE IF EXISTS `tree_locks`;';

        $sql .= $this->sqlBuilder->installPimcoreSql();
        $sql .= $this->sqlBuilder->restoreClassesSql($this->getOriginalConfig(), $this->_config['dbname'], $this->tables2Copy());
        $sql = $this->sqlBuilder->removeComments($sql);

        foreach(explode(';', $sql) as $v) {
            $v = trim($v);

            if(!$v)
                continue;
            try {
            $this->query(trim($v.';'));
            } catch(\Exception $e) {
                echo 'Exception on Query: '.$v."\n with message".$e->getMessage()."\n----------------------\n";
                die();
            }
        }

        $this->getConnection()->exec($sql);

        if(Pimcore_Version::$revision >= 1499)
            $this->sqlBuilder->addDefaultTableStructure($this);
    }

    public function setOriginalConfig($originalConfig) {
        $this->originalConfig = $originalConfig;
    }

    public function getOriginalConfig() {
        return $this->originalConfig;
    }

}
