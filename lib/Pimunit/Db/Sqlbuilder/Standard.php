<?php
class Pimunit_Db_Sqlbuilder_Standard implements Pimunit_Db_iSqlbuilder  {

    public function installPimcoreSql() {
        $initQuery = array(
           file_get_contents(PIMCORE_PATH . '/modules/install/mysql/install.sql')
        );

        $sql = implode(';', $initQuery);

         if(Pimcore_Version::$revision >= 1154) {
           $sql .= file_get_contents(__DIR__.'/1157.sql');
        }

        return $sql;
    }

    public function restoreClassesSql($origConfig, $newDb, $tables) {
      
        $origDb = $origConfig['dbname'];

        $sql = '
            DROP TABLE IF EXISTS `classes`;
            CREATE TABLE `classes` (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY) SELECT * FROM `'.$origDb.'`.`classes`;
        ';

        if(count($tables))
            foreach ($tables as $table) {

                $sql .= '
                    DROP TABLE IF EXISTS `'.$table.'`;
                    CREATE TABLE `'.$newDb.'`.`'.$table.'` (oo_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY) SELECT * FROM `'.$origDb.'`.`'.$table.'`;
                    TRUNCATE  `'.$table.'`;
                ';
            }

        return $sql;
    }

    public function removeComments($sql) {
        return preg_replace("/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/","", $sql);
    }

}