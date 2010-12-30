<?php
require_once('BxZender/Controller/Action.php');

class IndexController extends BxZender_Controller_Action
{
	public function indexAction()
	{
        $this->view->HELLO = "Hello world";
	}
	
	public function aboutAction()
	{
	}
}
