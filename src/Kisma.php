<?php
/**
 * @file
 *            The Kisma(tm) Framework bootstrap loader
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license   http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author    Jerry Ablan <kisma@pogostick.com>
 */

require_once __DIR__ . '/Kisma/enums.php';
require_once __DIR__ . '/Kisma/Components/Seed.php';

use Kisma\Utility\Option;

use Symfony\Component\ClassLoader\UniversalClassLoader;

/**
 * The Kisma bootstrap loader
 *
 * Contains a few core functions implemented statically to be lightweight and single instance.
 */
class Kisma
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Symfony\Component\ClassLoader\UniversalClassLoader
	 */
	protected static $_autoLoader = null;
	/**
	 * @var string The library's base path
	 */
	protected static $_basePath = __DIR__;
	/**
	 * @var array The library configuration options
	 */
	protected static $_options = array();

	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * @static
	 *
	 * @param array $options
	 */
	public static function initialize( $options = array() )
	{
		static $_initialized = false;

		if ( false === $_initialized )
		{
			/**
			 * Set up the autoloader
			 */
			self::$_autoLoader = new UniversalClassLoader();
			self::$_autoLoader->register( true );
			self::$_autoLoader->useIncludePath( true );

			//	We done baby!
			$_initialized = true;
		}
	}

	/**
	 * Registers a namespace.
	 *
	 * @param string       $namespace The namespace
	 * @param array|string $paths     The location(s) of the namespace
	 */
	public static function registerNamespace( $namespace, $paths )
	{
		self::initialize();
		self::$_autoLoader->registerNamespace( $namespace, $paths );
	}

	/**
	 * Registers a set of classes using the PEAR naming convention.
	 *
	 * @param string       $prefix  The classes prefix
	 * @param array|string $paths   The location(s) of the classes
	 */
	public static function registerPrefix( $prefix, $paths )
	{
		self::initialize();
		self::$_autoLoader->registerPrefix( $prefix, $paths );
	}

	/**
	 * @static
	 *
	 * @param string $message
	 * @param int    $logLevel
	 *
	 * @param array  $context
	 *
	 * @return bool
	 */
	public static function log( $message, $logLevel = \CIS\Utility\Lumberjack::Info, $context = array() )
	{
//		if ( !self::$_lumberjack )
//		{
//			self::initialize();
//		}
//
//		return self::$_lumberjack->log( $message, $logLevel, $context );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @return null|\Symfony\Component\ClassLoader\UniversalClassLoader
	 */
	public static function getAutoLoader()
	{
		return self::$_autoLoader;
	}

	/**
	 * @return string
	 */
	public static function getBasePath()
	{
		return self::$_basePath;
	}

	/**
	 * @param array $options
	 */
	public static function setOptions( $options )
	{
		self::$_options = $options;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	public static function setOption( $key, $value = null )
	{
		return Option::set( self::$_options, $key, $value );
	}

	/**
	 * @return array
	 */
	public static function getOptions()
	{
		return self::$_options;
	}

	/**
	 * @param string $key
	 * @param mixed  $defaultValue
	 * @param bool   $removeIfFound
	 *
	 * @return mixed
	 */
	public static function getOption( $key, $defaultValue = null, $removeIfFound = false )
	{
		return Option::get( self::$_options, $key, $defaultValue, $removeIfFound );
	}
}