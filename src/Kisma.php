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

//	Include Silex if not already
if ( !@class_exists( 'Silex\\Application', false ) )
{
	/** @noinspection PhpIncludeInspection */
	require_once dirname( __DIR__ ) . '/vendor/silex/silex.phar';
}

//	And our base junk
require_once __DIR__ . '/Kisma/enums.php';
require_once __DIR__ . '/Kisma/Components/Seed.php';

//*************************************************************************
//* Aliases
//*************************************************************************

use Silex\Application;

use Kisma\Provider;
use Kisma\Components as Components;
use Kisma\Utility as Utility;

use Monolog;

/**
 * The Kisma bootstrap loader
 *
 * Contains a few core functions implemented statically to be lightweight and single instance.
 */
class Kisma extends Components\Seed implements AppConfig
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Silex\Application|\Kisma\Kisma The $app
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
	/**
	 * @var bool Indicates the SAPI mode
	 */
	protected $_isCli = false;

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
		$_startTime = microtime( true );

		//	Determine our running mode
		if ( $options instanceof \Silex\Application )
		{
			$_args = func_get_args();
			self::$_app = array_shift( $_args );
			$options = ( empty( $_args ) ? array() : $_args );
		}
		else if ( is_array( $options ) && isset( $options['app'] ) && $options['app'] instanceof \Silex\Application )
		{
			self::$_app = $options['app'];
			unset( $options['app'] );
		}
		else
		{
			self::$_app = new \Silex\Application();
		}

		//	Add our namespace and path
		self::$_app[K::Autoloader]->registerNamespace( 'Kisma', __DIR__ );
		self::$_app[K::BasePath] = __DIR__;

		//	Call poppa
		parent::__construct( $options );

		//	Set any configuration options that are passed in...
		$this->_loadConfiguration( $options );

		//	Auto-subscribe for my handlers
		Utility\Events::subscribe( self::$_app );

		//	Dispatch initialize...
		$this->dispatch( Event\ApplicationEvent::Initialize );

		$_endTime = microtime( true );

		Utility\Log::trace(
			'Kisma operational velocity achieved in ' . number_format( $_endTime - $_startTime, 4 ) . 's'
		);
	}

	/**
	 * Destructor chosen!
	 */
	public function __destruct()
	{
		//	Trigger the onTerminate event
		$this->dispatch( Event\ApplicationEvent::Terminate );

		//	Dump metrics
		self::log( 'Cycle metrics: ' . print_r( self::$_metrics, true ) );
	}

	/**
	 * Routes method calls from me to Silex
	 *
	 * @param string	 $name
	 * @param array|null $arguments
	 *
	 * @return mixed
	 * @throws KismaException
	 */
	public function __call( $name, $arguments )
	{
		if ( method_exists( self::$_app, $name ) )
		{
			return call_user_func_array( array( self::$_app, $name ), $arguments );
		}

		throw new KismaException( 'Method "' . $name . '" does not exist.' );
	}

	/**
	 * Merges an array of data into an app-stored array. App data will
	 * be overwritten by duplicate keys per the rules of array_merge.
	 *
	 * @param string $key
	 * @param array  $data
	 *
	 * @return array The result of the merge
	 */
	public function merge( $key, $data = array() )
	{
		self::$_app[$key] = $_product = array_merge(
			(array)self::app( $key, array() ),
			$data
		);

		return $_product;
	}

	/**
	 * Like merge, appends a string to a previously app-stored string.
	 * Always appended, never overwritten
	 *
	 * @param string $key
	 * @param string $data
	 *
	 * @return array The result of the append
	 */
	public function append( $key, $data = null )
	{
		return self::$_app[$key] = ( self::app( $key ) . $data );
	}

	/**
	 * Registers multiple services at once
	 *
	 * Format of parameters is:
	 *
	 *		 array(
	 *			 [app_key] => array( serviceOptions )
	 *			 ...
	 *		 )
	 *
	 *	 or
	 *
	 *		 array(
	 *			 [service_class_name] => array( array( classOptions ), array( serviceOptions ) )
	 *			 ...
	 *		 )
	 *
	 * Each service received will be registered as so:
	 *
	 *		$app->register( new [service_class_name]( classOptions ), serviceOptions );
	 *
	 * Example:
	 *
	 *	 $app->registerServices(
	 *		 array(
	 *			 //	Single set of options
	 *			 'ScrubServiceProvider' => array( 'x' => 'y', 'z' => 'z', '1' => '2' ),
	 *			 'AwesomeServiceProvider' => array( 'b' => 'c', 'f' => 'w', '7' => '6' ),
	 *			 'WeakServiceProvider' => array( 'd' => 'e', 'h' => 'g', '3' => '4' ),
	 *
	 *			 //	Double options
	 *			 'StaleServiceProvider' => array(
	 *				//	Class options
	 *				 0 => array( 'd' => 'e', 'h' => 'g', '3' => '4' ),
	 *				//	Service options
	 *				 1 => array( 'd' => 'e', 'h' => 'g', '3' => '4' ),
	 *			),
	 *
	 *			 //	etc...
	 *		 ),
	 *	 ));
	 *
	 * @param array $services
	 */
	public function registerServices( $services = array() )
	{
		if ( empty( $services ) || !is_array( $services ) )
		{
			throw new \InvalidArgumentException( '$services must be an array.' );
		}

		foreach ( $services as $_service )
		{
			foreach ( $_service as $_serviceClass => $_options )
			{
				if ( 2 == count( $_options ) && Utility\Scalar::is_array( $_options[0], $_options[1] ) )
				{
					$_classOptions = $_options[0];
					$_serviceOptions = $_options[1];
				}
				else
				{
					$_classOptions = $_options;
				}

				//	Register
				self::$_app->register(
					new $_serviceClass( isset( $_classOptions ) ? $_classOptions : null ),
					isset( $_serviceOptions ) ? $_serviceOptions : array()
				);
			}
		}
	}

	/**
	 * Dispatches an application event
	 *
	 * @param string								   $eventName
	 * @param \Symfony\Component\EventDispatcher\Event $event
	 */
	public function dispatch( $eventName, $event = null )
	{
		self::$_app[self::Dispatcher]->dispatch(
			$eventName,
			$event ? : new Event\ApplicationEvent( self::$_app )
		);
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
		$this->dispatch( Event\RenderEvent::BeforeRender, $_renderEvent );

		$_renderEvent->setOutput(
			$_output = self::$_app['twig']->render( $viewFile, $_payload )
		);

		$this->dispatch( Event\RenderEvent::AfterRender, $_renderEvent );

		if ( false !== $returnString )
		{
			return $_output;
		}

		echo $_output;
	}

	/**
	 * @static
	 *
	 * @param string|null	   $service
	 * @param mixed|null		$defaultValue
	 * @param bool			  $setValue If true, $app[$service] will be set to $defaultValue
	 *
	 * @return \Silex\Application|\Kisma\Kisma|mixed
	 */
	public static function app( $service = null, $defaultValue = null, $setValue = false )
	{
		if ( false !== $setValue )
		{
			self::$_app[$service] = $defaultValue;
			return self::$_app;
		}

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
	 * @param string	 $message
	 * @param string	 $level
	 * @param array|null $context
	 * @param bool	   $echo
	 *
	 * @return
	 */
	public static function log( $message, $level = LogLevel::Info, $context = array(), $echo = false )
	{
		if ( isset( self::$_app[K::Logger] ) && false === $echo )
		{
			self::$_app[K::Logger]->{$level}( $message, $context );
			return;
		}

		if ( isset( self::$_app['app.config.full_log_file_name'] ) )
		{
			error_log( message . PHP_EOL, 3, self::$_app['app.config.full_log_file_name'] );
		}
		else
		{
			error_log( $message . PHP_EOL );
		}

		if ( false !== $echo )
		{
			echo $message . ( 'cli' == PHP_SAPI ? PHP_EOL : '<br/>' );
		}

		return;
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
		if ( 'cli' == PHP_SAPI || !$this->serviceEnabled( 'twig' ) )
		{
			//			Utility\Log::trace( 'Twig service disabled by configuration.' );
		}
		else
		{
			self::$_app[K::Autoloader]->registerPrefix( 'Twig', self::$_app['vendor_path'] . '/twig/lib' );

			$_viewPath = self::app( 'app.config.view_path' );

			if ( !is_array( $_viewPath ) )
			{
				$_viewPath = array(
					$_viewPath,
				);
			}

			$_cachePath = self::app( self::$_app['app.config.cache_path'], '/tmp/_app.twig.cache' );
			$_twigOptions = self::app( 'twig.options', array() );

			//	Set some defaults
			if ( !isset( $_twigOptions['twig.path'] ) )
			{
				$_twigOptions['twig.path'] = $_viewPath;
			}
			else
			{
				if ( !isset( $_twigOptions['cache'] ) )
				{
					$_twigOptions['cache'] = array(
						'cache' => '/tmp/_app.twig.cache',
					);
				}

				if ( !isset( $_twigOptions['twig.class_path'] ) )
				{
					$_twigOptions['twig.class_path'] = self::$_app['vendor_path'] . '/twig/lib';
				}

				$_twigOptions = array(
					'twig.path' => $_viewPath,
					'twig.options' => array(
						'cache' => $_cachePath,
					),
				);
			}

			//	Add system view path if not there
			if ( !in_array( self::$_app['system_view_path'], $_twigOptions['twig.path'] ) )
			{
				$_twigOptions['twig.path'][] = self::$_app['system_view_path'];
			}

			//	Register Twig
			self::$_app->register( new \Silex\Provider\TwigServiceProvider(), $_twigOptions );

			Utility\Log::trace( 'Twig service registered' );

			//	Register widget service
			self::$_app->register(
				new Provider\WidgetServiceProvider(),
				self::app( 'widget.options', array() )
			);

			Utility\Log::trace( 'Widget service registered' );
		}

		if ( !$this->serviceEnabled( 'error_handler' ) )
		{
			//			Utility\Log::trace( 'Error handler service disabled by configuration.' );
		}
		else
		{
			$_self = self::$_app;

			//
			//	Tell Silex to let us in on the fun
			//
			self::$_app->error(
				function( $exception, $code ) use ( $_self )
				{
					return Components\ErrorHandler::onException( new \Kisma\Event\ErrorEvent( $_self, $exception ) );
				}
			);
		}

		//
		//	Finally, register any controllers we can find
		//
		$this->_registerControllers();

		//		Utility\Log::trace( 'onInitialize() complete' );

		return true;
	}

	/**
	 * Called when the app is ending :(
	 *
	 * @return bool
	 */
	public function onTerminate()
	{
		return true;
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
		$_appRoot = self::$_app['app.config.app_root'];

		if ( false === ( $_logService = self::app( 'app.config.service.enable.monolog', true ) ) )
		{
			//			Utility\Log::trace( 'Monolog service disabled by configuration.' );
			return;
		}

		if ( !is_string( $_logService ) )
		{
			$_logService = '\\Silex\\Provider\\MonologServiceProvider';
		}

		$_logPath = Utility\FileSystem::makePath(
			$_appRoot,
			self::app( 'app.config.log_path', '/logs' ),
			false
		);

		//	Make sure the directory is there
		if ( !is_dir( $_logPath ) )
		{
			@@mkdir( $_logPath, 0775, true );
		}

		$_logFileName =
			Utility\FileSystem::makePath( $_logPath, self::app( 'app.config.log_file_name', 'web.app.log' ), false );

		$_monologOptions = array_merge(
			self::app( 'monolog.options', array() ),
			array(
				'monolog.logfile' => $_logFileName,
				'monolog.class_path' => realpath( self::$_app['vendor_path'] . '/silex/vendor/monolog/src' ),
				'monolog.name' => isset( self::$_app['app.config.app_name'] ) ? self::$_app['app.config.app_name'] :
					'kisma',
			)
		);

		self::$_app->register( new $_logService(), array(
			'monolog.logfile' => $_logFileName,
			'monolog.class_path' => realpath( self::$_app['vendor_path'] . '/silex/vendor/monolog/src' ),
			'monolog.name' => isset( self::$_app['app.config.app_name'] ) ? self::$_app['app.config.app_name'] :
				'kisma',
		) );

		if ( false !== Utility\Option::get( $_monologOptions, 'fire_php', false ) )
		{
			$_firePhp = new \Monolog\Handler\FirePHPHandler();
			self::$_app['monolog']->pushHandler( $_firePhp );
			Utility\Log::debug( 'FirePHP handler registered.' );
		}

		self::$_app['app.config.full_log_file_name'] = $_logFileName;
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
			'app_version' => self::$_app->app( 'app.config.app_version', 'Kisma Framework v ' . self::Version ),
			'page_date' => date( 'Y-m-d H:i:s' ),
			'vendor_path' => self::$_app['vendor_path'],
			'topbar' => self::app( 'app.config.topbar' ),
		);

		return array_merge( $_payload, $additional );
	}

	/**
	 * Registers all the controllers found with Silex
	 *
	 * @return bool
	 */
	protected function _registerControllers()
	{
		if ( !$this->serviceEnabled( 'auto_discover_controllers' ) )
		{
			//			Utility\Log::trace( 'Controller discovery disabled by configuration.' );
			return false;
		}

		$_controllerPattern = self::app( 'app.config.controller_pattern', 'Controller.php' );
		$_controllerPath = self::app( 'app.config.controller_path' );
		$_viewPath = self::app( 'app.config.view_path' );
		$_appControllers = self::app( 'app.config.controllers', array() );

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

			//	Does it match the pattern?
			//@todo make full regex
			if ( false === strpos( $_entry, $_controllerPattern ) )
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

			//	Was a class name specified?
			if ( empty( $_appControllers ) || null === ( $_class = Utility\Option::get( $_appControllers, 'class' ) ) )
			{
				//	Nope, build it.
				$_class =
					key( $this->_namespace ) . '\\Controllers\\' . str_ireplace( '.php', null,
						basename( $_entryPath ) );
			}

			//	Mount the route binding this class
			self::$_app->mount( '/' . $_route, new $_class() );

			//	Make a view path and tell Twig
			if ( !isset( self::$_app['app.config.route_view_path'] ) )
			{
				self::$_app['app.config.route_view_path'] = array();
			}

			$_routeViewPath = Utility\FileSystem::makePath( $_viewPath, $_route, false );

			if ( is_dir( $_routeViewPath ) )
			{
				self::$_app['app.config.route_view_path'][$_route] = $_routeViewPath;
				self::$_app['twig.loader.filesystem']->addPath( $_routeViewPath );
			}
		}

		//	If there is a default route, set it up as well.
		if ( isset( self::$_app['app.config.default_controller'] ) )
		{
			self::$_app->match(
				'/',
				function( \Silex\Application $app )
				{
					$_redirectUri = '/' . $app['app.config.default_controller'] . '/';
					return $app->redirect( $_redirectUri );
				}
			);
		}

		//	And asset manager if we're not cli...
		//@todo integrate assetic asset management
		if ( 'cli' != PHP_SAPI )
		{
			//			self::$_app[K::Autoloader]->registerNamespace( 'Assetic', self::$_app['vendor_path'] . '/assetic/src' );
			//
			//			self::$_app->register(
			//				new \Kisma\Provider\AssetManagerServiceProvider(),
			//				array(
			//					'assetic.options' => self::app( 'app.config.assetic.options', array() ),
			//				)
			//			);
		}
		return true;
	}

	/**
	 * @param array $options
	 */
	protected function _loadConfiguration( $options = array() )
	{
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
		$this->_isCli = ( 'cli' == PHP_SAPI );

		//	Load all files in the app config directory
		$_appRoot = realpath( self::app( 'app.config.app_root' ) );
		$this->_namespaceName = self::app( 'app.config.app_namespace' );

		$_configPath = self::app( 'app.config.config_path', '/config' );
		//		self::$_app['app.config.config_path'] =
		//			Utility\FileSystem::makePath( self::app( 'app.config.config_path', '/config' ) );
		//
		if ( empty( $_configPath ) )
		{
			throw new BogusPropertyException( 'Configuration path not specified or invalid. Set "config_path" in your app.config.php file.' );
		}

		self::$_app['app.config.config_path'] = $_configPath;
		$_configFilePattern = self::app( 'app.config.config_file_pattern', '/*.config.php' );
		$_configGlob = $_configPath . $_configFilePattern;
		$_configs = @\glob( $_configGlob );

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
							self::$_app['app.config.namespace_root'] =
								Utility\FileSystem::makePath( current( $this->_namespace ), $this->_namespaceName );
							break;

						//	Make full paths from relatives...
						case '_path' == substr( $_key, strlen( $_key ) - 5 ):
							if ( !empty( $_value ) && '/' !== $_value[0] )
							{
								$_value =
									Utility\FileSystem::makePath( self::$_app['app.config.namespace_root'], $_value );
							}
							break;
					}

					//	Don't prepend config name to '@' prefixed keys
					if ( '@' == $_key[0] )
					{
						$_key = ltrim( $_key, '@' );
						self::$_app[$_key] = $_value;

						//	Remove from config-level
						$_config = self::$_app[$_baseName];
						unset( $_config[$_key] );
						self::$_app[$_baseName] = $_config;
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
			self::$_app['app.config.view_path'] = $_configPath = Utility\FileSystem::makePath(
				self::$_app['app.config.namespace_root'],
				'Views'
			);
		}

		if ( !isset( self::$_app['app.config.document_path'] ) )
		{
			self::$_app['app.config.document_path'] = $_configPath = Utility\FileSystem::makePath(
				self::$_app['app.config.namespace_root'],
				'Documents'
			);
		}

		if ( !isset( self::$_app['app.config.model_path'] ) )
		{
			self::$_app['app.config.model_path'] = $_configPath = Utility\FileSystem::makePath(
				self::$_app['app.config.namespace_root'],
				'Models'
			);
		}

		if ( !isset( self::$_app['app.config.controller_path'] ) )
		{
			self::$_app['app.config.controller_path'] = $_configPath = Utility\FileSystem::makePath(
				self::$_app['app.config.namespace_root'],
				'Controllers'
			);
		}
	}

	/**
	 * Returns if a service is enabled. Services are enabled by default unless explicitly set to "false"
	 *
	 * @param string $serviceName
	 *
	 * @return bool
	 */
	public function serviceEnabled( $serviceName )
	{
		return ( false !== self::app( 'app.config.service.enable.' . $serviceName, true ) );
	}
}

/**
 * K
 * An alias to the Kisma base
 */
class K extends \Kisma\Kisma
{
}