<?php

class Pimunit_Controller_Dispatcher_Standard extends Zend_Controller_Dispatcher_Standard {

    protected $hooks = array();

    public function setHooks(array $arrayOfCallables) {
        foreach($arrayOfCallables as $v)
            if(!is_callable($v))
                throw new \Exception('invalid Arg');

        $this->hooks = $arrayOfCallables;
    }

    public function hasHook($hookname) {
        return isset($this->hooks[$hookname]);
    }

    /**
     * @param $hookname
     * @return Callable
     */
    public function getHook($hookname) {
        if(!$this->hasHook($hookname))
            return false;

        return $this->hooks[$hookname];
    }

    public function dispatch(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response)
    {
        $this->setResponse($response);

        /**
         * Get controller class
         */
        if (!$this->isDispatchable($request)) {
            $controller = $request->getControllerName();
            if (!$this->getParam('useDefaultControllerAlways') && !empty($controller)) {
                require_once 'Zend/Controller/Dispatcher/Exception.php';
                throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $request->getControllerName() . ')');
            }

            $className = $this->getDefaultControllerClass($request);
        } else {
            $className = $this->getControllerClass($request);
            if (!$className) {
                $className = $this->getDefaultControllerClass($request);
            }
        }

        /**
         * Load the controller class file
         */
        $className = $this->loadClass($className);


        /**
         * Instantiate controller with request, response, and invocation
         * arguments; throw exception if it's not an action controller
         */

        if($this->hasHook('createControllerInstance.pre')) {
            $hookLambda = $this->getHook('createControllerInstance.pre');
            $hookLambda($this, $className, $request);
        }

        if($this->hasHook('createControllerInstance')) {
            $hookLambda = $this->getHook('createControllerInstance');
            $controller = $hookLambda($className, $request, $this);
        } else {
            $controller = new $className($request, $this->getResponse(), $this->getParams());
        }

        if($this->hasHook('createControllerInstance.post')) {
            $hookLambda = $this->getHook('createControllerInstance.post');
            $hookLambda($this, $controller, $className, $request);
        }

        if (!($controller instanceof Zend_Controller_Action_Interface) &&
            !($controller instanceof Zend_Controller_Action)) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception(
                'Controller "' . $className . '" is not an instance of Zend_Controller_Action_Interface'
            );
        }

        /**
         * Retrieve the action name
         */
        $action = $this->getActionMethod($request);


        /**
         * Dispatch the method call
         */
        $request->setDispatched(true);

        // by default, buffer output
        $disableOb = $this->getParam('disableOutputBuffering');
        $obLevel   = ob_get_level();
        if (empty($disableOb)) {
            ob_start();
        }

        if($this->hasHook('dispatchAction.pre')) {
            $hookLambda = $this->getHook('dispatchAction.pre');
            $hookLambda($this, $controller, $action);
        }

        try {
            if($this->hasHook('dispatchAction')) {
                $hookLambda = $this->getHook('dispatchAction');
                $hookLambda($this, $controller, $action);
            } else {
                $controller->dispatch($action);
            }

        } catch (Exception $e) {
            // Clean output buffer on error
            $curObLevel = ob_get_level();
            if ($curObLevel > $obLevel) {
                do {
                    ob_get_clean();
                    $curObLevel = ob_get_level();
                } while ($curObLevel > $obLevel);
            }
            throw $e;
        }

        if($this->hasHook('dispatchAction.post')) {
            $hookLambda = $this->getHook('dispatchAction.post');
            $hookLambda($this, $controller, $action);
        }

        if (empty($disableOb)) {
            $content = ob_get_clean();
            $response->appendBody($content);
        }

        $response->controllerImpl = $controller;
    }

}