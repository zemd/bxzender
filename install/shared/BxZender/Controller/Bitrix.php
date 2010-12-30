<?php

/** Zend_Loader */
require_once 'Zend/Loader.php';

/** Zend_Controller_Action_HelperBroker */
require_once 'Zend/Controller/Action/HelperBroker.php';

/** Zend_Controller_Plugin_Broker */
require_once 'BxZender/Controller/Plugin/Broker.php';

class BxZender_Controller_Bitrix
{
    /**
     * Base URL
     * @var string
     */
    protected $_baseUrl = null;

    /**
     * Directory|ies where controllers are stored
     *
     * @var string|array
     */
    protected $_controllerDir = null;

    /**
     * Instance of BxZender_Controller_Dispatcher_Interface
     * @var BxZender_Controller_Dispatcher_Interface
     */
    protected $_dispatcher = null;

    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Zend_Controller_Front
     */
    protected static $_instance = null;

    /**
     * Array of invocation parameters to use when instantiating action
     * controllers
     * @var array
     */
    protected $_invokeParams = array();

    /**
     * Instance of Zend_Controller_Plugin_Broker
     * @var Zend_Controller_Plugin_Broker
     */
    protected $_plugins = null;

    /**
     * Instance of BxZender_Controller_Request_AbstractRequest
     * @var BxZender_Controller_Request_AbstractRequest
     */
    protected $_request = null;

    /**
     * Instance of Zend_Controller_Response_Abstract
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response = null;

    /**
     * Whether or not to return the response prior to rendering output while in
     * {@link dispatch()}; default is to send headers and render output.
     * @var boolean
     */
    protected $_returnResponse = false;

    /**
     * Instance of Zend_Controller_Router_Interface
     * @var Zend_Controller_Router_Interface
     */
    protected $_router = null;

    /**
     * Whether or not exceptions encountered in {@link dispatch()} should be
     * thrown or trapped in the response object
     * @var boolean
     */
    protected $_throwExceptions = false;
    
    protected function __construct()
    {
        $this->_plugins = new BxZender_Controller_Plugin_Broker();

    }

