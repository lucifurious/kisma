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
 * view.config.php
 *
 * This is an optional view configuration file.
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

	//	Global view options
	'@view.defaults' => array(
		'page_header' => 'The Kisma Blog',
		'page_header_small' => 'The rantings of a lunatic geek...',
		'topbar' => array(
			'brand' => 'Kisma',
			'items' => array(
				array(
					'active' => true,
					'href' => '#',
					'target' => null,
					'title' => 'Home',
				)
			),
		),
	),

	/**
	 * Fine tuning
	 *
	 *	Format:  <controller>.<action> => array(
	 *				<option> => <value>
	 *			)
	 *
	 *	Options:
	 *
	 * 	'view_file' => '/path/to/view'
	 * 	'page_header' => 'page header',
	 * 	'page_header_small' => 'page header subtitle',
	 *
	 */
	'blog.index' => array(
		'page_header' => 'The Kisma Blog : Index',
		'page_header_small' => 'The rantings of a lunatic geek...',
	),
);
