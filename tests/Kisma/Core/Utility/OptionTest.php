<?php
namespace Kisma\Core\Utility;

class OptionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Option
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = array(
			'ThisIsCamelCase'        => 'camel case',
			'this_is_not_camel_case' => 'not camel case',
			1                        => 'one',
			8                        => 'eight',
			0                        => 'zero',
		);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	/**
	 * @covers Kisma\Core\Utility\Option::get
	 * @todo   Implement testGet().
	 */
	public function testGet()
	{
		$this->assertEquals(
			'camel case',
			Option::get( $this->object, 'ThisIsCamelCase' )
		);
	}
}
