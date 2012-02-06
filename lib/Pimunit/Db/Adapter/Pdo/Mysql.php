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
        require_once __DIR__.'/../../iSqlbuilder.php';
        require_once __DIR__.'/../../Sqlbuilder/Standard.php';

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
        $sql = $this->sqlBuilder->installPimcoreSql();
        $sql .= $this->sqlBuilder->restoreClassesSql($this->getOriginalConfig(), $this->_config['dbname'], $this->tables2Copy());
        $sql = $this->sqlBuilder->removeComments($sql);
    
        $this->getConnection()->exec($sql);

        if(Pimcore_Version::$revision >= 1499)
            $this->addDefaultTableStructure();
    }

    public function addDefaultTableStructure() {
        // insert data into database
        $this->insert("assets", array(
            "id" => 1,
            "parentId" => 0,
            "type" => "folder",
            "filename" => "",
            "path" => "/",
            "creationDate" => time(),
            "modificationDate" => time(),
            "userOwner" => 1,
            "userModification" => 1
        ));

        $this->insert("documents", array(
            "id" => 1,
            "parentId" => 0,
            "type" => "page",
            "key" => "",
            "path" => "/",
            "index" => 999999,
            "published" => 1,
            "creationDate" => time(),
            "modificationDate" => time(),
            "userOwner" => 1,
            "userModification" => 1
        ));

        $this->insert("documents_page", array(
            "id" => 1,
            "controller" => "",
            "action" => "",
            "template" => "",
            "title" => "",
            "description" => "",
            "keywords" => ""
        ));

        $this->insert("objects", array(
            "o_id" => 1,
            "o_parentId" => 0,
            "o_type" => "folder",
            "o_key" => "",
            "o_path" => "/",
            "o_index" => 999999,
            "o_published" => 1,
            "o_creationDate" => time(),
            "o_modificationDate" => time(),
            "o_userOwner" => 1,
            "o_userModification" => 1
        ));

        $this->insert("users", array(
            "parentId" => 0,
            "name" => "system",
            "admin" => 1,
            "active" => 1
        ));

        $this->update("users",array("id" => 0), $this->quoteInto("name = ?", "system"));


        $userPermissions = array(
            array("key" => "assets"),
            array("key" => "classes"),
            array("key" => "clear_cache"),
            array("key" => "clear_temp_files"),
            array("key" => "document_types"),
            array("key" => "documents"),
            array("key" => "objects"),
            array("key" => "plugins"),
            array("key" => "predefined_properties"),
            array("key" => "routes"),
            array("key" => "seemode"),
            array("key" => "system_settings"),
            array("key" => "thumbnails"),
            array("key" => "translations"),
            array("key" => "redirects"),
            array("key" => "glossary" ),
            array("key" => "reports")
        );

        foreach ($userPermissions as $up) {
            $this->insert("users_permission_definitions", $up);
        }
    }

    public function setOriginalConfig($originalConfig) {
        $this->originalConfig = $originalConfig;
    }

    public function getOriginalConfig() {
        return $this->originalConfig;
    }

}
