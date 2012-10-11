<?php
/**
 * Kisma.php
 * Bootstrap loader
 */
/**
 * Kisma
 * Contains a few core functions implemented statically to be lightweight and single instance.
 */
class Kisma implements \Kisma\Core\Interfaces\Publisher, \Kisma\Core\Interfaces\Events\Kisma
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The current version
	 */
	const KismaVersion = '0.666';

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var array The library configuration options
	 */
	protected static $_options = array(
		'app.base_path'   => __DIR__,
		'app.auto_loader' => null,
		'app.conception'  => false,
		'app.version'     => self::KismaVersion,
		'app.name'        => 'App',
		'app.navbar'      => null,
		'app.framework'   => null,
	);

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
		//	Set any passed in options...
		if ( is_callable( $options ) )
		{
			$options = call_user_func( $options );
		}

		//	Set any application-level options passed in
		self::$_options = \Kisma\Core\Utility\Option::merge( self::$_options, $options );

		if ( null === ( $_autoLoader = self::getAutoLoader() ) )
		{
			/**
			 * Set up the autoloader
			 */
			if ( file_exists( __DIR__ . '/../vendor/autoload.php' ) )
			{
				$_autoLoader = require( __DIR__ . '/../vendor/autoload.php' );
				self::set( 'app.auto_loader', $_autoLoader );
			}
		}

		//	Register our faux-destructor
		if ( false === ( $_conceived = self::get( 'app.conception' ) ) )
		{
			\register_shutdown_function(
				function ( $eventName = \Kisma\Core\Interfaces\Events\Kisma::Death )
				{
					\Kisma\Core\Utility\EventManager::publish( null, $eventName );
				}
			);

			//	Try and detect the framework being used...
			\Kisma\Core\Utility\Detector::framework();

			//	We done baby!
			self::set( 'app.conception', $_conceived = true );
		}

		//	And let the world know we're alive
		\Kisma\Core\Utility\EventManager::publish( null, self::Birth );

		return $_conceived;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	public static function set( $key, $value = null )
	{
		\Kisma\Core\Utility\Option::set( self::$_options, $key, $value );
	}

	/**
	 * @param string $key If you pass in a null, you'll get an array of all keys in return.
	 * @param mixed  $defaultValue
	 * @param bool   $removeIfFound
	 *
	 * @return mixed|array
	 */
	public static function get( $key, $defaultValue = null, $removeIfFound = false )
	{
		if ( null === $key )
		{
			return self::$_options;
		}

		return \Kisma\Core\Utility\Option::get( self::$_options, $key, $defaultValue, $removeIfFound );
	}

	//*************************************************************************
	//* Default Event Handlers
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onBirth( $event = null )
	{
		return true;
	}

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onDeath( $event = null )
	{
		return true;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @return null|\Composer\Autoload\ClassLoader
	 */
	public static function getAutoLoader()
	{
		return self::get( 'app.auto_loader' );
	}

	/**
	 * @return string
	 */
	public static function getBasePath()
	{
		return self::get( 'app.base_path' );
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
