<?php

class Pimunit_IndexControllerTest extends Pimcore_Test_Case_Controller {

    function testIndexAction() {
        $resp = $this->dispatch('/plugin/Pimunit/index/index');
        $this->assertEquals($resp->getResponse()->getBody(), 'Hallo Welt!');
        $this->assertEquals($resp->getController()->view->foo, 'pimunit!');
    }

    function testIndex2Action() {

        $request = new Zend_Controller_Request_HttpTestCase();
        $request->setRequestUri('/plugin/Pimunit/index/index');

        $resp = $this->dispatch($request);
        $this->assertEquals($resp->getResponse()->getBody(), 'Hallo Welt!');
        $this->assertEquals($resp->getController()->view->foo, 'pimunit!');
    }

}