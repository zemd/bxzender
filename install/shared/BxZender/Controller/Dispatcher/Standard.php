<?php
require_once 'Zend/Loader.php';
require_once 'BxZender/Controller/Dispatcher/AbstractDispatcher.php';
class BxZender_Controller_Dispatcher_Standard extends BxZender_Controller_Dispatcher_AbstractDispatcher
{
    protected $_curDirectory;
    protected $_controllerDirectory;

    public function __construct(array $params = array())
    {
        parent::__construct($params);
    }

    public function addControllerDirectory($path)
    {
        $path   = rtrim((string) $path, '/\\');
        $this->_controllerDirectory = $path;
        return $this;
    }

    public function setControllerDirectory($directory)
    {
        if (is_string($directory)) {
            $this->addControllerDirectory($directory);
        } else {
            require_once 'Zend/Controller/Exception.php';
            throw new Zend_Controller_Exception('Controller directory spec must be a string');
        }
        return $this;
    }

    public function getControllerDirectory()
    {
        return $this->_controllerDirectory;
    }

    public function removeControllerDirectory()
    {
        unset($this->_controllerDirectory);
        return true;
    }

    public function formatClassName($className)
    {
        return $className;
    }

    public function classToFilename($class)
    {
        return str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    }

    public function isDispatchable(BxZender_Controller_Request_AbstractRequest $request)
    {
        $className = $this->getControllerClass($request);
        if (!$className) {
            return false;
        }

        $finalClass  = $className;
        if (class_exists($finalClass, false)) {
            return true;
        }

        $fileSpec    = $this->classToFilename($className);
        $dispatchDir = $this->getDispatchDirectory();
        $test        = $dispatchDir . DIRECTORY_SEPARATOR . $fileSpec;
        return Zend_Loader::isReadable($_SERVER['DOCUMENT_ROOT'] . $test);
    }

    public function dispatch(BxZender_Controller_Request_AbstractRequest $request, Zend_Controller_Response_Abstract $response)
    {
        $this->setResponse($response);
        $this->isDispatchable($request);
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
        $controller = new $className($request, $this->getResponse(), $this->getParams());

        if (!($controller instanceof BxZender_Controller_Action_Interface) &&
            !($controller instanceof BxZender_Controller_Action)) {
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

        try {
             var_dump(11);
            $controller->dispatch($action);
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

        if (empty($disableOb)) {
            $content = ob_get_clean();
            $response->appendBody($content);
        }
        // Destroy the page controller instance and reflection objects
        $controller = null;
    }

    public function loadClass($className)
    {
        $finalClass  = $className;
        if (class_exists($finalClass, false)) {
            return $finalClass;
        }

        $dispatchDir = $_SERVER['DOCUMENT_ROOT'] . $this->getDispatchDirectory();
        $loadFile    = $dispatchDir . DIRECTORY_SEPARATOR . $this->classToFilename($className);

        if (Zend_Loader::isReadable($loadFile)) {
            include_once $loadFile;
        } else {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception('Cannot load controller class "' . $className . '" from file "' . $loadFile . "'");
        }

        if (!class_exists($finalClass, false)) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller class ("' . $finalClass . '")');
        }

        return $finalClass;
    }

    public function getControllerClass(BxZender_Controller_Request_AbstractRequest $request)
    {
        $controllerName = CAllOption::GetOptionString("bxzender", "controller", "index");

        if (empty($controllerName)) {
            if (!$this->getParam('useDefaultControllerAlways')) {
                return false;
            }
            $controllerName = $this->getDefaultControllerName();
            $request->setControllerName($controllerName);
        }

        $className = $this->formatControllerName($controllerName);

        $controllerDirs      = $this->getControllerDirectory();
        $this->_curDirectory = $controllerDirs;
        return $className;
    }

    public function getDefaultControllerClass(Zend_Controller_Request_Abstract $request)
    {
        $controller = $this->getDefaultControllerName();
        $default    = $this->formatControllerName($controller);
        $request->setControllerName($controller)
                ->setActionName(null);

        $controllerDirs      = $this->getControllerDirectory();
        $this->_curDirectory = $controllerDirs;

        return $default;
    }

    public function getDispatchDirectory()
    {
        return $this->_curDirectory;
    }

    public function getActionMethod(BxZender_Controller_Request_AbstractRequest $request)
    {
        $action = $request->getActionName();
        if (empty($action)) {
            $action = $this->getDefaultAction();
            $request->setActionName($action);
        }

        return $this->formatActionName($action);
    }
}