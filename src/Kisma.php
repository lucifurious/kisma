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
use \Kisma\Provider;
use \Kisma\Utility;

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
	const ErrorHandler = 'error_handler';
	/**
	 * @var string System logger
	 */
	const Logger = 'monolog';
	/**
	 * @var string Twig service
	 */
	const Twig = 'twig';

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * This object
	 *
	 * @var \Kisma\Kisma
	 */
	public static $_app = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Construct our app and set our event handler
	 */
	public function __construct()
	{
		parent::__construct();

		//	Save me
		self::$_app = $this;

		//	Set base path
		$this['base_path'] = __DIR__ . '/..';

		//	Add our event handler
		Utility\Events::subscribe( $this );

		//	Dispatch initialize...
		$this->dispatch( Event\ApplicationEvent::Initialize );
	}

	/**
	 * Destructor chosen!
	 */
	function __destruct()
	{
		$this->dispatch( \Kisma\Event\ApplicationEvent::Terminate );
	}

	/**
	 * Dispatches an application event
	 *
	 * @param string								   $eventName
	 * @param \Symfony\Component\EventDispatcher\Event $event
	 */
	public function dispatch( $eventName, $event = null )
	{
		if ( null === $event )
		{
			$event = new Event\ApplicationEvent( $this );
		}

		self::app( self::Dispatcher )->dispatch( $eventName, $event );
	}

	/**
	 * Renders a Twig template view
	 *
	 * @param string $viewFile The name of the view file to render
	 * @param array  $payload The data to pass to the view
	 * @param bool   $returnString
	 *
	 * @return string
	 */
	public function render( $viewFile, $payload = array(), $returnString = false )
	{
		$_output = $this['twig']->render( $viewFile, $payload );

		if ( false !== $returnString )
		{
			return $_output;
		}

		echo $_output;
	}

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * Called after this application is constructed and is ready to begin
	 *
	 * @return bool
	 */
	public function onInitialize()
	{
		$_self = $this;

		//	Add our required namespaces
		$this[self::Autoloader]->registerNamespaces( array(
			'Kisma' => __DIR__, 'SilexExtension' => __DIR__ . '/../vendor/silex-extension/src'
		) );

		//@todo	Read configuration file...
		$this[self::AppConfig] = $this->share( function ()
		{
			return new \Kisma\Provider\AppConfigServiceProvider();
		} );

		//	Some initialization
		$this['is_cli'] = ( 'cli' == php_sapi_name() );

		//	Set up error handling
		$this[self::ErrorHandler] = $this->protect( function() use ( $_self )
		{
			\set_error_handler( function( $code, $message ) use ( $_self )
			{
				Kisma::app( self::Dispatcher )
					->dispatch( 'error', new \Kisma\Event\ErrorEvent( $_self, $code, $message ) );
			} );

			\set_exception_handler( function( $exception ) use ( $_self )
			{
				Kisma::app( self::Dispatcher )->dispatch( 'error', new \Kisma\Event\ErrorEvent( $_self, $exception ) );
			} );

			return new \Kisma\Components\ErrorHandler( self::app() );
		} );

		$_logPath = isset( $this['app.config.log_path'] ) ? $this['app.config.log_path'] : $this['base_path'];
		$_logFileName =
			$_logPath . '/' . ( isset( $this['app.config.log_file_name'] ) ? $this['app.config.log_file_name'] :
				'kisma.log' );

		$this->register( new \Silex\Provider\MonologServiceProvider(), array(
			'monolog.logfile' => $_logFileName,
			'monolog.class_path' => $this['base_path'] . '/vendor/silex/vendor/monolog/src',
			'monolog.name' => isset( $this['app.config.app_name'] ) ? $this['app.config.app_name'] : 'kisma',
		) );

		self::log( 'Initialization complete.' );

		return true;
	}

	/**
	 * Called when the app is ending :(
	 */
	public function onTerminate()
	{
		//	Restore error handlers
		\restore_error_handler();
		\restore_exception_handler();
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

	/**
	 * @static
	 *
	 * @param string|null $service
	 *
	 * @return \Silex\Application|\Kisma\Kisma|\Symfony\Component\EventDispatcher\EventDispatcher|\Silex\ServiceProviderInterface|\Silex\ControllerProviderInterface
	 */
	public static function app( $service = null )
	{
		if ( null === $service )
		{
			return self::$_app;
		}

		return self::$_app[$service];
	}

	/**
	 * @static
	 *
	 * @param string $message
	 * @param string $level
	 * @param bool   $echo
	 */
	public static function log( $message, $level = LogLevel::Info, $echo = false )
	{
		$_logger = self::app( self::Logger );

		/** @var $_logger \Monolog\Logger */
		if ( false !== $echo )
		{
			echo $message;
		}

		$_logger->{$level}( $message );
	}

}

/**
 * K
 * An alias to the Kisma base
 */
class K extends Kisma implements AppConfig
{
}
