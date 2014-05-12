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

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;
use Kisma\Core\Enums\CacheTypes;

class ObscuredKeyTest extends Flexistore
{
    /**
     * @param string $key
     *
     * @return string The MD5 hash of the key
     */
    protected function _obscureKey( $key )
    {
        return parent::_obscureKey( md5( $key ) );
    }
}

/**
 * FlexistoreTest
 */
class FlexistoreTest extends \PHPUnit_Framework_TestCase
{
    //*************************************************************************
    //	Constants
    //*************************************************************************

    /**
     * @type int Cache duration of 5 seconds for test
     */
    const EXPIRY_TEST_TTL = 5;

    //*************************************************************************
    //	Members
    //*************************************************************************

    /**
     * @var string[]
     */
    protected static $_testTypes = array();
    /**
     * @var \Redis|null
     */
    protected $_redis = null;

    //*************************************************************************
    //	Methods
    //*************************************************************************

    /**
     * @param $cache
     * @param $stats
     */
    protected function _getStats( $cache, $stats )
    {
        $stats = $stats ? : $cache->getStats();

        $this->assertArrayHasKey( Cache::STATS_HITS, $stats );
        $this->assertArrayHasKey( Cache::STATS_MISSES, $stats );
        $this->assertArrayHasKey( Cache::STATS_UPTIME, $stats );
        $this->assertArrayHasKey( Cache::STATS_MEMORY_USAGE, $stats );
        $this->assertArrayHasKey( Cache::STATS_MEMORY_AVAILABLE, $stats );
    }

    protected function setUp()
    {
        static::$_testTypes = array(
//            CacheTypes::REDIS       => array(
//                'shared' => true,
//                'stats'  => function ( $cache, $stats )
//                {
//                    $this->_getStats( $cache, $stats );
//                },
//            ),
//            CacheTypes::ARRAY_CACHE => array(
//                'shared' => false,
//                'stats'  => function ( $cache, $stats )
//                {
//                    $this->assertNull( $stats );
//                },
//            ),
CacheTypes::FILE_SYSTEM => array(
    'shared' => false,
    'stats'  => function ( $cache, $stats )
    {
        $this->assertNull( $stats[ Cache::STATS_HITS ] );
        $this->assertNull( $stats[ Cache::STATS_MISSES ] );
        $this->assertNull( $stats[ Cache::STATS_UPTIME ] );
        $this->assertEquals( 0, $stats[ Cache::STATS_MEMORY_USAGE ] );
        $this->assertGreaterThan( 0, $stats[ Cache::STATS_MEMORY_AVAILABLE ] );
    },
),
CacheTypes::PHP_FILE    => array(
    'shared' => false,
    'stats'  => function ( $cache, $stats )
    {
        $this->assertNull( $stats[ Cache::STATS_HITS ] );
        $this->assertNull( $stats[ Cache::STATS_MISSES ] );
        $this->assertNull( $stats[ Cache::STATS_UPTIME ] );
        $this->assertEquals( 0, $stats[ Cache::STATS_MEMORY_USAGE ] );
        $this->assertGreaterThan( 0, $stats[ Cache::STATS_MEMORY_AVAILABLE ] );
    },
),
        );

        parent::setUp();
    }

    //*************************************************************************
    //	Test Cases
    //*************************************************************************

    /**
     * @dataProvider provideCrudValues
     */
    public function testBasicCrudOperations( $value )
    {
        foreach ( static::$_testTypes as $_type => $_tests )
        {
            foreach ( array( false, true ) as $_obscure )
            {
                $cache = $this->_getDriver( $_type, $_obscure );

                // Test saving a value, checking if it exists, and fetching it back
                $this->assertTrue( $cache->save( 'key', 'value' ) );
                $this->assertTrue( $cache->contains( 'key' ) );
                $this->assertEquals( 'value', $cache->fetch( 'key' ) );

                // Test updating the value of a cache entry
                $this->assertTrue( $cache->save( 'key', 'value-changed' ) );
                $this->assertTrue( $cache->contains( 'key' ) );
                $this->assertEquals( 'value-changed', $cache->fetch( 'key' ) );

                // Test deleting a value
                $this->assertTrue( $cache->delete( 'key' ) );
                $this->assertFalse( $cache->contains( 'key' ) );
            }
        }
    }

    public function testCacheExpiry()
    {
        foreach ( static::$_testTypes as $_type => $_tests )
        {
            foreach ( array( true, false ) as $_obscureKeys )
            {
                $cache = $this->_getDriver( $_type, $_obscureKeys );

                $this->assertTrue( $cache->save( 'short-lived', 'shorty', static::EXPIRY_TEST_TTL ) );
                $this->assertTrue( $cache->save( 'long-lived', 'stretch' ) );

                //  Wait for the cache to expire...
                sleep( static::EXPIRY_TEST_TTL + 1 );

                $this->assertTrue( false === $cache->fetch( 'short-lived' ) );
                $this->assertTrue( 'stretch' == $cache->fetch( 'long-lived' ) );
            }
        }
    }

