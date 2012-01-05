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
use \Kisma\Utility\Property;
use \Kisma\Utility\Events;
use \Kisma\Utility\Option;

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
	 * @var string The version number of Kisma
	 */
	const Version = '1.0.0alpha';
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
	/**
	 * @var string The Assetic asset manager
	 */
	const AssetManager = 'asset_manager';

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * This object
	 *
	 * @var \Kisma\Kisma
	 */
	protected static $_app = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Construct our app and set our event handler
	 *
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
		//	Save me
		self::$_app = $this;

		//	Call poppa
		parent::__construct();

		//	Some initialization
		$this['base_path'] = realpath( __DIR__ . '/../' );
		$this['system_view_path'] = realpath( __DIR__ . '/Kisma/Views' );
		$this['vendor_path'] = $_vendorPath = realpath( __DIR__ . '/../vendor' );
		$this['is_cli'] = ( 'cli' == php_sapi_name() );

		//	Add our required namespaces
		$this[self::Autoloader]->registerNamespaces(
			array(
				'Kisma' => __DIR__,
			)
		);

		//	Set any configuration options that are passed in...
		$this->_loadConfiguration( $options );

		//	Auto-subscribe for my handlers
		Events::subscribe( $this );

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
		if ( !isset( $this['twig'] ) )
		{
			//	No twig? No go...
			return;
		}

		$_output = $this['twig']->render( $viewFile, $this->_getBaseRenderPayload( $payload ) );

		if ( false !== $returnString )
		{
			return $_output;
		}

		echo $_output;
	}

	/**
	 * Returns an array of standard values passed to all views
	 *
	 * @param array $additional
	 *
	 * @return array
	 */
	protected function _getBaseRenderPayload( $additional = array() )
	{
		$_payload = array(
			'app_name' => $this['app.config.app_name'],
			'app_root' => $this['app.config.app_root'],
			'app_version' => $this->app( 'app.config.app_version', 'Kisma Framework v ' . self::Version ),
			'page_date' => date( 'Y-m-d H:i:s' ),
			'vendor_path' => $this['vendor_path'],
		);

		return array_merge( $_payload, $additional );
	}

	/**
	 * @static
	 *
	 * @param string|null $service
	 * @param null		$defaultValue
	 *
	 * @return \Silex\Application|\Kisma\Kisma|\Symfony\Component\EventDispatcher\EventDispatcher|\Silex\ServiceProviderInterface|\Silex\ControllerProviderInterface
	 */
	public static function app( $service = null, $defaultValue = null )
	{
		if ( null === $service )
		{
			return self::$_app;
		}

		if ( null !== $defaultValue && !isset( self::$_app[$service] ) )
		{
			return $defaultValue;
		}

		return self::$_app[$service];
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * Registers all the controllers found with Silex
	 */
	protected function _registerControllers()
	{
		$_controllerPath =
			isset( $this['app.config.controller_path'] ) ? $this['app.config.controller_path'] : '/controllers';
		$_path = rtrim( \Kisma\Kisma::app( 'app.config.app_root' ), '/' ) . $_controllerPath;

		//	Get the list of directories within the controller path
		$_directory = \dir( $_path );

		/** @var $_entry string */
		while ( false !== ( $_entry = $_directory->read() ) )
		{
			$_entryPath = $_path . '/' . $_entry;

			//	Skip the dirs and unreadables
			if ( !is_file( $_entryPath ) || !is_readable( $_entryPath ) )
			{
				continue;
			}

			require_once $_entryPath;

			$_route =
				lcfirst( Utility\Inflector::camelize( str_ireplace( 'Controller.php', null,
					basename( $_entryPath ) ) ) );
			$_class = str_ireplace( '.php', null, basename( $_entryPath ) );

			$this->mount( '/' . $_route, new $_class() );

			//	Add to view path
			$this['twig.loader.filesystem']->addPath( $this['app.config.app_root'] . $this['app.config.view_path'] . '/' . $_route );
		}

		//	If there is a default route, set it up as well.
		if ( isset( $this['app.config.default_controller'] ) )
		{
			$this->match( '/', function( \Silex\Application $app )
			{
				$app->redirect( '/' . $app['app.config.default_controller'] );
			} );
		}

		//	And asset manager if we're not cli...
		if ( !$this['is_cli'] )
		{
			//			$this[self::Autoloader]->registerNamespace( 'Assetic', $this['vendor_path'] . '/assetic/src' );
			//
			//			$this->register(
			//				new \Kisma\Components\AssetManager(),
			//				array(
			//					'assetic.options' => $this['app.config.assetic.options']
			//				)
			//			);
		}
	}

	/**
	 * @param array $options
	 */
	protected function _loadConfiguration( $options = array() )
	{
		if ( is_array( $options ) && !empty( $options ) )
		{
			foreach ( $options as $_key => $_value )
			{
				$this[$_key] = $_value;
			}
		}

		//	Load all files in the app config directory
		if ( isset( $this['app.config.app_root'] ) )
		{
			$this['app.config.app_root'] = realpath( $this['app.config.app_root'] );

			$_configs = glob( $this['app.config.app_root'] . self::app( 'app.config.config_path', '/config' ) . '/*' );

			foreach ( $_configs as $_config )
			{
				$_baseName = str_ireplace( '.php', null, basename( $_config ) );
				$this[$_baseName] = include( $_config );

				//	If there are any items, add to individual settings
				if ( isset( $this[$_baseName] ) && !empty( $this[$_baseName] ) )
				{
					foreach ( $this[$_baseName] as $_key => $_value )
					{
						//	Don't prepend config name to '@' prefixed keys
						if ( '@' == $_key[0] )
						{
							$_key = ltrim( $_key, '@' );
							$this[$_key] = $_value;

							//	Remove from config-level
							unset( $this[$_baseName][$_key] );
						}
						//	Append key name to base name and save
						else
						{
							$this[$_baseName . '.' . $_key] = $_value;
						}
					}
				}
			}
		}
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

		$this['app.config.log_path'] = $_logPath = self::app( 'app.config.log_path', $this['base_path'] );
		$_logFileName = $_logPath . '/' . self::app( 'app.config.log_file_name', 'web.app.log' );

		$this->register( new \Silex\Provider\MonologServiceProvider(), array(
			'monolog.logfile' => $_logFileName,
			'monolog.class_path' => $this['vendor_path'] . '/silex/vendor/monolog/src',
			'monolog.name' => isset( $this['app.config.app_name'] ) ? $this['app.config.app_name'] : 'kisma',
		) );

		//	Initialize the view renderer
		if ( false === $this['is_cli'] )
		{
			$this[self::Autoloader]->registerPrefix( 'Twig', __DIR__ . '/../vendor/twig/lib' );

			$_twigOptions = self::app( 'twig.options', array() );

			if ( empty( $_twigOptions ) )
			{
				$_basePath = $this['app.config.app_root'];
				$_viewPath = $_basePath . $this['app.config.view_path'];

				$_twigOptions = array(
					'twig.path' => array(
						$_viewPath,
						$this['system_view_path'],
					),
					'twig.options' => array(
						'cache' => $this['app.config.app_root'] . '/cache',
					),
				);
			}

			if ( !isset( $_twigOptions['twig.class_path'] ) )
			{
				$_twigOptions['twig.class_path'] = $this['vendor_path'] . '/twig/lib';
			}

			$this->register( new \Silex\Provider\TwigServiceProvider(), $_twigOptions );
		}

		\set_error_handler( function( $code, $message ) use ( $_self )
		{
			\Kisma\Components\ErrorHandler::onError( new \Kisma\Event\ErrorEvent( $_self, $code, $message ) );
			return false;
		} );

		\set_exception_handler( function( $exception ) use ( $_self )
		{
			\Kisma\Components\ErrorHandler::onException( new \Kisma\Event\ErrorEvent( $_self, $exception ) );
			return false;
		} );

		//	Set up error handling
		$this->error( function( $exception, $code ) use ( $_self )
		{
			\Kisma\Components\ErrorHandler::onException( new \Kisma\Event\ErrorEvent( $_self, $exception ) );
			return false;
		} );

		//		echo $this['xyz'];

		$this->_registerControllers();

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
	//* Array/Option Helpers
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
		return Option::o( $options, $key, $defaultValue, $unsetValue );
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
		return Option::o( $options, $key, $defaultValue, $unsetValue );
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
		return Option::o( Option::o( $options, $key, array() ), $subKey, $defaultValue, $unsetValue );
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
		return Option::so( $options, $key, $value );
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
		return Option::so( $options, $key, $value );
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
		return Option::uo( $options, $key );
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
		return Option::o( $options, $key, null, true );
	}

	//*************************************************************************
	//* Logging Helper
	//*************************************************************************

	/**
	 * @static
	 *
	 * @param string $message
	 * @param string $level
	 * @param bool   $echo
	 */
	public static function log( $message, $level = LogLevel::Info, $echo = false )
	{
		error_log( $message . PHP_EOL );

		$_app = self::app();

		/** @var $_logger \Monolog\Logger */
		if ( !isset( $_app[self::Logger] ) || false !== $echo )
		{
			error_log( $message . PHP_EOL );

			echo $message . ( self::app( 'is_cli', false ) ? PHP_EOL : '<br/>' );

			if ( !$_logger )
			{
				return;
			}
		}

		$_app[self::Logger]->{$level}( $message );
	}

}

/**
 * K
 * An alias to the Kisma base
 */
class K extends Kisma implements AppConfig
{
}
