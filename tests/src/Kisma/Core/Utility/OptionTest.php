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
			'test-blank-value'       => '',
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
	 * @covers Kisma\Core\Utility\Inflector::neutralize
	 * @covers Kisma\Core\Utility\Inflector::deneutralize
	 * @covers Kisma\Core\Utility\Option::get
	 */
	public function testGet()
	{
		$this->assertEquals( 'camel case',
			Option::get( $this->object, 'ThisIsCamelCase' ) );

		$this->assertTrue( null === Option::get( $this->object, 'test-blank-value', '', false, true ) );
	}

	/**
	 * @covers Kisma\Core\Utility\Option::set
	 */
	public function testSetWithArray()
	{
		$_sourceOptions = array(
			'ThisIsCamelCase'        => 'camel case',
			'this_is_not_camel_case' => 'not camel case',
			'one'                    => 'one',
			'eight'                  => 'eight',
			'zero'                   => 'zero',
		);

		$_targetOptions = array();

		Option::set( $_targetOptions, $_sourceOptions );

		$this->assertEquals( $_sourceOptions, $_targetOptions );
	}

}
