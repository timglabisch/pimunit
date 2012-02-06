<?php

require_once __DIR__.'/../../../controllers/IndexController.php';

class Pimunit_IndexControllerTest extends Pimcore_Test_Case_Controller {

    function testIndexAction() {
        $resp = $this->dispatch('/plugin/Pimunit/index/index');
        $this->assertEquals($resp->getResponse()->getBody(), 'Hallo Welt!');
        $this->assertEquals($resp->getController()->view->foo, 'pimunit!');
    }

    function testIndex2Action() {
        $resp = $this->dispatch('/plugin/Pimunit/index/index');
        $this->assertEquals($resp->getResponse()->getBody(), 'Hallo Welt!');
        $this->assertEquals($resp->getController()->view->foo, 'pimunit!');
    }

}