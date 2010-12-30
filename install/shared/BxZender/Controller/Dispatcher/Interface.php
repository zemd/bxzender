<?php
require_once 'BxZender/Controller/Request/AbstractRequest.php';
require_once 'Zend/Controller/Response/Abstract.php';

interface BxZender_Controller_Dispatcher_Interface
{
    public function formatControllerName($unformatted);

    public function formatActionName($unformatted);

    public function isDispatchable(BxZender_Controller_Request_AbstractRequest $request);

    public function setParam($name, $value);

    public function setParams(array $params);

    public function getParam($name);

    public function getParams();

    public function clearParams($name = null);

    public function setResponse(Zend_Controller_Response_Abstract $response = null);

    public function getResponse();

    public function addControllerDirectory($path);

    public function setControllerDirectory($path);

    public function getControllerDirectory();

    public function dispatch(BxZender_Controller_Request_AbstractRequest $request, Zend_Controller_Response_Abstract $response);

    public function getDefaultControllerName();

    public function getDefaultAction();
}