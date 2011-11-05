<?php
interface Pimunit_Db_iSqlbuilder {
    public function installPimcoreSql();
    public function restoreClassesSql($origConfig, $newDb, $tables);
    public function removeComments($sql);
}
