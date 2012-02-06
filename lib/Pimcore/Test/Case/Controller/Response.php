<?php

class Pimcore_Test_Case_Controller_Response {

    /**
     * @var Zend_Controller_Response_HttpTestCase
     */
    private $response;

    /**
     * @var Pimcore_Controller_Action
     */
    private $controller;

    function __construct(Zend_Controller_Response_HttpTestCase $response) {

        /**
         * this is very ugly but so you can pass the controller trought the response
         */
        if(isset($response->controllerImpl)) {
            $this->setController($response->controllerImpl);
            unset($response->controllerImpl);
        }

        $this->setResponse($response);
    }

    /**
     * @param \Pimcore_Controller_Action $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return \Pimcore_Controller_Action
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param \Zend_Http_Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return \Zend_Http_Response
     */
    public function getResponse()
    {
        return $this->response;
    }


}
