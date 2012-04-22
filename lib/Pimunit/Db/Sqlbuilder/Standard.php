<?php
class Pimunit_Db_Sqlbuilder_Standard implements Pimunit_Db_iSqlbuilder  {

    public function installPimcoreSql() {
        $initQuery = array(
           file_get_contents(PIMCORE_PATH . '/modules/install/mysql/install.sql')
        );

        $sql = implode(';', $initQuery);

         if(Pimcore_Version::$revision >= 1154 && Pimcore_Version::$revision <= 1499) {
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

    public function addDefaultTableStructure($db) {
        // insert data into database
        $db->insert("assets", array(
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
        $db->insert("documents", array(
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
        $db->insert("documents_page", array(
            "id" => 1,
            "controller" => "",
            "action" => "",
            "template" => "",
            "title" => "",
            "description" => "",
            "keywords" => ""
        ));
        $db->insert("objects", array(
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


        $db->insert("users", array(
            "parentId" => 0,
            "name" => "system",
            "admin" => 1,
            "active" => 1
        ));
        $db->update("users",array("id" => 0), $db->quoteInto("name = ?", "system"));


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
            $db->insert("users_permission_definitions", $up);
        }
    }

    public function removeComments($sql) {
        return preg_replace("/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/","", $sql);
    }

}