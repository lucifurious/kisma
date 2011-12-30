<?php
/**
 * @file
 * The Kisma(tm) Framework bootstrap loader
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Framework
 * @package kisma
 * @since 1.0.0
 *
 * @ingroup framework
 */

namespace Kisma;

//*************************************************************************
//* Requirements
//*************************************************************************

require_once __DIR__ . '/../vendor/silex.phar';
require_once __DIR__ . '/Kisma/enums.php';

//*************************************************************************
//* Aliases
//*************************************************************************

use \Silex;
use \Kisma\Event;
use \Kisma\Event\Listener\ApplicationListener;
use \Kisma\Provider;

/**
 * The Kisma bootstrap loader
 *
 * Contains a few core functions implemented statically to be lightweight and single instance.
 *
 * @property int $debugLevel
 * @property \Pimp $settings
 */
class Kisma extends \Silex\Application
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const AppConfig = 'app.config';
	/**
	 * @var string
	 */
	const Autoloader = 'autoloader';
	/**
	 * @var string
	 */
	const Routes = 'routes';
	/**
	 * @var string
	 */
	const Controllers = 'controllers';
	/**
	 * @var string
	 */
	const Dispatcher = 'dispatcher';
	/**
	 * @var string
	 */
	const Resolver = 'resolver';
	/**
	 * @var string
	 */
	const Kernel = 'kernel';
	/**
	 * @var string
	 */
	const RequestContext = 'request_context';
	/**
	 * @var string
	 */
	const ExceptionHandler = 'exception_handler';

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Construct our app and set our event handler
	 */
	public function __construct()
	{
		parent::__construct();

		//	Add our event handler
		$this[self::Dispatcher]->addSubscriber( new ApplicationListener() );

		//	Dispatch initialize...
		$this[self::Dispatcher]->dispatch(
			Event\ApplicationEvent::INITIALIZE,
			new Event\ApplicationEvent( $this )
		);
	}

	/**
	 * Destructor chosen!
	 */
	function __destruct()
	{
		//	Dispatch terminator...
		$this[self::Dispatcher]->dispatch(
			Event\ApplicationEvent::TERMINATE,
			new Event\ApplicationEvent( $this )
		);
	}

	/**
	 * Called after this application is constructed and is ready to begin
	 *
	 * @return bool
	 */
	public function initialize()
	{
		//@todo	Read configuration file...
		$this[self::AppConfig] = $this->share( function ()
		{
			return new \Kisma\Provider\AppConfigServiceProvider();
		} );

		//	Add our required namespaces
		$this[self::Autoloader]->registerNamespaces(
			array(
				'Kisma' => __DIR__,
				'SilexExtension' => __DIR__ . '/../vendor/silex-extension/src'
			) );

		//	Some initialization
		$this['is_cli'] = ( 'cli' == php_sapi_name() );

		return true;
	}

	/**
	 * Called when the app is ending :(
	 */
	public function terminate()
	{
		//	Do stuff
	}

	//*************************************************************************
	//* Array/Option Methods
	//*************************************************************************

	/**
	 * Alias for {@link \Kisma\Kisma::o)
	 *
	 * @param array	  $options
	 * @param string	 $key
	 * @param mixed|null $defaultValue
	 * @param boolean	$unsetValue
	 *
	 * @return mixed
	 */
	public static function getOption( &$options = array(), $key, $defaultValue = null, $unsetValue = false )
	{
		return self::o( $options, $key, $defaultValue, $unsetValue );
	}

	/**
	 * Retrieves an option from the given array. $defaultValue is set and returned if $_key is not 'set'.
	 * Optionally will unset option in array.
	 *
	 * @param array	  $options
	 * @param string	 $key
	 * @param mixed|null $defaultValue
	 * @param boolean	$unsetValue
	 *
	 * @return mixed
	 * @see \Kisma\Kisma::getOption
	 */
	public static function o( &$options = array(), $key, $defaultValue = null, $unsetValue = false )
	{
		$_originalKey = $key;

		//	Set the default value
		$_newValue = $defaultValue;

		//	Get array value if it exists
		if ( is_array( $options ) )
		{
			//	Check for the original key too
			if ( isset( $options[$_originalKey] ) )
			{
				$key = $_originalKey;
			}

			if ( isset( $options[$key] ) )
			{
				$_newValue = $options[$key];

				if ( $unsetValue )
				{
					unset( $options[$key] );
				}
			}

			//	Set it in the array if not an unsetter...
			if ( !$unsetValue )
			{
				$options[$key] = $_newValue;
			}
		}
		//	Also now handle accessible object properties
		else if ( is_object( $options ) )
		{
			if ( property_exists( $options, $_originalKey ) )
			{
				$key = $_originalKey;
			}

			if ( property_exists( $options, $key ) )
			{
				if ( isset( $options->$key ) )
				{
					$_newValue = $options->$key;

					if ( $unsetValue )
					{
						unset( $options->$key );
					}
				}

				if ( !$unsetValue )
				{
					$options->$key = $_newValue;
				}
			}
		}

		//	Return...
		return $_newValue;
	}

	/**
	 * Similar to {@link \Kisma\Kisma::o} except it will pull a value from a nested array.
	 *
	 * @param array	  $options
	 * @param string	 $key
	 * @param string	 $subKey
	 * @param mixed	  $defaultValue Only applies to target value
	 * @param boolean	$unsetValue   Only applies to target value
	 *
	 * @return mixed
	 */
	public static function oo( &$options = array(), $key, $subKey, $defaultValue = null, $unsetValue = false )
	{
		return self::o( self::o( $options, $key, array() ), $subKey, $defaultValue, $unsetValue );
	}

	/**
	 * Alias for {@link \Kisma\Kisma::so}
	 *
	 * @param array  $options
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	public static function setOption( &$options = array(), $key, $value = null )
	{
		return self::so( $options, $key, $value );
	}

	/**
	 * Sets an value in the given array at key.
	 *
	 * @param array|object $options
	 * @param string	   $key
	 * @param mixed|null   $value
	 *
	 * @return mixed The new value of the key
	 */
	public static function so( &$options = array(), $key, $value = null )
	{
		if ( is_array( $options ) )
		{
			return $options[$key] = $value;
		}
		else if ( is_object( $options ) )
		{
			return $options->$key = $value;
		}

		return null;
	}

	/**
	 * Alias of {@link \Kisma\Kisma::unsetOption}
	 *
	 * @param array  $options
	 * @param string $key
	 *
	 * @return mixed The last value of the key
	 */
	public static function unsetOption( &$options = array(), $key )
	{
		return self::uo( $options, $key );
	}

	/**
	 * Unsets an option in the given array
	 *
	 * @param array  $options
	 * @param string $key
	 *
	 * @return mixed The new value of the key
	 */
	public static function uo( &$options = array(), $key )
	{
		return self::o( $options, $key, null, true );
	}

}

/**
 * K
 * An alias to the Kisma base
 */
class K extends Kisma implements AppConfig
{
}
