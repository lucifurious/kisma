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
use Kisma\Core\Components\Flexistore;
use Kisma\Core\Enums\CoreSettings;
use Kisma\Core\Events\Enums\KismaEvents;
use Kisma\Core\Utility\Detector;
use Kisma\Core\Utility\EventManager;
use Kisma\Core\Utility\Inflector;
use Kisma\Core\Utility\Option;

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
     */
    const KISMA_VERSION = '0.2.57';
    /**
     * @var string The current version
     * @deprecated Deprecated in 0.2.19, to be removed in 0.3.x
     */
    const KismaVersion = self::KISMA_VERSION;

    //*************************************************************************
    //* Members
    //*************************************************************************

    /**
     * @var array Kisma global settings
     */
    private static $_options = array(
        CoreSettings::AUTO_LOADER => null,
        CoreSettings::BASE_PATH   => __DIR__,
        CoreSettings::CONCEPTION  => false,
        CoreSettings::FRAMEWORK   => null,
        CoreSettings::NAME        => 'App',
        CoreSettings::NAV_BAR     => null,
        CoreSettings::VERSION     => self::KISMA_VERSION,
    );
    /**
     * @var bool Indicates if the object is awake yet
     */
    protected static $_awake = false;
    /**
     * @var Flexistore
     */
    protected static $_cache = null;

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

        static::_initializeCache();

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
                /** @noinspection PhpUndefinedClassInspection */
                static::set( CoreSettings::AUTO_LOADER, \ComposerAutoloaderInit::getLoader() );
            }

            //	And let the world know we're alive
            EventManager::trigger( KismaEvents::BIRTH );
        }

        //	Load any session data...
        static::__wakeup();

        return $_conceived;
    }

    /**
     * Serialize
     */
    public static function __sleep()
    {
        //	Save options out to cache...
        if ( static::$_awake )
        {
            $_data = static::$_options;
            unset( $_data['app.autoloader'], $_data[CoreSettings::AUTO_LOADER] );

            //	store
            static::$_cache->set( CoreSettings::CACHE_KEY, $_data );
        }
    }

    /**
     * Deserialize
     */
    public static function __wakeup()
    {
        $_data = static::$_cache->get( CoreSettings::CACHE_KEY );

        if ( !empty( $_data ) )
        {
            static::$_options = Option::merge( $_data, static::$_options );
        }

        static::$_awake = true;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public static function set( $key, $value = null )
    {
        Option::set( static::$_options, static::_getKeyTag( $key ), $value );
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
        Option::addTo( static::$_options, static::_getKeyTag( $key ), $subKey, $value );
    }

    /**
     * @param string $key
     * @param string $subKey
     *
     * @return mixed
     */
    public static function removeFrom( $key, $subKey )
    {
        Option::removeFrom( static::$_options, static::_getKeyTag( $key ), $subKey );
    }

    /**
     * @param string $key           The key to get. If you pass in a null, you'll get the entire array of options
     * @param mixed  $defaultValue  The value to return if the key is not found
     * @param bool   $removeIfFound If this is true, and the key is found, it will be removed and the value returned
     *
     * @return mixed | array
     */
    public static function get( $key, $defaultValue = null, $removeIfFound = false )
    {
        if ( null === $key )
        {
            return static::$_options;
        }

        return Option::get( static::$_options, static::_getKeyTag( $key ), $defaultValue, $removeIfFound );
    }

    /**
     * Easier access to Kisma core settings (app-wide options).
     * Use "get" and "set" with a CoreSetting constant name.
     *
     * Examples:
     *
     *        $_autoloader = \Kisma::getAutoLoader();    //    Returns \Kisma::get( 'app.auto_loader' )
     *        $_debug = \Kisma::getDebug();              //    Returns \Kisma::get( 'app.debug' )
     *        $_aLife = \Kisma::getALife();              //    throws bad method call exception
     *
     * @param string $name
     * @param array  $arguments
     *
     * @throws \BadMethodCallException
     * @return mixed
     */
    public static function __callStatic( $name, $arguments )
    {
        $_type = substr( $_name = strtolower( $name ), 0, 3 );

        if ( $_type == 'get' || $_type == 'set' )
        {
            $_tag = static::_getKeyTag( $name );

            if ( CoreSettings::contains( $_tag ) )
            {
                array_unshift( $arguments, $_tag );

                return call_user_func_array( array(get_called_class(), $_type), $arguments );
            }
        }

        throw new \BadMethodCallException( 'The method "' . $name . '" does not exist, or at least, I can\'t find it.' );
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

    /**
     * @param string $name Cleans up a user-supplied option key
     *
     * @return string
     */
    protected static function _getKeyTag( $name )
    {
        //	If this is an array, apply to all keys
        if ( is_array( $name ) )
        {
            $_items = array();

            foreach ( $name as $_key => $_value )
            {
                $_items[$_key] = $_value;
            }

            return $_items;
        }

        if ( is_string( $name ) )
        {
            $_tag = Inflector::neutralize( $name );

            return $_tag;
        }

        //	Dunno, have it back the same I guess...
        return $name;
    }

    /**
     * Initialize my settings cache...
     */
    protected static function _initializeCache()
    {
        //  Try memcached
        try
        {
            return static::$_cache = Flexistore::createMemcachedStore( array('host' => 'localhost') );
        }
        catch ( \Exception $_ex )
        {

        }

        //  Try memcache
        try
        {
            return static::$_cache = Flexistore::createMemcacheStore( array('host' => 'localhost') );
        }
        catch ( \Exception $_ex )
        {

        }

        //  Try files
        return static::$_cache = Flexistore::createFileStore( sys_get_temp_dir() . '/.kc', '.kc' );
    }

}

\Kisma::conceive();
