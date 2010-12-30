<?php
class bxzender extends CModule
{
	public $MODULE_ID = "bxzender";
	public $MODULE_VERSION = "0.1";
	public $MODULE_VERSION_DATE = "2010-06-08 14:40:00";
	public $MODULE_NAME = "bxzender";
	public $MODULE_DESCRIPTION = "Module description";
	public $MODULE_CSS = "";
	
	public function __construct()
	{
	}
	
	public function InstallDB($arParams = array())
	{
		RegisterModuleDependences("main", "OnPageStart", "bxzender", "BxZender_Application", "bootstrap", 1);
		RegisterModuleDependences("main", "OnProlog", "bxzender", "BxZender_Application", "run", 1);
		RegisterModuleDependences("main", "OnEpilog", "bxzender", "BxZender_Application", "postEpilog", 1);
		RegisterModule("bxzender");
		return true;
	}
	
	public function UnInstallDB($arParams = array())
	{
		UnRegisterModuleDependences("main", "OnPageStart", "bxzender", "BxZender_Application", "bootstrap", 1);
		UnRegisterModuleDependences("main", "OnProlog", "bxzender", "BxZender_Application", "run", 1);
        UnRegisterModuleDependences("main", "OnEpilog", "bxzender", "BxZender_Application", "postEpilog", 1);
		UnRegisterModule("bxzender");
		return true;
	}
	
	public function UnInstallEvents()
	{
		return true;
	}
	
	public function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/modules/bxzender/install/shared", $_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/bxzender/shared", false, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/modules/bxzender/install/configs", $_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/bxzender/configs", false, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/modules/bxzender/install/controllers", $_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/bxzender/controllers", false, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/modules/bxzender/install/shared", $_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/bxzender/shared", false, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/modules/bxzender/install/Bootstrap.php", $_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/bxzender/Bootstrap.php", false, true);
		return true;
	}
	
	public function UnInstallFiles()
	{
		return true;
	}
	
	public function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallDB();
        $this->InstallFiles();
		$APPLICATION->IncludeAdminFile("bxzender module install", $DOCUMENT_ROOT . "/" . BX_ROOT . "/modules/".$this->MODULE_ID."/install/step.php");
	}
	
	public function DoUninstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallDB();
        $this->UnInstallFiles();
		$APPLICATION->IncludeAdminFile("bxzender module uninstall", $DOCUMENT_ROOT."/".BX_ROOT."/modules/".$this->MODULE_ID."/install/unstep.php");
	}
}