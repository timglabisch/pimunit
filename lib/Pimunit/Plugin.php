<?php 
class Pimunit_Plugin extends Pimcore_API_Plugin_Abstract implements Pimcore_API_Plugin_Interface {


    public static function install() {

        if (self::isInstalled()) {
            $statusMessage = "Pimunit Plugin successfully installed.";
        } else {
            $statusMessage = "Pimunit Plugin could not be installed";
        }
        return $statusMessage;

    }

    public static function uninstall() {

        if (!self::isInstalled()) {
            $statusMessage = "Pimunit Plugin successfully uninstalled.";
        } else {
            $statusMessage = "Pimunit Plugin could not be uninstalled";
        }
        return $statusMessage;

    }

    public static function isInstalled() {
       return true;
    }

    public static function getTranslationFileDirectory() {
        return PIMCORE_PLUGINS_PATH . "/Pimunit/texts";
    }

    /**
     *
     * @param string $language
     * @return string path to the translation file relative to plugin direcory
     */
    public static function getTranslationFile($language) {
        if (is_file(PIMCORE_PLUGINS_PATH . "/Pimunit/texts/" . $language . ".csv")) {
            return "/Pimunit/texts/" . $language . ".csv";
        } else {
            return "/Pimunit/texts/en.csv";
        }

    }

}