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
namespace Kisma\Core\Components;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\Cache\XcacheCache;
use Kisma\Core\Enums\CacheTypes;
use Kisma\Core\Utility\Option;

/**
 * Wrapper around doctrine/cache
 *
 * @method fetch()
 * @method save()
 */
class Flexistore
{
    //*************************************************************************
    //	Constants
    //*************************************************************************

    /**
     * @type string The namespace containing our store provider
     */
    const STORE_NAMESPACE = 'Doctrine\\Common\\Cache\\';
    /**
     * @type int The number of seconds to keep cached items. Defaults to 0, or forever.
     */
    const DEFAULT_CACHE_TTL = 0;
    /**
     * @type string The suffix for the cache files
     */
    const DEFAULT_CACHE_EXTENSION = '.kfs.php';

    //*************************************************************************
    //	Members
    //*************************************************************************

    /**
     * @var CacheProvider|RedisCache|XcacheCache
     */
    protected $_store = null;

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param string $type
     * @param array  $settings You can set 'namespace', 'extension', and 'arguments'
     * @param bool   $init
     */
    public function __construct( $type = CacheTypes::ARRAY_CACHE, array $settings = array(), $init = true )
    {
        if ( !CacheTypes::contains( $type ) )
        {
            throw new \InvalidArgumentException( 'The $type "' . $type . '" is not valid.' );
        }

        $_class = static::STORE_NAMESPACE . $type . 'Cache';

        if ( !class_exists( $_class ) || null === ( $_mirror = new \ReflectionClass( $_class ) ) )
        {
            if ( !class_exists( $type . 'Cache' ) || null === ( $_mirror = new \ReflectionClass( $type . 'Cache' ) ) )
            {
                throw new \InvalidArgumentException( 'Associated driver for type "' . $type . '" not found. Looking for "' . $_class . '"' );
            }
        }

        $_arguments = Option::get( $settings, 'arguments' );

        $this->_store =
            $_mirror->getConstructor() ? ( $_mirror->newInstanceArgs( $_arguments ? : $this->_getCacheTypeArguments( $type ) ) ) : $_mirror->newInstance();

        if ( null !== ( $_namespace = Option::get( $settings, 'namespace' ) ) )
        {
            $this->_store->setNamespace( $_namespace );
        }

        if ( $init )
        {
            $this->_initializeCache( $type );
        }
    }

    /**
     * @param string $type
     *
     * @return array|null
     */
    protected function _getCacheTypeArguments( $type )
    {
        switch ( $type )
        {
            case CacheTypes::FILE_SYSTEM:
            case CacheTypes::PHP_FILE:
                do
                {
                    $_directory = sys_get_temp_dir() . '/cache.' . uniqid();
                }
                while ( is_dir( $_directory ) );

                return array( $_directory, static::DEFAULT_CACHE_EXTENSION );
        }

        return array();
    }

    /**
     * @param string $type
     *
     * @throws \LogicException
     */
    protected function _initializeCache( $type )
    {
        switch ( $type )
        {
            case CacheTypes::REDIS:
                $_redis = new \Redis();

                if ( false === $_redis->pconnect( '127.0.0.1' ) )
                {
                    throw new \LogicException( 'Cannot connect to redis server @ 127.0.0.1' );
                }

                $this->_store->setRedis( $_redis );
                break;
        }
    }

    /**
     * @param string $namespace
     *
     * @return Flexistore
     */
    public static function createSingleton( $namespace = null )
    {
        return new Flexistore( CacheTypes::ARRAY_CACHE, array( 'namespace' => $namespace ) );
    }

    /**
     * @param string $path
     * @param string $namespace
     * @param string $extension
     *
     * @return \Kisma\Core\Components\Flexistore
     */
    public static function createFileStore( $path = null, $extension = self::DEFAULT_CACHE_EXTENSION, $namespace = null )
    {
        $_path = $path ? : static::_getUniqueTempPath();

        return new Flexistore( CacheTypes::PHP_FILE, array( 'namespace' => $namespace, 'arguments' => array( $_path, $extension ) ) );
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    protected static function _getUniqueTempPath( $prefix = 'flex' )
    {
        //  Get a unique temp directory
        do
        {
            $_path = sys_get_temp_dir() . '/' . $prefix . '.' . uniqid();
        }
        while ( is_dir( $_path ) );

        return $_path;
    }

    /**
     * @param string $host
     * @param int    $port
     * @param float  $timeout
     * @param string $namespace
     *
     * @throws LogicException
     * @return Flexistore
     */
    public static function createRedisStore( $host = '127.0.0.1', $port = 6379, $timeout = 0.0, $namespace = null )
    {
        if ( !extension_loaded( 'redis' ) )
        {
            throw new LogicException( 'The PHP Redis extension is required to use this store type.' );
        }

        $_redis = new \Redis();

        if ( false === $_redis->pconnect( $host, $port, $timeout ) )
        {
            throw new \LogicException( 'No redis server answering at ' . $host . ':' . $port );
        }

        $_store = new static( CacheTypes::REDIS, array( 'namespace' => $namespace ), false );

        /** @noinspection PhpUndefinedMethodInspection */
        $_store->setRedis( $_redis );

        return $_store;
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id           The id of the cache entry to fetch
     * @param mixed  $defaultValue The default value if $id not found
     * @param bool   $remove
     *
     * @return mixed The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function get( $id, $defaultValue = null, $remove = false )
    {
        if ( false === ( $_data = $this->fetch( $id ) ) )
        {
            if ( !$remove )
            {
                $this->save( $id, $_data = $defaultValue );
            }
        }
        elseif ( $remove )
        {
            $this->delete( $id );
        }

        return $_data;
    }

    /**
     * Puts data into the cache.
     *
     * $id can be specified as an array of key-value pairs: array( 'alpha' => 'xyz', 'beta' => 'qrs', 'gamma' => 'lmo', ... )
     *
     *
     * @param string|array $id       The cache id or array of key-value pairs
     * @param mixed        $data     The cache entry/data.
     * @param int          $lifeTime The cache lifetime. Sets a specific lifetime for this cache entry. Defaults to 0, or "never expire"
     *
     * @return boolean|boolean[] TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function set( $id, $data = null, $lifeTime = self::DEFAULT_CACHE_TTL )
    {
        $_multi = false;
        $_ids = null;

        if ( is_string( $id ) )
        {
            $_ids = array( $id => $data );
        }
        else if ( is_array( $id ) )
        {
            $_ids = $id;
            $_multi = true;
        }

        $_result = array();

        foreach ( $_ids as $_key => $_value )
        {
            $_result[ $_key ] = $this->save( $id, $_value, $lifeTime );
        }

        return $_multi ? $_result : current( $_result );
    }

    /**
     * Pass-thru for other cache methods to avoid extending CacheProvider. Also obscures keys.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call( $name, $arguments = array() )
    {
        if ( $this->_store && method_exists( $this->_store, $name ) )
        {
            switch ( $name )
            {
                //  Obscure the ids of pass-thru calls if they accept $id as the first argument
                case 'doContains':
                case 'doFetch':
                case 'doSave':
                case 'doDelete':
                    array_unshift( $arguments, $this->_obscureKey( array_shift( $arguments ) ) );
                    break;
            }

            //  Pass the buck...
            return call_user_func_array( array( $this->_store, $name ), $arguments );
        }
    }

    /**
     * @param string $key
     *
     * @return string mixed
     */
    protected function _obscureKey( $key )
    {
        //  No obscuring done but allows for children to obscure automatically
        return $key;
    }

}
