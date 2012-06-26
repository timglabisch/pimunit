<?php
interface Pimunit_Db_iSqlbuilder {
    public function installPimcoreSql();
    public function restoreClassesSql($origConfig, $newDb, array $tables = array());
    public function removeComments($sql);
    public function addDefaultTableStructure($db);
}

