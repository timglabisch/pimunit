<?php

class Pimcore_Test_Cleanup {

    public function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
         reset($objects);
         rmdir($dir);
       }
    }

    public function cleanUp()
    {

        if(is_dir(PIMUNIT_ROOT_PROC))
            $this->rrmdir(PIMUNIT_ROOT_PROC);
        
        $dirs = array(
                            PIMUNIT_ROOT_PROC,
                            PIMCORE_ASSET_DIRECTORY,
                            PIMCORE_VERSION_DIRECTORY,
                            PIMCORE_VERSION_DIRECTORY.'/asset',
                            PIMCORE_VERSION_DIRECTORY.'/document',
                            PIMCORE_VERSION_DIRECTORY.'/object',
                            PIMCORE_WEBDAV_TEMP,
                            PIMCORE_LOG_DEBUG,
                            PIMCORE_LOG_MAIL_TEMP,
                            PIMCORE_TEMPORARY_DIRECTORY,
                            PIMCORE_CACHE_DIRECTORY,
                            PIMCORE_CLASS_DIRECTORY,
                            PIMCORE_CLASS_DIRECTORY.'/Object',
                            PIMCORE_CLASS_DIRECTORY.'/fieldcollections',
                            PIMCORE_BACKUP_DIRECTORY,
                            PIMCORE_RECYCLEBIN_DIRECTORY,
                            PIMCORE_SYSTEM_TEMP_DIRECTORY,
                            PIMCORE_SYSTEM_TEMP_DIRECTORY.'/update'
                       );

        foreach($dirs as $dir)
            mkdir($dir, 0777, true);
       
    }
}
