<?php
/**
 * @file
 * Basic index.php file for a Kisma application
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Examples
 * @package kisma.examples
 * @since 1.0.0
 *
 * @ingroup examples
 */

/**
 * app.config.php
 *
 * This is the configuration file for the Blog example application.
 *
 * Kisma looks in the config directory for an files and loads them automatically into app variables with the same name.
 *
 * Example:
 *
 * app.config.php:
 *
 * return(
 *		 array(
 *			 'one' => 1,
 *			 'two' => 2,
 *		 )
 * );
 *
 * $app['app.config'] == array( 'one' => 1, 'two' => 2 )
 *
 * In addition, each variable within the array will be loaded individually extending the file name.
 *
 * Example:
 *
 * app.config.php:
 *
 * return(
 *		 array(
 *			 'one' => 1,
 *			 'two' => 2,
 *		 )
 * );
 *
 * $app['app.config.one'] == 1
 * $app['app.config.two'] == 2
 *
 * Finally, any top-level key within the returned array that begins with an '@' sign will not have app.config.
 * pre-pended to it within the app array. This allows you to set top-level variables for Kisma and other
 * extensions.
 */

return array(

	//*************************************************************************
	//* Application-level Options
	//*************************************************************************

	//	The name of the application
	'app_name' => 'Example Blog',

	//	The namespace mapping for the autoloader
	'namespace' => array(
		'ExampleBlog' => __DIR__ . '/../..',
	),

	//	The name of our log file
	'log_file_name' => 'blog.log',

	//	Where log files will go
	'log_path' => 'logs',

	//	The controller path (defaults to /Controllers)
	'controller_path' => 'Controllers',

	//	The models path (defaults to /Models)
	'model_path' => 'Models',

	//	The documents path for CouchDB mappings (defaults to /Documents)
	'document_path' => 'Documents',

	//	The view path (defaults to /Views)
	'view_path' => 'Views',

	//	The default controller for the site
	'default_controller' => 'blog',

	//*************************************************************************
	//* Kisma/Silex Global Options
	//*************************************************************************

	//	Databases
	'@couchdbs.options' => array(
		'db.blog' => array(
			'host' => 'teledini.gna.me',
			'dbname' => 'kisma_examples_blog_posts',
			'user' => 'sinker',
			'password' => 'sinker',
		),
	),

	//	Logging
	'@monolog.options' => array(
		'fire_php' => true,
	),

	//	Widget options
	'@widget.options' => array(

	),
);
