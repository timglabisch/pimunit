<?php
class Pimunit_Startup_Constants_Standard implements Pimunit_Startup_iConstants {
    private $pimunitRoot;
    private $pimunitProc;
    private $pimunitWebsiteDirectory;
    private $ConfigurationSystemFile;
    private $AssetDirectory;
    private $VersionDirectory;
    private $WebdavTempDirectory;
    private $LogDebugDirectory;
    private $LogMailTempDirectory;
    private $TempDirectory;
    private $CacheDirectory;
    private $ClassDirectory;
    private $BackupDirectory;
    private $RecyclebinDirectory;
    private $SystemTempDirectory;

    public function setAssetDirectory($AssetDirectory)
    {
        $this->AssetDirectory = $AssetDirectory;
    }

    public function getAssetDirectory()
    {
        if(!$this->AssetDirectory)
            $this->AssetDirectory = $this->getPimunitProc().'/var/assets';

        return $this->AssetDirectory;
    }

    public function setBackupDirectory($BackupDirectory)
    {
        $this->BackupDirectory = $BackupDirectory;
    }

    public function getBackupDirectory()
    {
        if(!$this->BackupDirectory)
            $this->BackupDirectory = $this->getPimunitProc().'/var/backup';

        return $this->BackupDirectory;
    }

    public function setCacheDirectory($CacheDirectory)
    {
        $this->CacheDirectory = $CacheDirectory;
    }

    public function getCacheDirectory()
    {
        if(!$this->CacheDirectory)
            $this->CacheDirectory = $this->getPimunitProc().'/var/cache';

        return $this->CacheDirectory;
    }

    public function setClassDirectory($ClassDirectory)
    {
        $this->ClassDirectory = $ClassDirectory;
    }

    public function getClassDirectory()
    {
        if(!$this->ClassDirectory)
            $this->ClassDirectory = $this->getPimunitProc().'/var/classes';

        return $this->ClassDirectory;
    }

    public function setConfigurationSystemFile($ConfigurationSystemFile)
    {
        $this->ConfigurationSystemFile = $ConfigurationSystemFile;
    }

    public function getConfigurationSystemFile()
    {
        if(!$this->ConfigurationSystemFile)
            $this->ConfigurationSystemFile = $this->getPimunitRoot().'/../../website/var/config/system.xml';

        return $this->ConfigurationSystemFile;
    }

    public function setLogDebugDirectory($LogDebugDirectory)
    {
        $this->LogDebugDirectory = $LogDebugDirectory;
    }

    public function getLogDebugDirectory()
    {
        if(!$this->LogDebugDirectory)
            $this->LogDebugDirectory = $this->getPimunitProc().'/var/log/debug.log';

        return $this->LogDebugDirectory;
    }

    public function setLogMailTempDirectory($LogMailTempDirectory)
    {
        $this->LogMailTempDirectory = $LogMailTempDirectory;
    }

    public function getLogMailTempDirectory()
    {
        if(!$this->LogMailTempDirectory)
            $this->LogMailTempDirectory = $this->getPimunitProc().'/var/log/mail';
        
        return $this->LogMailTempDirectory;
    }

    public function setRecyclebinDirectory($RecyclebinDirectory)
    {
        $this->RecyclebinDirectory = $RecyclebinDirectory;
    }

    public function getRecyclebinDirectory()
    {
        if(!$this->RecyclebinDirectory)
            $this->RecyclebinDirectory = $this->getPimunitProc().'/var/recyclebin';

        return $this->RecyclebinDirectory;
    }

    public function setSystemTempDirectory($SystemTempDirectory)
    {
        $this->SystemTempDirectory = $SystemTempDirectory;
    }

    public function getSystemTempDirectory()
    {
        if(!$this->SystemTempDirectory)
            $this->SystemTempDirectory = $this->getPimunitProc().'/var/system';

        return $this->SystemTempDirectory;
    }

    public function setTempDirectory($TempDirectory)
    {
        $this->TempDirectory = $TempDirectory;
    }

    public function getTempDirectory()
    {
        if(!$this->TempDirectory)
            $this->TempDirectory = '/var/tmp';

        return $this->TempDirectory;
    }

    public function setVersionDirectory($VersionDirectory)
    {
        $this->VersionDirectory = $VersionDirectory;
    }

    public function getVersionDirectory()
    {
        if(!$this->VersionDirectory)
            $this->VersionDirectory = $this->getPimunitProc().'/var/versions';

        return $this->VersionDirectory;
    }

    public function setWebdavTempDirectory($WebdavTempDirectory)
    {
        $this->WebdavTempDirectory = $WebdavTempDirectory;
    }

    public function getWebdavTempDirectory()
    {
        if(!$this->WebdavTempDirectory)
            $this->WebdavTempDirectory = $this->getPimunitProc().'/var/webdav';

        return $this->WebdavTempDirectory;
    }

    public function setPimunitProc($pimunitProc)
    {
        $this->pimunitProc = $pimunitProc;
    }

    public function getPimunitProc()
    {
        if(!$this->pimunitProc)
            $this->pimunitProc = $this->getPimunitRoot().'/var/tmp/'.getmypid();

        return $this->pimunitProc;
    }

    public function setPimunitRoot($pimunitRoot)
    {
        $this->pimunitRoot = $pimunitRoot;
    }

    public function getPimunitRoot()
    {
        return $this->pimunitRoot;
    }

    public function setPimunitWebsiteDirectory($pimunitWebsiteDirectory)
    {
        $this->pimunitWebsiteDirectory = $pimunitWebsiteDirectory;
    }

    public function getPimunitWebsiteDirectory()
    {
         if(!$this->pimunitWebsiteDirectory)
            $this->pimunitWebsiteDirectory = $this->getPimunitProc().'/var/tmp/website';

        return $this->pimunitWebsiteDirectory;
    }
}
 
