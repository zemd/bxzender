<?php
/** Zend_Controller_Plugin_Abstract */
require_once 'BxZender/Controller/Plugin/AbstractPlugin.php';


class BxZender_Controller_Plugin_Broker extends BxZender_Controller_Plugin_AbstractPlugin
{
    /**
     * Array of instance of objects extending BxZender_Controller_Plugin_AbstractPlugin
     *
     * @var array
     */
    protected $_plugins = array();

    /**
     * Register a plugin.
     *
     * @param  BxZender_Controller_Plugin_AbstractPlugin $plugin
     * @param  int $stackIndex
     * @return Zend_Controller_Plugin_Broker
     */
    public function registerPlugin(BxZender_Controller_Plugin_AbstractPlugin $plugin, $stackIndex = null)
    {
        if (false !== array_search($plugin, $this->_plugins, true))
        {
            require_once 'Zend/Controller/Exception.php';
            throw new Zend_Controller_Exception('Plugin already registered');
        }

        $stackIndex = (int) $stackIndex;

        if ($stackIndex)
        {
            if (isset($this->_plugins[$stackIndex]))
            {
                require_once 'Zend/Controller/Exception.php';
                throw new Zend_Controller_Exception('Plugin with stackIndex "' . $stackIndex . '" already registered');
            }
            $this->_plugins[$stackIndex] = $plugin;
        }
        else
        {
            $stackIndex = count($this->_plugins);
            while (isset($this->_plugins[$stackIndex]))
            {
                ++$stackIndex;
            }
            $this->_plugins[$stackIndex] = $plugin;
        }

        $request = $this->getRequest();
        if ($request) {
            $this->_plugins[$stackIndex]->setRequest($request);
        }
        $response = $this->getResponse();
        if ($response) {
            $this->_plugins[$stackIndex]->setResponse($response);
        }

        ksort($this->_plugins);

        return $this;
    }

    /**
     * Unregister a plugin.
     *
     * @param string|Zend_Controller_Plugin_Abstract $plugin Plugin object or class name
     * @return Zend_Controller_Plugin_Broker
     */
    public function unregisterPlugin($plugin)
    {
        if ($plugin instanceof BxZender_Controller_Plugin_AbstractPlugin) {
            // Given a plugin object, find it in the array
            $key = array_search($plugin, $this->_plugins, true);
            if (false === $key) {
                require_once 'Zend/Controller/Exception.php';
                throw new Zend_Controller_Exception('Plugin never registered.');
            }
            unset($this->_plugins[$key]);
        } elseif (is_string($plugin)) {
            // Given a plugin class, find all plugins of that class and unset them
            foreach ($this->_plugins as $key => $_plugin) {
                $type = get_class($_plugin);
                if ($plugin == $type) {
                    unset($this->_plugins[$key]);
                }
            }
        }
        return $this;
    }

    /**
     * Is a plugin of a particular class registered?
     *
     * @param  string $class
     * @return bool
     */
    public function hasPlugin($class)
    {
        foreach ($this->_plugins as $plugin) {
            $type = get_class($plugin);
            if ($class == $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve a plugin or plugins by class
     *
     * @param  string $class Class name of plugin(s) desired
     * @return false|BxZender_Controller_Plugin_AbstractPlugin|array Returns false if none found, plugin if only one found, and array of plugins if multiple plugins of same class found
     */
    public function getPlugin($class)
    {
        $found = array();
        foreach ($this->_plugins as $plugin) {
            $type = get_class($plugin);
            if ($class == $type) {
                $found[] = $plugin;
            }
        }

        switch (count($found)) {
            case 0:
                return false;
            case 1:
                return $found[0];
            default:
                return $found;
        }
    }

    /**
     * Retrieve all plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }

    /**
     * Set request object, and register with each plugin
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return Zend_Controller_Plugin_Broker
     */
    public function setRequest(BxZender_Controller_Request_AbstractRequest $request)
    {
        $this->_request = $request;

        foreach ($this->_plugins as $plugin) {
            $plugin->setRequest($request);
        }

        return $this;
    }

    /**
     * Get request object
     *
     * @return Zend_Controller_Request_Abstract $request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set response object
     *
     * @param Zend_Controller_Response_Abstract $response
     * @return Zend_Controller_Plugin_Broker
     */
    public function setResponse(Zend_Controller_Response_Abstract $response)
    {
        $this->_response = $response;

        foreach ($this->_plugins as $plugin) {
            $plugin->setResponse($response);
        }


        return $this;
    }

    /**
     * Get response object
     *
     * @return Zend_Controller_Response_Abstract $response
     */
    public function getResponse()
    {
        return $this->_response;
    }


    /**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(BxZender_Controller_Request_AbstractRequest $request)
    {
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->routeStartup($request);
            } catch (Exception $e) {
                if (BxZender_Controller_Bitrix::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called before Zend_Controller_Front exits its iterations over
     * the route set.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(BxZender_Controller_Request_AbstractRequest $request)
    {
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->routeShutdown($request);
            } catch (Exception $e) {
                if (BxZender_Controller_Bitrix::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * During the dispatch loop, Zend_Controller_Front keeps a
     * Zend_Controller_Request_Abstract object, and uses
     * Zend_Controller_Dispatcher to dispatch the
     * Zend_Controller_Request_Abstract object to controllers/actions.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(BxZender_Controller_Request_AbstractRequest $request)
    {
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->dispatchLoopStartup($request);
            } catch (Exception $e) {
                if (BxZender_Controller_Bitrix::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(BxZender_Controller_Request_AbstractRequest $request)
    {
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->preDispatch($request);
            } catch (Exception $e) {
                if (BxZender_Controller_Bitrix::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(BxZender_Controller_Request_AbstractRequest $request)
    {
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->postDispatch($request);
            } catch (Exception $e) {
                if (BxZender_Controller_Bitrix::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopShutdown()
    {
       foreach ($this->_plugins as $plugin) {
           try {
                $plugin->dispatchLoopShutdown();
            } catch (Exception $e) {
                if (BxZender_Controller_Bitrix::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
       }
    }
}
