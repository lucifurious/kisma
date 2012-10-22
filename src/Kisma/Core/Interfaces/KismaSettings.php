<?php
/**
 * KismaSettings.php
 */
namespace Kisma\Core\Interfaces;
/**
 * KismaSettings
 * Default application-level settings defined by Kisma
 */
interface KismaSettings
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string Set to non-empty to enable debug logging
	 */
	const Debug = 'app.debug';
	/**
	 * @var string The base path of the Kisma library
	 */
	const BasePath = 'app.base_path';
	/**
	 * @var string The Composer autoloader object
	 */
	const AutoLoader = 'app.auto_loader';
	/**
	 * @var string Set to TRUE once Kisma is initialized
	 */
	const Conception = 'app.conception';
	/**
	 * @var string The version of Kisma
	 */
	const Version = 'app.version';
	/**
	 * @var string The name of the app
	 */
	const Name = 'app.name';
	/**
	 * @var string The navbar items, if any
	 */
	const NavBar = 'app.nav_bar';
	/**
	 * @var string The detected framework, if any
	 */
	const Framework = 'app.framework';
}
