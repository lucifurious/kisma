<?php
use Kisma\Core\Enums\CoreSettings;

/**
 * KismaTest
 */
class KismaTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @covers \Kisma::__callStatic
	 */
	public function testCallStatic()
	{
		$this->assertEquals( 'App', \Kisma::get( CoreSettings::NAME ) );
	}

	/**
	 * @covers \Kisma::conceive
	 * @covers \Kisma::__callStatic
	 * @covers \Kisma::get
	 */
	public function testConceive()
	{
		//	Go both ways ;)
		$this->assertTrue( true === \Kisma::get( CoreSettings::CONCEPTION ) );
	}

	/**
	 * @covers \Kisma::set
	 * @covers \Kisma::get
	 * @cover  \Kisma\Core\Utility\Inflector::neutralize
	 */
	public function testSet()
	{
		\Kisma::set( 'testSetOption', true );

		//	These two keys are identical, but one is neutralized
		$this->assertTrue( true === \Kisma::get( 'testSetOption' ) );
		$this->assertTrue( true === \Kisma::get( 'test_set_option' ) );
	}

	/**
	 * @covers Kisma::get
	 */
	public function testGet()
	{
		$this->assertTrue( \Kisma::get( 'testSetOption' ) );
	}

}
