<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
 *
 * Kisma(tm) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kisma(tm) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kisma(tm).  If not, see <http://www.gnu.org/licenses/>.
 */
use Kisma\Core\Interfaces\KismaSettings;
use Kisma\Core\Interfaces\PublisherLike;
use Kisma\Core\Utility\Detector;
use Kisma\Core\Utility\EventManager;
use Kisma\Core\Utility\Inflector;
use Kisma\Core\Utility\Option;
use Kisma\Core\Utility\Scalar;

/**
 * Kisma
 * Contains a few core functions implemented statically to be lightweight and single instance.
 *
 * @method static bool getConception() Gets the conception flag
 * @method static bool setConception( bool $how ) Sets the conception flag
 * @method static mixed getDebug() Gets the debug setting( s )
 * @method static mixed setDebug( mixed $how ) Sets the debug setting( s )
 * @method static \Composer\Autoload\ClassLoader getAutoLoader()
 * @method static mixed setAutoLoader( mixed $autoLoader )
 */
class Kisma implements PublisherLike, \Kisma\Core\Interfaces\Events\Kisma, KismaSettings
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The current version
	 */
	const KismaVersion = '0.2.13';

	//*************************************************************************
	//* Members
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
	//* Methods
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

		//	Load from session...
		static::__wakeup();

		//	Set any application-level options passed in
		static::$_options = Option::merge( static::$_options, $options );

		//	Register our faux-destructor
		if ( false === ( $_conceived = static::getConception() ) )
		{
			\register_shutdown_function(
				function ( $eventName = Kisma::Death )
				{
					\Kisma::__sleep();
					EventManager::publish( null, $eventName );
				}
			);

			//	Try and detect the framework being used...
			Detector::framework();

			//	We done baby!
			static::setConception( true );

			if ( null === static::getAutoLoader() && class_exists( '\\ComposerAutoloaderInit', false ) )
			{
				static::setAutoLoader( \ComposerAutoloaderInit::getLoader() );
			}

			//	And let the world know we're alive
			EventManager::publish( null, Kisma::Birth );
		}

		return $_conceived;
	}

	/**
	 * Serialize
	 */
	public static function __sleep()
	{
		//	Save options out to session...
		if ( isset( $_SESSION ) )
		{
			$_SESSION['kisma.options'] = static::$_options;
		}
	}

	/**
	 * Deserialize
	 */
	public static function __wakeup()
	{
		//	Load options from session...
		if ( isset( $_SESSION, $_SESSION['kisma.options'] ) )
		{
			//	Merge them into the fold
			static::$_options = array_merge(
				$_SESSION['kisma.options'],
				static::$_options
			);
		}
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	public static function set( $key, $value = null )
	{
		Option::set( static::$_options, $key, $value );
	}

	/**
	 * @param string $key
	 * @param string $subKey
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	public static function addTo( $key, $subKey, $value = null )
	{
		Option::addTo( static::$_options, $key, $subKey, $value );
	}

	/**
	 * @param string $key
	 * @param string $subKey
	 *
	 * @return mixed
	 */
	public static function removeFrom( $key, $subKey )
	{
		Option::removeFrom( static::$_options, $key, $subKey );
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
			return static::$_options;
		}

		return Option::get( static::$_options, $key, $defaultValue, $removeIfFound );
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
		if ( Scalar::in( $_type = strtolower( substr( $name, 0, 3 ) ), 'get', 'set' ) )
		{
			$_tag = 'app.' . Inflector::tag( substr( $name, 3 ), true );

			if ( \Kisma\Core\Enums\KismaSettings::contains( $_tag ) )
			{
				array_unshift( $arguments, $_tag );

				return call_user_func_array( array( __CLASS__, $_type ), $arguments );
			}
		}

		throw new \BadMethodCallException( 'The method "' . $name . '" does not exist, or at least, I can\'t find it.' );
	}

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public static function onBirth( $event = null )
	{
		static::__wakeup();

		return true;
	}

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public static function onDeath( $event = null )
	{
		static::__sleep();

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
