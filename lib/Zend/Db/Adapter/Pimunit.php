<?php

class Zend_Db_Adapter_Pimunit extends Zend_Db_Adapter_Pdo_Mysql {

    private $originalConfig;
    private $dbConnection;

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

        $config['dbname'] = '';
        parent::__construct($config);

        $config['dbname'] =  'pimunit_'.str_replace('-','_', $config['dbname'].'_'.getmypid().'_test');
        $q = 'CREATE DATABASE '.$config['dbname'].' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;';
        $this->getConnection()->exec($q);

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
        $classes = __DIR__.'/../../../../../../website/var/classes';

        // get original db name
        $origConfig = $this->getOriginalConfig();
        $origDb = $origConfig['dbname'];

        $sql = '
            DROP TABLE IF EXISTS `'.$this->_config['dbname'].'`.`classes`;
            CREATE TABLE `'.$this->_config['dbname'].'`.`classes` SELECT * FROM `'.$origDb.'`.`classes`;
        ';

        $iterator = new FilesystemIterator($classes);

        if(!count($iterator))
            return;

        foreach ($iterator as $fileinfo) {
            if($fileinfo->isDir())
                continue;

            $classId = explode('_',basename($fileinfo->getFilename(), '.psf'));
            $classId = $classId[1];

            $sql .= '
                DROP TABLE IF EXISTS `'.$this->_config['dbname'].'`.`object_query_'.$classId.'`;
                CREATE TABLE `'.$this->_config['dbname'].'`.`object_query_'.$classId.'` SELECT * FROM `'.$origDb.'`.`object_query_'.$classId.'` LIMIT 0;

                DROP TABLE IF EXISTS `'.$this->_config['dbname'].'`.`object_store_'.$classId.'`;
                CREATE TABLE `'.$this->_config['dbname'].'`.`object_store_'.$classId.'` SELECT * FROM `'.$origDb.'`.`object_store_'.$classId.'` LIMIT 0;

                DROP TABLE IF EXISTS `'.$this->_config['dbname'].'`.`object_relations_'.$classId.'`;
                CREATE TABLE `'.$this->_config['dbname'].'`.`object_relations_'.$classId.'` SELECT * FROM `'.$origDb.'`.`object_relations_'.$classId.'` LIMIT 0;

                DROP VIEW IF EXISTS `'.$this->_config['dbname'].'`.`object_'.$classId.'`;
                CREATE VIEW `'.$this->_config['dbname'].'`.`object_'.$classId.'` AS SELECT * FROM `'.$origDb.'`.`object_'.$classId.'`;
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