    /**
     * @return array
     */
    public function provideCrudValues()
    {
        return array(
            'array'   => array( array( 'one', 2, 3.0 ) ),
            'string'  => array( 'value' ),
            'integer' => array( 1 ),
            'float'   => array( 1.5 ),
            'object'  => array( new \ArrayObject() ),
        );
    }

    /**
     */
    public function testDeleteAll()
    {
        foreach ( static::$_testTypes as $_type => $_tests )
        {
            foreach ( array( true, false ) as $_obscureKeys )
            {
                $cache = $this->_getDriver( $_type, $_obscureKeys );

                $this->assertTrue( $cache->save( 'key1', 1 ) );
                $this->assertTrue( $cache->save( 'key2', 2 ) );

                $this->assertTrue( $cache->deleteAll() );
                $cache->flushAll();

                $this->assertFalse( $cache->contains( 'key1' ) );
                $this->assertFalse( $cache->contains( 'key2' ) );
            }
        }
    }

    /**
     */
    public function testDeleteAllAndNamespaceVersioningBetweenCaches()
    {
        foreach ( static::$_testTypes as $_type => $_tests )
        {
            if ( !$this->isSharedStorage( $_type ) )
            {
                echo( 'The store type of "' . CacheTypes::prettyNameOf( $_type ) . '" does not use shared storage' . PHP_EOL );
                continue;
            }

            $cache1 = $this->_getDriver( $_type );
            $cache2 = $this->_getDriver( $_type );

            $this->assertTrue( $cache1->save( 'key1', 1 ) );
            $this->assertTrue( $cache2->save( 'key2', 2 ) );

            /* Both providers are initialized with the same namespace version, so
             * they can see entries set by each other.
             */
            $this->assertTrue( $cache1->contains( 'key1' ) );
            $this->assertTrue( $cache1->contains( 'key2' ) );
            $this->assertTrue( $cache2->contains( 'key1' ) );
            $this->assertTrue( $cache2->contains( 'key2' ) );

            /* Deleting all entries through one provider will only increment the
             * namespace version on that object (and in the cache itself, which new
             * instances will use to initialize). The second provider will retain
             * its original version and still see stale data.
             */
            $this->assertTrue( $cache1->deleteAll() );

            $this->assertFalse( $cache1->contains( 'key1' ) );
            $this->assertFalse( $cache1->contains( 'key2' ) );
            $this->assertTrue( $cache2->contains( 'key1' ) );
            $this->assertTrue( $cache2->contains( 'key2' ) );

            /* A new cache provider should not see the deleted entries, since its
             * namespace version will be initialized.
             */
            $cache3 = $this->_getDriver( $_type );
            $this->assertFalse( $cache3->contains( 'key1' ) );
            $this->assertFalse( $cache3->contains( 'key2' ) );
        }
    }

    /**
     */
    public function testFlushAll()
    {
        foreach ( static::$_testTypes as $_type => $_tests )
        {
            $cache = $this->_getDriver( $_type );

            $this->assertTrue( $cache->save( 'key1', 1 ) );
            $this->assertTrue( $cache->save( 'key2', 2 ) );
            $this->assertTrue( $cache->flushAll() );
            $this->assertFalse( $cache->contains( 'key1' ) );
            $this->assertFalse( $cache->contains( 'key2' ) );
        }
    }

