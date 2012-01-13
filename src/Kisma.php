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

require_once __DIR__ . '/../vendor/silex/silex.phar';
require_once __DIR__ . '/Kisma/enums.php';

//*************************************************************************
//* Aliases
//*************************************************************************

use \Silex;
use \Kisma\Event;
use \Kisma\Provider;
use \Kisma\Utility\FileSystem;
use \Kisma\Utility\Property;
use \Kisma\Utility\Events;
use \Kisma\Utility\Option;
use \Kisma\Utility\Http;

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
	const Version = '1.0.0-RC1';
	/**
	 * @var string
	 */
	const AppConfig = 'app.config';
	/**
	 * @var string
	 */
	const ViewConfig = 'view.config';
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
	/**
	 * @var array This app's namespace map
	 */
	protected $_namespace = null;

	/**
	 * @var string The namespace root
	 */
	protected $_namespaceName = null;

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
		$this[AppConfig::BasePath] = __DIR__;

		//	Call poppa
		parent::__construct();

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
	public function __destruct()
	{
		$this->dispatch( Event\ApplicationEvent::Terminate );
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
	 * @param string $viewFile The name of the view file or view tag to render
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

		$_output = $this['twig']->render( $viewFile, $this->_getBaseRenderPayload( $viewFile, $payload ) );

		if ( false !== $returnString )
		{
			return $_output;
		}

		echo $_output;
	}

	/**
	 * @static
	 *
	 * @param string|null $service
	 * @param null		$defaultValue
	 *
	 * @return \Silex\Application|\Kisma\Kisma|\Symfony\Component\EventDispatcher\EventDispatcher|\Silex\ServiceProviderInterface|\Silex\ControllerProviderInterface|array
	 */
	public static function app( $service = null, $defaultValue = null )
	{
		if ( null === $service )
		{
			return self::$_app;
		}

		if ( !isset( self::$_app[$service] ) )
		{
			return $defaultValue;
		}

		return self::$_app[$service];
	}

	/**
	 * @param string $message
	 * @param string $level
	 * @param bool   $echo
	 *
	 * @return
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

		$_logPath = self::app( 'app.config.log_path', __DIR__, '/logs' );
		$_logFileName =
			FileSystem::makePath( $_logPath, self::app( 'app.config.log_file_name', 'web.app.log' ), false );

		$this->register( new \Silex\Provider\MonologServiceProvider(), array(
			'monolog.logfile' => $_logFileName,
			'monolog.class_path' => $this['vendor_path'] . '/silex/vendor/monolog/src',
			'monolog.name' => isset( $this['app.config.app_name'] ) ? $this['app.config.app_name'] : 'kisma',
		) );

		//	Initialize the view renderer
		if ( false === $this['is_cli'] )
		{
			$this[self::Autoloader]->registerPrefix( 'Twig', $this['vendor_path'] . '/twig/lib' );

			$_twigOptions = self::app( 'twig.options', array() );

			if ( empty( $_twigOptions ) )
			{
				$_viewPath = $this['app.config.view_path'];

				$_twigOptions = array(
					'twig.path' => array(
						$_viewPath,
						$this['system_view_path'],
					),
					'twig.options' => array(
						'cache' => $this['app.config.namespace_root'] . '/cache',
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
	public
	function onTerminate()
	{
		//	Restore error handlers
		\restore_error_handler();
		\restore_exception_handler();
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * Returns an array of standard values passed to all views
	 *
	 * @param null  $viewFile
	 * @param array $additional
	 *
	 * @return array
	 */
	protected function _getBaseRenderPayload( $viewFile = null, $additional = array() )
	{
		$additional = array_merge(
			$additional,
			self::app( 'view.defaults', array() )
		);

		if ( null !== $viewFile )
		{
			$additional = array_merge(
				$additional,
				self::app( self::ViewConfig . '.' . $viewFile, array() )
			);
		}

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
	 * Registers all the controllers found with Silex
	 */
	protected function _registerControllers()
	{
		$_appRoot =
			FileSystem::makePath( self::app( 'app.config.app_root', current( $this->_namespace ) ? : getcwd() ),
				$this->_namespaceName );

		$_controllerPath = self::app( 'app.config.controller_path' );
		$_viewPath = self::app( 'app.config.view_path' );

		//	Get the list of directories within the controller path
		$_directory = \dir( $_controllerPath );

		/** @var $_entry string */
		while ( $_directory && false !== ( $_entry = $_directory->read() ) )
		{
			$_entryPath = $_controllerPath . '/' . $_entry;

			//	Skip the dirs and unread-ables
			if ( !is_file( $_entryPath ) || !is_readable( $_entryPath ) )
			{
				continue;
			}

			require_once $_entryPath;

			$_route =
				lcfirst( Utility\Inflector::camelize( str_ireplace( 'Controller.php', null,
					basename( $_entryPath ) ) ) );

			$_class =
				key( $this->_namespace ) . '\\Controllers\\' . str_ireplace( '.php', null, basename( $_entryPath ) );
			//			$_mirror = new \ReflectionClass( $_class );
			//			$_class = $_mirror->getName();

			$this->mount( '/' . $_route, new $_class() );

			\Kisma\Utility\Log::trace( print_r( $this['routes'], true ) );

			//	Add to view path
			$this['twig.loader.filesystem']->addPath( FileSystem::makePath( $_viewPath, $_route ) );
		}

		//	If there is a default route, set it up as well.
		if ( isset( $this['app.config.default_controller'] ) )
		{
			$this->match( '/', function( \Silex\Application $app )
			{
				$_redirectUri = '/' . $app['app.config.default_controller'] . '/';
				return $app->redirect( $_redirectUri );
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
		//	Add our namespace
		$this[self::Autoloader]->registerNamespace( 'Kisma', __DIR__ );

		//	Load the options...
		if ( is_array( $options ) && !empty( $options ) )
		{
			foreach ( $options as $_key => $_value )
			{
				$this[$_key] = $_value;
			}
		}

		//	Some system-level option initialization
		$this['system_view_path'] = realpath( __DIR__ . '/Kisma/Views' );
		$this['vendor_path'] = $_vendorPath = realpath( __DIR__ . '/../vendor' );
		$this['is_cli'] = ( 'cli' == php_sapi_name() );

		//	Load all files in the app config directory
		$_appRoot = realpath( self::app( 'app.config.app_root' ) );
		$this->_namespaceName = self::app( 'app.config.app_namespace' );
		$this['app.config.namespace_root'] = $_namespaceRoot = FileSystem::makePath( $_appRoot, $this->_namespaceName );

		$this['app.config.config_path'] = $_configPath = FileSystem::makePath(
			$_namespaceRoot,
			self::app( 'app.config.config_path', '/config' )
		);

		if ( false === $_configPath )
		{
			throw new BogusPropertyException( 'Configuration path not specified or invalid. Set "config_path" in your app.config.php file.' );
		}

		$_configGlob = $_configPath . '/*.php';
		$_configs = glob( $_configGlob );

		//	Loop through the configs we found...
		foreach ( $_configs as $_config )
		{
			$_baseName = str_ireplace( '.php', null, basename( $_config ) );
			$this[$_baseName] = include( $_config );

			//	If there are any items, add to individual settings
			if ( isset( $this[$_baseName] ) && !empty( $this[$_baseName] ) && is_array( $this[$_baseName] ) )
			{
				foreach ( $this[$_baseName] as $_key => $_value )
				{
					$_cleanKey = strtolower( trim( $_key ) );

					//	Pre-config directory manipulation
					switch ( $_cleanKey )
					{
						//	Load up the namespace
						case 'namespace':
							//	Register our namespace
							$this['app.config.namespace'] = $this->_namespace = $_value;

							if ( empty( $this->_namespace ) )
							{
								throw new BogusPropertyException( 'You must specify the "namespace" option in your configuration.' );
							}

							if ( !is_array( $this->_namespace ) || !is_string( key( $this->_namespace ) ) || !is_dir( current( $this->_namespace ) ) )
							{
								throw new BogusPropertyException( 'The "namespace" option must be an array of <namespace> => </path/to/ns> mappings.' );
							}

							$this['autoloader']->registerNamespaces( $this->_namespace );
							break;

						//	Make full paths from relatives...
						case '_path' == substr( $_key, strlen( $_key ) - 5 ):
							if ( !empty( $_value ) && '/' !== $_value[0] )
							{
								$_value = FileSystem::makePath( $_namespaceRoot, $_value );
							}
							break;
					}

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

		//	Check application paths (Controllers, Models, & Views)...
		if ( !isset( $this['app.config.view_path'] ) )
		{
			$this['app.config.view_path'] = $_configPath = FileSystem::makePath(
				$_namespaceRoot,
				'Views'
			);
		}

		if ( !isset( $this['app.config.document_path'] ) )
		{
			$this['app.config.document_path'] = $_configPath = FileSystem::makePath(
				$_namespaceRoot,
				'Document'
			);
		}

		if ( !isset( $this['app.config.model_path'] ) )
		{
			$this['app.config.model_path'] = $_configPath = FileSystem::makePath(
				$_namespaceRoot,
				'Models'
			);
		}

		if ( !isset( $this['app.config.controller_path'] ) )
		{
			$this['app.config.controller_path'] = $_configPath = FileSystem::makePath(
				$_namespaceRoot,
				'Controllers'
			);
		}
	}

}

/**
 * K
 * An alias to the Kisma base
 */
class K extends Kisma implements AppConfig
{
}
