<?php

class Pimcore_Test_Case_Controller extends Pimcore_Test_Case {

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return Pimcore_Test_Case_Controller_Response
     */
    public function dispatchRequest(Zend_Controller_Request_Abstract $request) {
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();

        $front->setDispatcher(new Pimunit_Controller_Dispatcher_Standard());

        Zend_Controller_Action_HelperBroker::addHelper(new Pimcore_Controller_Action_Helper_ViewRenderer());
        Zend_Controller_Action_HelperBroker::addHelper(new Pimcore_Controller_Action_Helper_Json());

        // register general pimcore plugins for frontend
        $front->registerPlugin(new Pimcore_Controller_Plugin_ErrorHandler(), 1);
        $front->registerPlugin(new Pimcore_Controller_Plugin_Less(), 799);
        $front->registerPlugin(new Pimcore_Controller_Plugin_Robotstxt(), 795);
        $front->registerPlugin(new Pimcore_Controller_Plugin_WysiwygAttributes(), 796);
        $front->registerPlugin(new Pimcore_Controller_Plugin_Webmastertools(), 797);
        $front->registerPlugin(new Pimcore_Controller_Plugin_Analytics(), 798);
        $front->registerPlugin(new Pimcore_Controller_Plugin_CssMinify(), 800);
        $front->registerPlugin(new Pimcore_Controller_Plugin_JavascriptMinify(), 801);
        $front->registerPlugin(new Pimcore_Controller_Plugin_ImageDataUri(), 803);
        $front->registerPlugin(new Pimcore_Controller_Plugin_Cache(), 901);

        Pimcore::initControllerFront($front);

        // set router
        $router = $front->getRouter();
        $routeAdmin = new Zend_Controller_Router_Route(
            'admin/:controller/:action/*',
            array(
                'module' => 'admin',
                "controller" => "index",
                "action" => "index"
            )
        );
        $routeInstall = new Zend_Controller_Router_Route(
            'install/:controller/:action/*',
            array(
                'module' => 'install',
                "controller" => "index",
                "action" => "index"
            )
        );
        $routeUpdate = new Zend_Controller_Router_Route(
            'admin/update/:controller/:action/*',
            array(
                'module' => 'update',
                "controller" => "index",
                "action" => "index"
            )
        );
        $routePlugins = new Zend_Controller_Router_Route(
            'admin/plugin/:controller/:action/*',
            array(
                'module' => 'pluginadmin',
                "controller" => "index",
                "action" => "index"
            )
        );
        $routeExtensions = new Zend_Controller_Router_Route(
            'admin/extensionmanager/:controller/:action/*',
            array(
                'module' => 'extensionmanager',
                "controller" => "index",
                "action" => "index"
            )
        );
        $routeReports = new Zend_Controller_Router_Route(
            'admin/reports/:controller/:action/*',
            array(
                'module' => 'reports',
                "controller" => "index",
                "action" => "index"
            )
        );
        $routePlugin = new Zend_Controller_Router_Route(
            'plugin/:module/:controller/:action/*',
            array(
                "controller" => "index",
                "action" => "index"
            )
        );
        $routeWebservice = new Zend_Controller_Router_Route(
            'webservice/:controller/:action/*',
            array(
                "module" => "webservice",
                "controller" => "index",
                "action" => "index"
            )
        );

        $routeSearchAdmin = new Zend_Controller_Router_Route(
            'admin/search/:controller/:action/*',
            array(
                "module" => "searchadmin",
                "controller" => "index",
                "action" => "index",
            )
        );

        $front->getRouter()->addRoute('default', new Pimcore_Controller_Router_Route_Frontend());
        $front->getRouter()->addRoute("install", $routeInstall);
        $front->getRouter()->addRoute('plugin', $routePlugin);
        $front->getRouter()->addRoute('admin', $routeAdmin);
        $front->getRouter()->addRoute('update', $routeUpdate);
        $front->getRouter()->addRoute('plugins', $routePlugins);
        $front->getRouter()->addRoute('extensionmanager', $routeExtensions);
        $front->getRouter()->addRoute('reports', $routeReports);
        $front->getRouter()->addRoute('searchadmin', $routeSearchAdmin);
        $front->getRouter()->addRoute('webservice', $routeWebservice);

        $response = new Zend_Controller_Response_HttpTestCase();

        $front->throwExceptions(true);
        $front->dispatch($request, $response);

        return new Pimcore_Test_Case_Controller_Response($response);
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return Zend_Controller_Request_HttpTestCase | string
     */
    public function dispatch($request) {

        if(is_string($request)) {
            $r = new Zend_Controller_Request_HttpTestCase();
            $r->setRequestUri($request);
            return $this->dispatchRequest($r);
        }

        if($request instanceof Zend_Controller_Request_HttpTestCase) {
            return $this->dispatchRequest($request);
        }

        throw new \Exception('bad Argument, string or Zend_Controller_Request_HttpTestCase required');
    }

}