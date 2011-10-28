<?php

class Pimunit_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql {

    private $originalConfig;
    private $dbConnection;

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

        $this->setOriginalConfig($config);

        if(!isset($config['dbname']))
            throw new Exception('no Database?');

        $config['dbname'] =  'pimunit_'.str_replace('-','_', $config['dbname'].'_'.getmypid().'_test');

        // create the database
        $db = new PDO('mysql:host='.$config['host'], $config['username'], $config['password']);
        $q = 'CREATE DATABASE '.$config['dbname'].' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;';
        $db->exec($q);
        $this->rootdb = $db;

        parent::__construct($config);
        $this->verifIsMockDb();
    }

    public function initPimcore() {

        $sql = '';

        $sql .= $this->installPimcoreSql();
        $sql .= $this->restoreClassesSql();

        $this->getConnection()->exec($sql);
    }

    public function installPimcoreSql() {

        $sql = '';

        // drop the old database
        $initQuery = array(
            'DROP DATABASE IF EXISTS '.$this->_config['dbname'],
            'CREATE DATABASE '.$this->_config['dbname'].' CHARACTER SET utf8',
            'USE '.$this->_config['dbname'],
            file_get_contents(PIMCORE_PATH . '/modules/install/mysql/install.sql')
        );

        $sql .= implode(';', $initQuery);

         if(Pimcore_Version::$revision >= 1154) {
           $sql .= file_get_contents(__DIR__.'/Sql/1157.sql');
        }

        return $sql;
    }

    public function restoreClassesSql() {
        $sql = '';

        // add custom classes
        $classes = __DIR__.'/../../../../../../../website/var/classes';

        // get original db name
        $origConfig = $this->getOriginalConfig();
        $origDb = $origConfig['dbname'];

        $sql = '
            DROP TABLE IF EXISTS `'.$this->_config['dbname'].'`.`classes`;
            CREATE TABLE `'.$this->_config['dbname'].'`.`classes` SELECT * FROM `'.$origDb.'`.`classes`;
        ';

        // insert objects
        $tables = $this->rootdb->prepare('
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
        $tables->execute();

        $tables = $tables->fetchAll(PDO::FETCH_ASSOC);

        if(count($tables))
            foreach ($tables as $table) {

                $sql .= '
                    DROP TABLE IF EXISTS `'.$this->_config['dbname'].'`.`'.$table['TABLE_NAME'].'`;
                    CREATE TABLE `'.$this->_config['dbname'].'`.`'.$table['TABLE_NAME'].'` SELECT * FROM `'.$origDb.'`.`'.$table['TABLE_NAME'].'`;
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
