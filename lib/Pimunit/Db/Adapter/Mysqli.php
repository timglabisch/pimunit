<?php

class Pimunit_Db_Adapter_Mysqli extends Zend_Db_Adapter_Mysqli {

    private $originalConfig;
    private $dbConnection;

    /** @return \Mysqli */
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
        $this->query('DROP DATABASE '.$this->_config['dbname']);
    }

    public function verifIsMockDb() {
        if (substr ( $this->_config['dbname'], strlen ( $this->_config['dbname'] ) - 5 ) != '_test')
            throw new Exception('isn\'t Mock Database');
    }

    public function __construct($config) {

        $this->setOriginalConfig($config);

        if(!isset($config['dbname']))
            throw new Exception('no Database?');

        $config['dbname'] =  'pimunit_'.str_replace('-','_', $config['dbname'].'_'.getmypid().'_test');

        // create the database
        $db = new mysqli($config['host'], $config['username'], $config['password']);
        $q = 'CREATE DATABASE '.$config['dbname'].' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;';
        $db->query($q);
        unset($db);

        parent::__construct($config);
        $this->verifIsMockDb();
    }

    public function initPimcore() {

        $sql = '';

        $sql .= $this->installPimcoreSql();
        $sql .= $this->restoreClassesSql();

        $sql = preg_replace("/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/","",$sql);

        foreach(explode(';', $sql) as $v) {
            $v = trim($v);
            
            if(!$v)
                continue;

            $this->query(trim($v.';'));
        }
    }

    public function installPimcoreSql() {

        $sql = '';

        // drop the old database
        $initQuery = array(
            file_get_contents(PIMCORE_PATH . '/modules/install/mysql/install.sql')
        );

        $sql .= implode(';', $initQuery);

         if(Pimcore_Version::$revision >= 1154) {
           $sql .= file_get_contents(__DIR__.'/Sql/1157.sql');
        }

        // remove comments in SQL script
        $sql = preg_replace("/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/","", $sql);

        return $sql;
    }

    public function restoreClassesSql() {
        $sql = '';

        // get original db name
        $origConfig = $this->getOriginalConfig();
        $origDb = $origConfig['dbname'];

        $sql = '
            DROP TABLE IF EXISTS `classes`;
            CREATE TABLE `classes` (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY) SELECT * FROM `'.$origDb.'`.`classes`;
        ';

        // insert objects
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

        if(count($tables))
            foreach ($tables as $table) {

                $sql .= '
                    DROP TABLE IF EXISTS `'.$table['TABLE_NAME'].'`;
                    CREATE TABLE `'.$table['TABLE_NAME'].'` (oo_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY) SELECT * FROM `'.$origDb.'`.`'.$table['TABLE_NAME'].'`;
                    TRUNCATE  `'.$table['TABLE_NAME'].'`;
                ';
            }

        return $sql;
    }

    public function setOriginalConfig($originalConfig)
    {
        $this->originalConfig = $originalConfig;
    }

    public function getOriginalConfig()
    {
        return $this->originalConfig;
    }

}
