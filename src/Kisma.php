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
	//* Private Members
	//*************************************************************************

	/**
	 * @var array The library configuration options
	 */
	protected static $_options = array(
		'base_path'   => __DIR__,
		'auto_loader' => null,
		'conception'  => false,
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
			$_autoLoader = require( dirname( __DIR__ ) . '/vendor/autoload.php' );
			self::set( 'auto_loader', $_autoLoader );
		}

		//	Register our faux-destructor
		if ( false === ( $_conceived = self::get( 'conception' ) ) )
		{
			\register_shutdown_function(
				function ( $eventName = \Kisma\Core\Interfaces\Events\Kisma::Death )
				{
					\Kisma\Core\Utility\EventManager::publish( null, $eventName );
				}
			);

			//	We done baby!
			self::set( 'conception', $_conceived = true );
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
		return self::get( 'auto_loader' );
	}

	/**
	 * @return string
	 */
	public static function getBasePath()
	{
		return self::get( 'base_path' );
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
