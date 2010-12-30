<?php

abstract class BxZender_Controller_Plugin_AbstractPlugin
{
    /**
     * @var BxZender_Controller_Request_AbstractRequest
     */
    protected $_request;

    /**
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response;

    /**
     * Set request object
     *
     * @param BxZender_Controller_Request_AbstractRequest $request
     * @return Zend_Controller_Plugin_Abstract
     */
    public function setRequest(BxZender_Controller_Request_AbstractRequest $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Get request object
     *
     * @return BxZender_Controller_Request_AbstractRequest $request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set response object
     *
     * @param Zend_Controller_Response_Abstract $response
     * @return Zend_Controller_Plugin_Abstract
     */
    public function setResponse(Zend_Controller_Response_Abstract $response)
    {
        $this->_response = $response;
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
     * @param BxZender_Controller_Request_AbstractRequest $request
     * @return void
     */
    public function routeStartup(BxZender_Controller_Request_AbstractRequest $request)
    {}

    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  BxZender_Controller_Request_AbstractRequest $request
     * @return void
     */
    public function routeShutdown(BxZender_Controller_Request_AbstractRequest $request)
    {}

    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  BxZender_Controller_Request_AbstractRequest $request
     * @return void
     */
    public function dispatchLoopStartup(BxZender_Controller_Request_AbstractRequest $request)
    {}

    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  BxZender_Controller_Request_AbstractRequest $request
     * @return void
     */
    public function preDispatch(BxZender_Controller_Request_AbstractRequest $request)
    {}

    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior. By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * a new action may be specified for dispatching.
     *
     * @param  BxZender_Controller_Request_AbstractRequest $request
     * @return void
     */
    public function postDispatch(BxZender_Controller_Request_AbstractRequest $request)
    {}

    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {}
}