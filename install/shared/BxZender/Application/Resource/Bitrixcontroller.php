<?php

require_once 'Zend/Application/Resource/ResourceAbstract.php';


class BxZender_Application_Resource_Bitrixcontroller extends Zend_Application_Resource_ResourceAbstract
{

  protected $_bitrix;
  
  public function init()
  {
    $bitrix = $this->getBitrixController();

    foreach ($this->getOptions() as $key => $value) 
    {
        switch (strtolower($key)) {
                case 'controllerdirectory':
                    if (is_string($value)) {
                        $bitrix->setControllerDirectory($value);
                    }
                    break;
                case 'defaultcontrollername':
                    $bitrix->setDefaultControllerName($value);
                    break;

                case 'defaultaction':
                    $bitrix->setDefaultAction($value);
                    break;

                case 'baseurl':
                    if (!empty($value)) {
                        $bitrix->setBaseUrl($value);
                    }
                    break;

                case 'params':
                    $bitrix->setParams($value);
                    break;

                case 'plugins':
                    foreach ((array) $value as $pluginClass) {
                    	$stackIndex = null;
                    	if(is_array($pluginClass)) {
                    	    $pluginClass = array_change_key_case($pluginClass, CASE_LOWER);
                            if(isset($pluginClass['class']))
                            {
                                if(isset($pluginClass['stackindex'])) {
                                    $stackIndex = $pluginClass['stackindex'];
                                }

                                $pluginClass = $pluginClass['class'];
                            }
                        }

                        $plugin = new $pluginClass();
                        $bitrix->registerPlugin($plugin, $stackIndex);
                    }
                    break;

                //case 'returnresponse':
                //    $bitrix->returnResponse((bool) $value);
                //    break;

                //case 'throwexceptions':
                //    $bitrix->throwExceptions((bool) $value);
                //    break;

                //case 'actionhelperpaths':
                //    if (is_array($value)) {
                //        foreach ($value as $helperPrefix => $helperPath) {
                //            Zend_Controller_Action_HelperBroker::addPath($helperPath, $helperPrefix);
                //        }
                //    }
                //    break;

                default:
                    $bitrix->setParam($key, $value);
                    break;
            }
      }

    return $bitrix;
  }
  
  public function getBitrixController()
  {

    if (null === $this->_bitrix) {
      require_once('BxZender/Controller/Bitrix.php');
      $this->_bitrix = BxZender_Controller_Bitrix::getInstance();
    }
    return $this->_bitrix;
  }
}  
