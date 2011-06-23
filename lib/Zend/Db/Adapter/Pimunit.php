<?php

class Zend_Db_Adapter_Pimunit extends Zend_Db_Adapter_Pdo_Mysql {

    /**
     * make sure that this function is called as shutdown method
     */
    public function deleteMockDb() {
        $this->exec('DROP DATABASE '.$this->_config['dbname']);
    }

    public function __construct($config) {

        if(!isset($config['dbname']))
            throw new Exception('no Database?');

        $config['dbname'] =  str_replace('-','_', $config['dbname'].'_'.getmypid().'_test');

        $db = new PDO('mysql:host='.$config['host'], $config['username'], $config['password']);
        $q = 'CREATE DATABASE '.$config['dbname'].' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;';

        $db->exec($q);

        unset($db);
        parent::__construct($config);
    }

}
