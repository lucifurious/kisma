<?php
//	Include the common stuff...
require_once __DIR__ . '/common.php';

$_frameworkPath = __DIR__ . '/../../framework';

Yii::setPathOfAlias( 'kisma', $_frameworkPath );

//	Our configuration array
return array(
	//	/tests/config => /framework/core
	'basePath' => $_frameworkPath . '/core',
	'runtimePath' => '/var/log/kisma',
	'name' => 'Kisma Test Suite',

	//	preloading 'log' component
	'preload' => array( 'log' ),

	//	autoloading model and component classes
	'import' => array(
		//	System...
		'application.models.*',
		'application.components.*',
		'application.controllers.*',
		//	Kisma
		'kisma.core.*',
		'kisma.core.components.*',
	),

	//	application components
	'components' => array(

		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => $_logLevel,
					'maxFileSize' => 10240,
					'maxLogFiles' => 7,
					'logFile' => 'kisma_tests.log',
				),
			),
		),

		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => false,
			'rules' => array(
			),
		),

		'errorHandler' => array(
			'errorAction' => 'site/error',
		),
	),

	//	Application-level parameters
	'params' => array(
		'version' => '1.0',
		'adminEmail' => 'opensource@pogostick.com',
		'@copyright' => 'Copyright &copy; ' . date('Y') . ' Pogostick, LLC.',
		'@author' => 'Jerry Ablan <opensource@pogostick.com>',
		'@link' => 'http://www.pogostick.com/opensource/',
		'@package' => 'kisma',
		'@category' => 'Kisma Tests',

		//	UI theme
		'theme' => 'redmond',
	),

);
