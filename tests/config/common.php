<?php
/**
 * common.php
 *
 * This is the common configuration file for testing YiiXL with PHPUnit/Selenium.
 * There is a web component and a CLI component. They have separate configuration file (main.php for web, and console.php for CLI).
 * This file holds the common settings for both and is required by both above config files.
 *
 * @copyright Copyright (c) 2009-2011 Pogostick, LLC.
 * @link http://www.pogostick.com Pogostick, LLC.
 * @license http://www.pogostick.com/licensing
 * @package yiixl
 * @subpackage tests
 * @author Jerry Ablan <yiixl@pogostick.com>
 */

//	YiiXL bootstrap
require_once '/opt/kisma/framework/Kisma.php';

/**
 * Set some basic values to properly configure the return array
 */
$_isProduction = true;
$_autoConnect = false;
$_defaultController = 'app';
$_appUrl = 'http://localhost/testing';
$_logLevel = 'error,warning,info,trace';

$_dbConfig = array(
	'host' => 'localhost',
	'name' => 'any',
	'user' => 'app_user',
	'password' => 'app_user',
);

$_cacheObject = array(
	'class' => 'CApcCache',
);
