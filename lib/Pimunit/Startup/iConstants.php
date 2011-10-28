<?php
interface Pimunit_Startup_iConstants {
    function getPimunitRoot();
    function getPimunitProc();
    function getPimunitWebsiteDirectory();
    function getConfigurationSystemFile();
    function getAssetDirectory();
    function getVersionDirectory();
    function getWebdavTempDirectory();
    function getLogDebugDirectory();
    function getLogMailTempDirectory();
    function getTempDirectory();
    function getCacheDirectory();
    function getClassDirectory();
    function getBackupDirectory();
    function getRecyclebinDirectory();
    function getSystemTempDirectory();

    function setPimunitRoot($var);
    function setPimunitProc($var);
    function setPimunitWebsiteDirectory($var);
    function setConfigurationSystemFile($var);
    function setAssetDirectory($var);
    function setVersionDirectory($var);
    function setWebdavTempDirectory($var);
    function setLogDebugDirectory($var);
    function setLogMailTempDirectory($var);
    function setTempDirectory($var);
    function setCacheDirectory($var);
    function setClassDirectory($var);
    function setBackupDirectory($var);
    function setRecyclebinDirectory($var);
    function setSystemTempDirectory($var);
}