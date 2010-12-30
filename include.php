<?php
//error_reporting(E_ALL);
defined('BXZENDER_PATH') || define('BXZENDER_PATH', realpath(dirname(__FILE__)));
defined('BXZENDER_DEFAULT_CONFIG') || define('BXZENDER_DEFAULT_CONFIG', $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/bxzender/configs/application.ini');
set_include_path(
	implode(PATH_SEPARATOR,
            array(
                $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/bxzender/shared',
                get_include_path(),
            )
    )
);
require_once('BxZender/Application.php');
//new BxZender_Application($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/bxzender/configs/application.ini');