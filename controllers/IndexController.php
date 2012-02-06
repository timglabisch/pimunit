<?php

class Pimunit_IndexController extends Pimcore_Controller_Action {

    function indexAction() {
        $this->view->foo = 'pimunit!';
    }

}