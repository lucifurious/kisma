<?php
namespace Kisma\Core;

/**
 * SeedBagTest
 *
 * @package Kisma\Core
 */
class SeedBagTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var SeedBag
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @covers Kisma\Core\SeedBag::__construct
	 */
	protected function setUp()
	{
		$this->object = new SeedBag;
		$this->object->set( 'testItem', 'testValue' );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	/**
	 * @covers Kisma\Core\SeedBag::keys
	 */
	public function testKeys()
	{
		$this->assertTrue( is_array( $_keys = $this->object->keys() ) );
		$this->assertTrue( $_keys[0] == 'testItem' );
	}

	/**
	 * @covers Kisma\Core\SeedBag::values
	 */
	public function testValues()
	{
		$this->assertTrue( is_array( $_values = $this->object->values() ) );
		$this->assertTrue( $_values[0] == 'testValue' );
	}

	/**
	 * @covers Kisma\Core\SeedBag::get
	 * @covers Kisma\Core\SeedBag::offsetGet
	 */
	public function testGet()
	{
		$this->assertTrue( 'testValue' == $this->object->get( 'testItem' ) );
		$this->assertTrue( 'testValue' == $this->object->offsetGet( 'testItem' ) );
	}

	/**
	 * @covers Kisma\Core\SeedBag::set
	 * @covers Kisma\Core\SeedBag::offsetSet
	 * @covers Kisma\Core\SeedBag::remove
	 * @covers Kisma\Core\SeedBag::offsetUnset
	 */
	public function testSet()
	{
		//	Regular
		$this->object->set( 'newTestItem', 'newTestValue' );
		$this->assertTrue( 'newTestValue' == $this->object->get( 'newTestItem' ) );

		$this->object->remove( 'newTestItem' );
		$this->assertTrue( false === $this->object->contains( 'newTestItem' ) );

		//	Offsets...
		$this->object->offsetSet( 'newTestItem', 'newTestValue' );
		$this->assertTrue( 'newTestValue' == $this->object->offsetGet( 'newTestItem' ) );

		$this->object->offsetUnset( 'newTestItem' );
		$this->assertTrue( false === $this->object->offsetExists( 'newTestItem' ) );
	}

	/**
	 * @covers Kisma\Core\SeedBag::clear
	 */
	public function testClear()
	{
		$this->object->clear();
		$this->assertEmpty( $this->object->get() );
	}

	/**
	 * @covers Kisma\Core\SeedBag::merge
	 */
	public function testMerge()
	{
	}

	/**
	 * @covers Kisma\Core\SeedBag::contains
	 * @covers Kisma\Core\SeedBag::offsetExists
	 */
	public function testContains()
	{
		//	Yes
		$this->assertTrue( $this->object->contains( 'testItem' ) );

		//	No
		$this->assertFalse( $this->object->contains( 'newTestItem' ) );

		//	Yes
		$this->assertTrue( $this->object->offsetExists( 'test_item' ) );

		//	No
		$this->assertFalse( $this->object->offsetExists( 'new_test_item' ) );
	}

	/**
	 * @covers Kisma\Core\SeedBag::getIterator
	 */
	public function testGetIterator()
	{
		$this->assertInstanceOf( '\\Kisma\\Core\\SeedBagIterator', $this->object->getIterator() );
	}

	/**
	 * @covers Kisma\Core\SeedBag::count
	 */
	public function testCount()
	{
		$this->assertTrue( 1 == $this->object->count() );
	}
}
