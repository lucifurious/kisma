<?php
namespace Kisma\Core;

require_once __DIR__ . '/SeedTest_Object.php';

class SeedBagTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var SeedBag
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 * @covers Kisma\Core\SeedBag::__construct
	 */
	protected function setUp()
	{
		$this->object = new SeedBag;
		$this->object->add( 'testItem', 'testValue' );
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
		$this->assertTrue( $_keys[0] == 'test_item' );
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
	 */
	public function testGet()
	{
		$this->assertTrue( 'testValue' == $this->object->get( 'testItem' ) );
	}

	/**
	 * @covers Kisma\Core\SeedBag::add
	 * @covers Kisma\Core\SeedBag::remove
	 */
	public function testAdd()
	{
		$this->object->add( 'newTestItem', 'newTestValue' );
		$this->assertTrue( 'newTestValue' == $this->object->get( 'newTestItem' ) );
		$this->object->remove( 'newTestItem' );
		$this->assertTrue( false === $this->object->contains( 'newTestItem' ) );
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
	 */
	public function testContains()
	{
	}

	/**
	 * @covers Kisma\Core\SeedBag::offsetExists
	 */
	public function testOffsetExists()
	{
	}

	/**
	 * @covers Kisma\Core\SeedBag::offsetGet
	 */
	public function testOffsetGet()
	{
	}

	/**
	 * @covers Kisma\Core\SeedBag::offsetSet
	 * @todo   Implement testOffsetSet().
	 */
	public function testOffsetSet()
	{
	}

	/**
	 * @covers Kisma\Core\SeedBag::offsetUnset
	 * @todo   Implement testOffsetUnset().
	 */
	public function testOffsetUnset()
	{
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