    /**
     */
    public function testFlushAllAndNamespaceVersioningBetweenCaches()
    {
        foreach ( static::$_testTypes as $_type => $_tests )
        {
            if ( !$this->isSharedStorage( $_type ) )
            {
                echo( 'The store type of "' . CacheTypes::prettyNameOf( $_type ) . '" does not use shared storage' . PHP_EOL );
                continue;
            }

            $cache1 = $this->_getDriver( $_type );
            $cache2 = $this->_getDriver( $_type );

            /* Deleting all elements from the first provider should increment its
             * namespace version before saving the first entry.
             */
            $cache1->deleteAll();
            $this->assertTrue( $cache1->save( 'key1', 1 ) );

            /* The second provider will be initialized with the same namespace
             * version upon its first save operation.
             */
            $this->assertTrue( $cache2->save( 'key2', 2 ) );

            /* Both providers have the same namespace version and can see entires
             * set by each other.
             */
            $this->assertTrue( $cache1->contains( 'key1' ) );
            $this->assertTrue( $cache1->contains( 'key2' ) );
            $this->assertTrue( $cache2->contains( 'key1' ) );
            $this->assertTrue( $cache2->contains( 'key2' ) );

            /* Flushing all entries through one cache will remove all entries from
             * the cache but leave their namespace version as-is.
             */
            $this->assertTrue( $cache1->flushAll() );
            $this->assertFalse( $cache1->contains( 'key1' ) );
            $this->assertFalse( $cache1->contains( 'key2' ) );
            $this->assertFalse( $cache2->contains( 'key1' ) );
            $this->assertFalse( $cache2->contains( 'key2' ) );

            /* Inserting a new entry will use the same, incremented namespace
             * version, and it will be visible to both providers.
             */
            $this->assertTrue( $cache1->save( 'key1', 1 ) );
            $this->assertTrue( $cache1->contains( 'key1' ) );
            $this->assertTrue( $cache2->contains( 'key1' ) );

            /* A new cache provider will be initialized with the original namespace
             * version and not share any visibility with the first two providers.
             */
            $cache3 = $this->_getDriver( $_type );

            $this->assertFalse( $cache3->contains( 'key1' ) );
            $this->assertFalse( $cache3->contains( 'key2' ) );
            $this->assertTrue( $cache3->save( 'key3', 3 ) );
            $this->assertTrue( $cache3->contains( 'key3' ) );
        }
    }

    public function testNamespace()
    {
        foreach ( static::$_testTypes as $_type => $_tests )
        {
            $cache = $this->_getDriver( $_type );
            $cache->setNamespace( 'ns1_' );

            $this->assertTrue( $cache->save( 'key1', 1 ) );
            $this->assertTrue( $cache->contains( 'key1' ) );

            $cache->setNamespace( 'ns2_' );

            $this->assertFalse( $cache->contains( 'key1' ) );
        }
    }

    public function testDeleteAllNamespace()
    {
        foreach ( static::$_testTypes as $_type => $_tests )
        {
            $cache = $this->_getDriver( $_type );

            $cache->setNamespace( 'ns1' );
            $this->assertFalse( $cache->contains( 'key1' ) );
            $cache->save( 'key1', 'test' );
            $this->assertTrue( $cache->contains( 'key1' ) );

            $cache->setNamespace( 'ns2' );
            $this->assertFalse( $cache->contains( 'key1' ) );
            $cache->save( 'key1', 'test' );
            $this->assertTrue( $cache->contains( 'key1' ) );

            $cache->setNamespace( 'ns1' );
            $this->assertTrue( $cache->contains( 'key1' ) );
            $cache->deleteAll();
            $this->assertFalse( $cache->contains( 'key1' ) );

            $cache->setNamespace( 'ns2' );
            $this->assertTrue( $cache->contains( 'key1' ) );
            $cache->deleteAll();
            $this->assertFalse( $cache->contains( 'key1' ) );
        }
    }

    /**
     * @group  DCOM-43
     */
    public function testGetStats()
    {
        foreach ( static::$_testTypes as $_type => $_tests )
        {
            $cache = $this->_getDriver( $_type );
            $stats = $cache->getStats();

            $_tests['stats']( $cache, $stats );
        }
    }

    public function testFetchMissShouldReturnFalse()
    {
        foreach ( static::$_testTypes as $_type => $_tests )
        {
            $cache = $this->_getDriver( $_type );

            /* Ensure that caches return boolean false instead of null on a fetch
             * miss to be compatible with ORM integration.
             */
            $result = $cache->fetch( 'nonexistent_key' );

            $this->assertFalse( $result );
            $this->assertNotNull( $result );
        }
    }

    /**
     * Return whether multiple cache providers share the same storage.
     *
     * This is used for skipping certain tests for shared storage behavior.
     *
     * @param string $type
     *
     * @return boolean
     */
    protected function isSharedStorage( $type )
    {
        return static::$_testTypes[ $type ]['shared'];
    }

    /**
     * @param string $type
     * @param bool   $obscured If true, an obscured key store is returned
     *
     * @return CacheProvider
     */
    protected function _getDriver( $type, $obscured = false )
    {
        echo $obscured ? 'O' : null;

        return $obscured ? new ObscuredKeyTest( $type ) : new Flexistore( $type );
    }
}

/**
 * NotSetStateClass
 *
 * @package Kisma\Core\Components
 */
class NotSetStateClass
{
    private $value;

    /**
     * @param $value
     */
    public function __construct( $value )
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}

/**
 * SetStateClass
 *
 * @package Kisma\Core\Components
 */
class SetStateClass extends NotSetStateClass
{
    public static $values = array();

    /**
     * @param $data
     *
     * @return SetStateClass
     */
    public static function __set_state( $data )
    {
        static::$values = $data;

        return new static( $data['value'] );
    }
}
