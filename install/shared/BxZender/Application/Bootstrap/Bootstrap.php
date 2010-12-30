<?php

require_once 'BxZender/Application/Bootstrap/BootstrapAbstract.php';

class BxZender_Application_Bootstrap_Bootstrap extends BxZender_Application_Bootstrap_BootstrapAbstract
{
	public function __construct($application)
    {
        parent::__construct($application);

        if ($application->hasOption('resourceloader')) {
            $this->setOptions(array(
                'resourceloader' => $application->getOption('resourceloader')
            ));
        }
        $this->getResourceLoader();

        if (!$this->hasPluginResource('BitrixController')) {
            $this->registerPluginResource('BitrixController');
        }
    }
	
	public function run()
    {
		  $bitrix = $this->getResource('BitrixController');
          $bitrix->setParam('bootstrap', $this);
          $bitrix->dispatch();
          //var_dump($bitrix->getControllerDirectory());
//          if (null === $bitrix->getControllerDirectory())
//          {
//              throw new Zend_Application_Bootstrap_Exception(
//                'No default controller directory registered with front controller'
//              );
//          }

        //var_dump($response);
		//return $response;
        //if ($front->returnResponse()) {
        //    return $response;
        //}
    }

    public function post()
    {
        $bitrix = $this->getResource('BitrixController');
        $bitrix->setParam('bootstrap', $this);
        $response = $bitrix->postDispatch();
    }
	
	public function setResourceLoader(Zend_Loader_Autoloader_Resource $loader)
    {
        $this->_resourceLoader = $loader;
        return $this;
    }
	
	public function getResourceLoader()
    {
        if ((null === $this->_resourceLoader)
            && (false !== ($namespace = $this->getAppNamespace()))
        )
        {
            $r    = new ReflectionClass($this);
            $path = $r->getFileName();
            $this->setResourceLoader(new Zend_Application_Module_Autoloader(array(
                'namespace' => $namespace,
                'basePath'  => dirname($path),
            )));
        }
        return $this->_resourceLoader;
    }
	
	public function getAppNamespace()
    {
        return $this->_appNamespace;
    }
	
	public function setAppNamespace($value)
    {
        $this->_appNamespace = (string) $value;
        return $this;
    }
}
