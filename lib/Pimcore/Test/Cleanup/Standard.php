<?php

class Pimcore_Test_Cleanup_Standard implements Pimcore_Test_Icleanup {

    /** @var Pimunit_Startup_iConstants !inject */
    public $config;

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

    function rcopy($src, $dst) {
        if (file_exists($dst))
            $this->rrmdir($dst);

        if (is_dir($src)) {
            mkdir($dst);
            $files = scandir($src);
            foreach ($files as $file)
                if ($file != "." && $file != "..")
                    $this->rcopy("$src/$file", "$dst/$file");
        }
        else
            if (file_exists($src))
                copy($src, $dst);
    }

    public function cleanUp()
    {

        if(is_dir($this->config->getPimunitProc()))
            $this->rrmdir($this->config->getPimunitProc());
        
        $dirs = array(
            $this->config->getPimunitProc(),
            $this->config->getAssetDirectory(),
            $this->config->getVersionDirectory(),
            $this->config->getVersionDirectory().'/asset',
            $this->config->getVersionDirectory().'/document',
            $this->config->getVersionDirectory().'/object',
            $this->config->getWebdavTempDirectory(),
            $this->config->getLogDebugDirectory(),
            $this->config->getLogMailTempDirectory(),
          //  PIMCORE_TEMPORARY_DIRECTORY,
            $this->config->getCacheDirectory(),
            $this->config->getClassDirectory(),
            $this->config->getBackupDirectory(),
            $this->config->getRecyclebinDirectory(),
            $this->config->getSystemTempDirectory(),
            $this->config->getSystemTempDirectory().'/update',
            $this->config->getPimunitProc() . '/var/config/'
       );


        foreach($dirs as $dir)
            mkdir($dir, 0777, true);

        // copy field definitions
        $this->rcopy($this->config->getPimunitRoot() . '/../../website/var/classes', $this->config->getClassDirectory());
    }
}
