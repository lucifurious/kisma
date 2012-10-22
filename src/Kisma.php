<?php
/**
 * Kisma.php
 * Bootstrap loader
 */
/**
 * Kisma
 * Contains a few core functions implemented statically to be lightweight and single instance.
 *
 * @method static bool getConception() Gets the conception flag
 * @method static bool setConception( bool $how ) Sets the conception flag
 * @method static mixed getDebug() Gets the debug setting( s )
 * @method static mixed setDebug( mixed $how ) Sets the debug setting( s )
 */
class Kisma implements \Kisma\Core\Interfaces\PublisherLike, \Kisma\Core\Interfaces\Events\Kisma, \Kisma\Core\Interfaces\KismaSettings
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
		self::BasePath   => __DIR__,
		self::AutoLoader => null,
		self::Conception => false,
		self::Version    => self::KismaVersion,
		self::Name       => 'App',
		self::NavBar     => null,
		self::Framework  => null,
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

		//	Register our faux-destructor
		if ( false === ( $_conceived = self::getConception() ) )
		{
			\register_shutdown_function(
				function ( $eventName = self::Death )
				{
					\Kisma\Core\Utility\EventManager::publish( null, $eventName );
				}
			);

			//	Try and detect the framework being used...
			\Kisma\Core\Utility\Detector::framework();

			//	We done baby!
			self::setConception( true );
			self::setAutoLoader( ComposerAutoloaderInit::getLoader() );

			//	And let the world know we're alive
			\Kisma\Core\Utility\EventManager::publish( null, self::Birth );
		}

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
	 * @param string $key If you pass in a null, you'll get the entire array of options
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

	/**
	 * An easy way to get Kisma settings out of the bag
	 *
	 * @param string $name
	 * @param array  $arguments
	 *
	 * @throws \BadMethodCallException
	 * @return mixed
	 */
	public static function __callStatic( $name, $arguments )
	{
		if ( \Kisma\Core\Utility\Scalar::in( $_type = strtolower( substr( $name, 0, 3 ) ), 'get', 'set' ) )
		{
			$_tag = 'app.' . \Kisma\Core\Utility\Inflector::tag( substr( $name, 3 ), true );

			if ( \Kisma\Core\Enums\KismaSettings::contains( $_tag ) )
			{
				array_unshift( $arguments, $_tag );

				return call_user_func_array( array( __CLASS__, $_type ), $arguments );
			}
		}

		throw new \BadMethodCallException( 'The method "' . $name . '" does not exist, or at least, I can\'t find it.' );
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

	/**
	 * @param Composer\Script\Event $event
	 */
	public static function postInstall( \Composer\Script\Event $event )
	{
		//	Nada
	}

	/**
	 * @param Composer\Script\Event $event
	 */
	public static function postUpdate( \Composer\Script\Event $event )
	{
		//	Nada
	}

}
