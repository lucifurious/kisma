<?php
/**
 * Kisma.php
 * The Kisma(tm) Fun-Size Framework bootstrap loader
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 *
 * @copyright Copyright (c) 2009-2012 Jerry Ablan
 * @license   MIT (http://github.com/lucifurious/kisma/blob/master/LICENSE)
 * @author    Jerry Ablan <get.kisma@gmail.com>
 */
namespace Kisma;

require_once __DIR__ . '/Kisma/enums.php';
require_once __DIR__ . '/Kisma/Components/Seed.php';
require_once __DIR__ . '/Kisma/Utility/Option.php';

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
	 * @var \Composer\Autoload\ClassLoader
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
	/**
	 * @var bool True if Kisma has been initialized
	 */
	protected static $_conception = false;

	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * Plant the seed of life into Kisma!
	 *
	 * @param array $options
	 *
	 * @return bool
	 */
	public static function conceive( $options = array() )
	{
		if ( false === self::$_conception || empty( self::$_autoLoader ) )
		{
			/**
			 * Set up the autoloader
			 */
			self::$_autoLoader = require( dirname( __DIR__ ) . '/vendor/autoload.php' );

			if ( is_callable( $options ) )
			{
				$options = call_user_func( $options );
			}

			//	Set any application-level options passed in
			self::$_options = \Kisma\Utility\Option::merge( self::$_options, $options );

			//	We done baby!
			self::$_conception = true;
		}

		return self::$_conception;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	public static function set( $key, $value = null )
	{
		return \Kisma\Utility\Option::set( self::$_options, $key, $value );
	}

	/**
	 * @param string $key
	 * @param mixed  $defaultValue
	 * @param bool   $removeIfFound
	 *
	 * @return mixed
	 */
	public static function get( $key, $defaultValue = null, $removeIfFound = false )
	{
		return \Kisma\Utility\Option::get( self::$_options, $key, $defaultValue, $removeIfFound );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @return null|\Composer\Autoload\ClassLoader
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
	 * @return array
	 */
	public static function getOptions()
	{
		return self::$_options;
	}

}