    /**
     * Enforce singleton; disallow cloning
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Singleton instance
     *
     * @return Zend_Controller_Front
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    public static function run()
    {
        self::getInstance()
            ->dispatch();
    }
    
    public function setRequest($request)
    {
        if (is_string($request)) {
            if (!class_exists($request)) {
                require_once 'Zend/Loader.php';
                Zend_Loader::loadClass($request);
            }
            $request = new $request();
        }
        if (!$request instanceof BxZender_Controller_Request_AbstractRequest) {
            require_once 'Zend/Controller/Exception.php';
            throw new Zend_Controller_Exception('Invalid request class');
        }

        $this->_request = $request;

        return $this;
    }
    
    public function getRequest()
    {
        return $this->_request;
    }
    
    public function setDispatcher(BxZender_Controller_Dispatcher_Interface $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }
    
    public function getDispatcher()
    {
        /**
         * Instantiate the default dispatcher if one was not set.
         */
        if (!$this->_dispatcher instanceof BxZender_Controller_Dispatcher_Interface) {
            require_once 'BxZender/Controller/Dispatcher/Standard.php';
            $this->_dispatcher = new BxZender_Controller_Dispatcher_Standard();
        }
        return $this->_dispatcher;
    }

    public function setResponse($response)
    {
        if (is_string($response)) {
            if (!class_exists($response)) {
                require_once 'Zend/Loader.php';
                Zend_Loader::loadClass($response);
            }
            $response = new $response();
        }
        if (!$response instanceof Zend_Controller_Response_Abstract) {
            require_once 'Zend/Controller/Exception.php';
            throw new Zend_Controller_Exception('Invalid response class');
        }

        $this->_response = $response;

        return $this;
    }
    
    public function getResponse()
    {
        return $this->_response;
    }

    public function setParam($name, $value)
    {
        $name = (string) $name;
        $this->_invokeParams[$name] = $value;
        return $this;
    }


    public function setParams(array $params)
    {
        $this->_invokeParams = array_merge($this->_invokeParams, $params);
        return $this;
    }

    public function getParam($name)
    {
        if(isset($this->_invokeParams[$name])) {
            return $this->_invokeParams[$name];
        }

        return null;
    }

    public function getParams()
    {
        return $this->_invokeParams;
    }

    public function registerPlugin(BxZender_Controller_Plugin_AbstractPlugin $plugin, $stackIndex = null)
    {
        $this->_plugins->registerPlugin($plugin, $stackIndex);
        return $this;
    }

    public function unregisterPlugin($plugin)
    {
        $this->_plugins->unregisterPlugin($plugin);
        return $this;
    }

    public function hasPlugin($class)
    {
        return $this->_plugins->hasPlugin($class);
    }

    public function getPlugin($class)
    {
        return $this->_plugins->getPlugin($class);
    }


    public function getPlugins()
    {
        return $this->_plugins->getPlugins();
    }

    public function postDispatch()
    {
        if ($this->_request->isDispatched())
        {
            return;
        }
        /**
          * Notify plugins of dispatch completion
          */
        $this->_plugins->postDispatch($this->_request);
        /**
         * Notify plugins of dispatch loop completion
         */
        try
        {
            $this->_plugins->dispatchLoopShutdown();
        }
        catch (Exception $e) 
        {
            //if ($this->throwExceptions())
            //{
            //    throw $e;
            //}
            var_dump($e);
            $this->_response->setException($e);
        }
//        if ($this->returnResponse()) {
//            return $this->_response;
//        }
        $this->_response->sendResponse();
    }

    public function dispatch(BxZender_Controller_Request_AbstractRequest $request = null, Zend_Controller_Response_Abstract $response = null)
    {
        if (!$this->getParam('noErrorHandler') && !$this->_plugins->hasPlugin('Zend_Controller_Plugin_ErrorHandler')) {
            // Register with stack index of 100
            require_once 'BxZender/Controller/Plugin/ErrorHandler.php';
            $this->_plugins->registerPlugin(new BxZender_Controller_Plugin_ErrorHandler(), 100);
        }
        //if (!$this->getParam('noViewRenderer') && !Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
            //require_once 'Zend/Controller/Action/Helper/ViewRenderer.php';
            //Zend_Controller_Action_HelperBroker::getStack()->offsetSet(-80, new Zend_Controller_Action_Helper_ViewRenderer());
        //}
        /**
         * Instantiate default request object (HTTP version) if none provided
         */
        if (null !== $request) {
            $this->setRequest($request);
        } elseif ((null === $request) && (null === ($request = $this->getRequest()))) {
            require_once 'BxZender/Controller/Request/Http.php';
            $request = new BxZender_Controller_Request_Http();
            $this->setRequest($request);
        }
        /**
         * Set base URL of request object, if available
         */
        //var_dump("BASE URL: " . $this->_baseUrl);
        if (is_callable(array($this->_request, 'setBaseUrl'))) {
            if (null !== $this->_baseUrl) {
//                $this->_request->setBaseUrl($this->_baseUrl);
            }
        }

        /**
         * Instantiate default response object (HTTP version) if none provided
         */
        if (null !== $response)
        {
            $this->setResponse($response);
        }
        elseif ((null === $this->_response) && (null === ($this->_response = $this->getResponse())))
        {
            require_once 'Zend/Controller/Response/Http.php';
            $response = new Zend_Controller_Response_Http();
            $this->setResponse($response);
        }
        /**
         * Register request and response objects with plugin broker
         */
        $this->_plugins
             ->setRequest($this->_request)
             ->setResponse($this->_response);

        /**
         * Initialize dispatcher
         */
        $dispatcher = $this->getDispatcher();
        $dispatcher->setParams($this->getParams())
                   ->setResponse($this->_response);
        // Begin dispatch
        try {
            /**
             * Notify plugins of dispatch loop startup
             */
            $this->_plugins->dispatchLoopStartup($this->_request);
            /**
             *  Attempt to dispatch the controller/action. If the $this->_request
             *  indicates that it needs to be dispatched, move to the next
             *  action in the request.
             */
            do {
                $this->_request->setDispatched(true);
                /**
                 * Notify plugins of dispatch startup
                 */
                $this->_plugins->preDispatch($this->_request);
                /**
                 * Skip requested action if preDispatch() has reset it
                 */
                if (!$this->_request->isDispatched()) {
                    continue;
                }
                /**
                 * Dispatch request
                 */
                try {
                    $dispatcher->dispatch($this->_request, $this->_response);
                } catch (Exception $e) {
//                    if ($this->throwExceptions()) {
//                        throw $e;
//                    }
                    var_dump($e);
                    $this->_response->setException($e);
                }
            } while (!$this->_request->isDispatched());
        } catch (Exception $e) {
//            if ($this->throwExceptions()) {
//                throw $e;
//            }

            $this->_response->setException($e);
        }
    }

    public function addControllerDirectory($directory)
    {
        $this->getDispatcher()->addControllerDirectory($directory);
        return $this;
    }

    public function setControllerDirectory($directory)
    {
        $this->getDispatcher()->setControllerDirectory($directory);
        return $this;
    }

}
