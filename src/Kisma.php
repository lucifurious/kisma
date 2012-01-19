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
use Monolog;

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
	 * @var \Silex\Application The $app
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
		//	Save me, or passed in $app
		self::$_app = ( isset( $options, $options['app'] ) ? $options['app'] : $this );
		self::$_app[AppConfig::BasePath] = __DIR__;

		//	Call poppa
		parent::__construct();

		//	Set any configuration options that are passed in...
		self::$_app->_loadConfiguration( $options );

		//	Auto-subscribe for my handlers
		Events::subscribe( self::$_app );

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
			$event = new Event\ApplicationEvent( self::$_app );
		}

		self::$_app[self::Dispatcher]->dispatch( $eventName, $event );
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
		if ( !isset( self::$_app['twig'] ) )
		{
			//	No twig? No go...
			return;
		}

		$_payload = $this->_getBaseRenderPayload( $viewFile, $payload );
		$_renderEvent = new \Kisma\Event\RenderEvent( self::$_app, $viewFile, $_payload );
		$this->dispatch( \Kisma\Event\RenderEvent::BeforeRender, $_renderEvent );

		$_renderEvent->setOutput(
			$_output = self::$_app['twig']->render( $viewFile, $_payload )
		);

		$this->dispatch( \Kisma\Event\RenderEvent::AfterRender, $_renderEvent );

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

		/** @var $_logger \Monolog\Logger */
		if ( !isset( self::$_app[self::Logger] ) || false !== $echo )
		{
			error_log( $message . PHP_EOL );

			echo $message . ( self::app( 'is_cli', false ) ? PHP_EOL : '<br/>' );

			if ( !$_logger )
			{
				return;
			}
		}

		self::$_app[self::Logger]->{$level}( $message );
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
		$this->_initializeLogging();

		//
		//	Initialize the view renderer
		//
		if ( false === self::$_app['is_cli'] )
		{
			self::$_app[self::Autoloader]->registerPrefix( 'Twig', self::$_app['vendor_path'] . '/twig/lib' );

			$_twigOptions = self::app( 'twig.options', array() );

			if ( empty( $_twigOptions ) )
			{
				$_viewPath = self::$_app['app.config.view_path'];

				$_twigOptions = array(
					'twig.path' => array(
						$_viewPath,
						self::$_app['system_view_path'],
					),
					'twig.options' => array(
						'cache' => self::$_app['app.config.namespace_root'] . '/cache',
					),
				);
			}

			if ( !isset( $_twigOptions['twig.class_path'] ) )
			{
				$_twigOptions['twig.class_path'] = self::$_app['vendor_path'] . '/twig/lib';
			}

			//	Register Twig
			self::$_app->register( new \Silex\Provider\TwigServiceProvider(), $_twigOptions );
			\Kisma\Utility\Log::trace( 'Twig registered' );

			//	Register widget service
			self::$_app->register(
				new \Kisma\Provider\WidgetServiceProvider(),
				self::app( 'widget.options', array() )
			);

			\Kisma\Utility\Log::trace( 'Widget services registered' );
		}

		$_self = self::$_app;

		//
		//	Set error handlers
		//
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

		//
		//	Tell Silex to let us in on the fun
		//
		self::$_app->error( function( $exception, $code ) use ( $_self )
		{
			\Kisma\Components\ErrorHandler::onException( new \Kisma\Event\ErrorEvent( $_self, $exception ) );
			return false;
		} );

		//
		//	Finally, register any controllers we can find
		//
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
	//* Private Methods
	//*************************************************************************

	/**
	 * Initialize logging
	 *
	 * @todo enable/disable with option
	 */
	protected function _initializeLogging()
	{
		$_logFileName = FileSystem::makePath(
			$_logPath = self::app( 'app.config.log_path', __DIR__, '/logs' ),
			self::app( 'app.config.log_file_name', 'web.app.log' ),
			false
		);

		$_monologOptions = array_merge(
			self::app( 'monolog.options', array() ),
			array(
				'monolog.logfile' => $_logFileName,
				'monolog.class_path' => self::$_app['vendor_path'] . '/silex/vendor/monolog/src',
				'monolog.name' => isset( self::$_app['app.config.app_name'] ) ? self::$_app['app.config.app_name'] :
					'kisma',
			)
		);

		self::$_app->register( new \Silex\Provider\MonologServiceProvider(), array(
			'monolog.logfile' => $_logFileName,
			'monolog.class_path' => self::$_app['vendor_path'] . '/silex/vendor/monolog/src',
			'monolog.name' => isset( self::$_app['app.config.app_name'] ) ? self::$_app['app.config.app_name'] :
				'kisma',
		) );

		if ( false !== Option::get( $_monologOptions, 'fire_php', false ) )
		{
			$_firePhp = new \Monolog\Handler\FirePHPHandler();
			self::$_app['monolog']->pushHandler( $_firePhp );
			\Kisma\Utility\Log::debug( 'FirePHP handler registered.' );
		}
	}

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
			'app_name' => self::$_app['app.config.app_name'],
			'app_root' => self::$_app['app.config.app_root'],
			'app_version' => $this->app( 'app.config.app_version', 'Kisma Framework v ' . self::Version ),
			'page_date' => date( 'Y-m-d H:i:s' ),
			'vendor_path' => self::$_app['vendor_path'],
			'topbar' => self::app( 'app.config.topbar' ),
		);

		return array_merge( $_payload, $additional );
	}

	/**
	 * Registers all the controllers found with Silex
	 */
	protected function _registerControllers()
	{
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

			$_route = lcfirst(
				Utility\Inflector::camelize(
					str_ireplace( 'Controller.php',
						null,
						basename( $_entryPath )
					)
				)
			);

			$_class =
				key( $this->_namespace ) . '\\Controllers\\' . str_ireplace( '.php', null, basename( $_entryPath ) );

			self::$_app->mount( '/' . $_route, new $_class() );

			//	Add to view path
			self::$_app['twig.loader.filesystem']->addPath( FileSystem::makePath( $_viewPath, $_route ) );
		}

		//	If there is a default route, set it up as well.
		if ( isset( self::$_app['app.config.default_controller'] ) )
		{
			self::$_app->match( '/', function( \Silex\Application $app )
			{
				$_redirectUri = '/' . $app['app.config.default_controller'] . '/';
				return $app->redirect( $_redirectUri );
			} );
		}

		//	And asset manager if we're not cli...
		//@todo integrate assetic asset management
		if ( !self::$_app['is_cli'] )
		{
//			self::$_app[self::Autoloader]->registerNamespace( 'Assetic', self::$_app['vendor_path'] . '/assetic/src' );
//
//			self::$_app->register(
//				new \Kisma\Provider\AssetManagerServiceProvider(),
//				array(
//					'assetic.options' => self::app( 'app.config.assetic.options', array() ),
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
		self::$_app[self::Autoloader]->registerNamespace( 'Kisma', __DIR__ );

		//	Load the options...
		if ( is_array( $options ) && !empty( $options ) )
		{
			foreach ( $options as $_key => $_value )
			{
				self::$_app[$_key] = $_value;
			}
		}

		//	Some system-level option initialization
		self::$_app['system_view_path'] = realpath( __DIR__ . '/Kisma/Views' );
		self::$_app['vendor_path'] = $_vendorPath = realpath( __DIR__ . '/../vendor' );
		self::$_app['is_cli'] = ( 'cli' == php_sapi_name() );

		//	Load all files in the app config directory
		$_appRoot = realpath( self::app( 'app.config.app_root' ) );
		self::$_app->_namespaceName = self::app( 'app.config.app_namespace' );
		self::$_app['app.config.namespace_root'] = $_namespaceRoot = FileSystem::makePath( $_appRoot, $this->_namespaceName );

		self::$_app['app.config.config_path'] = $_configPath = FileSystem::makePath(
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
			self::$_app[$_baseName] = include( $_config );

			//	If there are any items, add to individual settings
			if ( isset( self::$_app[$_baseName] ) && !empty( self::$_app[$_baseName] ) && is_array( self::$_app[$_baseName] ) )
			{
				foreach ( self::$_app[$_baseName] as $_key => $_value )
				{
					$_cleanKey = strtolower( trim( $_key ) );

					//	Pre-config directory manipulation
					switch ( $_cleanKey )
					{
						//	Load up the namespace
						case 'namespace':
							//	Register our namespace
							self::$_app['app.config.namespace'] = $this->_namespace = $_value;

							if ( empty( $this->_namespace ) )
							{
								throw new BogusPropertyException( 'You must specify the "namespace" option in your configuration.' );
							}

							if ( !is_array( $this->_namespace ) || !is_string( key( $this->_namespace ) ) || !is_dir( current( $this->_namespace ) ) )
							{
								throw new BogusPropertyException( 'The "namespace" option must be an array of <namespace> => </path/to/ns> mappings.' );
							}

							self::$_app['autoloader']->registerNamespaces( $this->_namespace );
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
						self::$_app[$_key] = $_value;

						//	Remove from config-level
						unset( self::$_app[$_baseName][$_key] );
					}
					//	Append key name to base name and save
					else
					{
						self::$_app[$_baseName . '.' . $_key] = $_value;
					}
				}
			}
		}

		//	Check application paths (Controllers, Models, & Views)...
		if ( !isset( self::$_app['app.config.view_path'] ) )
		{
			self::$_app['app.config.view_path'] = $_configPath = FileSystem::makePath(
				$_namespaceRoot,
				'Views'
			);
		}

		if ( !isset( self::$_app['app.config.document_path'] ) )
		{
			self::$_app['app.config.document_path'] = $_configPath = FileSystem::makePath(
				$_namespaceRoot,
				'Document'
			);
		}

		if ( !isset( self::$_app['app.config.model_path'] ) )
		{
			self::$_app['app.config.model_path'] = $_configPath = FileSystem::makePath(
				$_namespaceRoot,
				'Models'
			);
		}

		if ( !isset( self::$_app['app.config.controller_path'] ) )
		{
			self::$_app['app.config.controller_path'] = $_configPath = FileSystem::makePath(
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
