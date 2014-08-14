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
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\Cache\XcacheCache;
use Kisma\Core\Enums\CacheTypes;
use Kisma\Core\Utility\Option;

/**
 * Wrapper around doctrine/cache
 *
 * @method bool fetch( string $key, mixed $defaultValue = null, bool $remove = false )
 * @method bool save( string $key, mixed $value )
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
     * @var CacheProvider|RedisCache|XcacheCache|MemcachedCache|MemcacheCache
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
            $_mirror->getConstructor() ? ( $_mirror->newInstanceArgs( $_arguments ?: $this->_getCacheTypeArguments( $type ) ) ) : $_mirror->newInstance();

        if ( null !== ( $_namespace = Option::get( $settings, 'namespace' ) ) )
        {
            $this->_store->setNamespace( $_namespace );
        }

        if ( $init )
        {
            $this->_initializeCache( $type, $settings );
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
                    $_directory = sys_get_temp_dir() . '/kisma-' . uniqid();
                }
                while ( is_dir( $_directory ) );

                return array($_directory, static::DEFAULT_CACHE_EXTENSION);
        }

        return array();
    }

    /**
     * @param string $type
     * @param array  $settings
     */
    protected function _initializeCache( $type, $settings = array() )
    {
        switch ( $type )
        {
            case CacheTypes::MEMCACHE:
            case CacheTypes::MEMCACHED:
                if ( CacheTypes::MEMCACHE == $type && ( !class_exists( '\\Memcache', false ) || !extension_loaded( 'memcache' ) ) )
                {
                    throw new \RuntimeException( 'Memcache support is not available.' );
                }

                if ( CacheTypes::MEMCACHED == $type && ( !class_exists( '\\Memcached', false ) || !extension_loaded( 'memcached' ) ) )
                {
                    throw new \RuntimeException( 'Memcached support is not available.' );
                }

                $_cache = CacheTypes::MEMCACHE == $type
                    ? new \Memcache()
                    : new \Memcached(
                        Option::get( $settings, 'persistent_id' ), Option::get( $settings, 'callback' )
                    );

                $_servers = Option::get( $settings, 'servers', array() );

                foreach ( $_servers as $_server )
                {
                    $_cache->addServer( Option::get( $_server, 'host' ), Option::get( $_server, 'port', 11211 ), Option::get( $_server, 'weight', 0 ) );
                }

                if ( CacheTypes::MEMCACHED == $type )
                {
                    $this->_store->setMemcached( $_cache );
                }
                else
                {
                    $this->_store->setMemcache( $_cache );
                }
                break;

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
        return new Flexistore( CacheTypes::ARRAY_CACHE, array('namespace' => $namespace) );
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
        $_path = $path ?: static::_getUniqueTempPath();

        return new Flexistore( CacheTypes::FILE_SYSTEM, array('namespace' => $namespace, 'arguments' => array($_path, $extension)) );
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    protected static function _getUniqueTempPath( $prefix = '.dsp' )
    {
        //  Get a unique temp directory
        do
        {
				$_path = sys_get_temp_dir() . '/' . $prefix . '/kisma-' . \Kisma::KISMA_VERSION;
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
     * @throws \LogicException
     * @return Flexistore
     */
    public static function createRedisStore( $host = '127.0.0.1', $port = 6379, $timeout = 0.0, $namespace = null )
    {
        if ( !extension_loaded( 'redis' ) )
        {
            throw new \LogicException( 'The PHP Redis extension is required to use this store type.' );
        }

        $_redis = new \Redis();

        if ( false === $_redis->pconnect( $host, $port, $timeout ) )
        {
            throw new \LogicException( 'No redis server answering at ' . $host . ':' . $port );
        }

        $_store = new static( CacheTypes::REDIS, array('namespace' => $namespace), false );

        /** @noinspection PhpUndefinedMethodInspection */
        $_store->setRedis( $_redis );

        return $_store;
    }

    /**
     * @param array    $servers An array of memcached servers i.e. array('host'=>'localhost','port'=>11211,'weight'=>0)
     * @param string   $persistentId
     * @param callable $callback
     *
     * @return Flexistore
     */
    public static function createMemcachedStore( array $servers = array(), $persistentId = null, $callback = null )
    {
        if ( !extension_loaded( 'memcached' ) )
        {
            throw new \LogicException( 'The PHP "Memcached" extension is required to use this store type.' );
        }

        return new static(
            CacheTypes::MEMCACHED, array(
                'servers'       => $servers,
                'persistent_id' => $persistentId,
                'callback'      => $callback,
            )
        );
    }

    /**
     * @param array $servers An array of memcache servers i.e. array('host'=>'localhost','port'=>11211,'weight'=>0)
     *
     * @return Flexistore
     */
    public static function createMemcacheStore( array $servers = array() )
    {
        if ( !extension_loaded( 'memcache' ) )
        {
            throw new \LogicException( 'The PHP "Memcache" extension is required to use this store type.' );
        }

        return new static(
            CacheTypes::MEMCACHE, array(
                'servers' => $servers,
            )
        );
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
     * @param string $id
     *
     * @return bool
     */
    public function delete( $id )
    {
        return $this->_store->delete( $id );
    }

    /**
     * @return bool
     */
    public function deleteAll()
    {
        return $this->_store->deleteAll();
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
        $_multi = true;
        $_ids = $id;

        if ( !is_array( $id ) )
        {
            $_multi = false;
            $_ids = array($id => $data);
        }

        $_result = array();

        foreach ( $_ids as $_key => $_value )
        {
            $_result[$_key] = $this->save( $_key, $_value, $lifeTime );
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
            return call_user_func_array( array($this->_store, $name), $arguments );
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
