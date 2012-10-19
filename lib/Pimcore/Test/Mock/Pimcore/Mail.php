<?php

class Pimcore_Test_Mock_Pimcore_Mail extends Pimcore_Mail {

    /**
     * @var Pimcore_Mail[]
     */
    static $sendStack;

    static function reset() {
        self::$sendStack = array();
    }

    function send($transport=null) {
        self::$sendStack[] = unserialize(serialize($this));
    }

}