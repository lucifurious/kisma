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
use Kisma\Core\Enums\CoreSettings;
use Kisma\Core\Events\Enums\KismaEvents;
use Kisma\Core\Utility\Detector;
use Kisma\Core\Utility\EventManager;
use Kisma\Core\Utility\Option;
use Kisma\Core\Utility\Storage;

/**
 * Top-level bootstrap class with a few core functions
 */
class Kisma
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The current version
	 * @deprecated Deprecated in 0.2.19, to be removed in 0.3.x
	 */
	const KismaVersion = '0.2.19';
	/**
	 * @var string The current version
	 */
	const KISMA_VERSION = '0.2.19';

	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var array The library configuration options
	 */
	protected static $_options = array(
		CoreSettings::BASE_PATH   => __DIR__,
		CoreSettings::AUTO_LOADER => null,
		CoreSettings::CONCEPTION  => false,
		CoreSettings::VERSION     => self::KISMA_VERSION,
		CoreSettings::NAME        => 'App',
		CoreSettings::NAV_BAR     => null,
		CoreSettings::FRAMEWORK   => null,
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
		if ( false === ( $_conceived = static::get( CoreSettings::CONCEPTION ) ) )
		{
			\register_shutdown_function(
				function ( $eventName = KismaEvents::DEATH )
				{
					\Kisma::__sleep();
					EventManager::trigger( $eventName );
				}
			);

			//	Try and detect the framework being used...
			Detector::framework();

			//	We done baby!
			static::set( CoreSettings::CONCEPTION, true );

			if ( null === static::get( CoreSettings::AUTO_LOADER ) && class_exists( '\\ComposerAutoloaderInit', false ) )
			{
				static::set( CoreSettings::AUTO_LOADER, \ComposerAutoloaderInit::getLoader() );
			}

			//	And let the world know we're alive
			EventManager::trigger( KismaEvents::BIRTH );
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
			$_SESSION[CoreSettings::SESSION_KEY] = Storage::freeze( static::$_options );
		}
	}

	/**
	 * Deserialize
	 */
	public static function __wakeup()
	{
		//	Load options from session...
		if ( isset( $_SESSION, $_SESSION[CoreSettings::SESSION_KEY] ) )
		{
			//	Merge them into the fold
			static::$_options =
				Options::merge(
					   Storage::defrost( $_SESSION[CoreSettings::SESSION_KEY] ),
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
