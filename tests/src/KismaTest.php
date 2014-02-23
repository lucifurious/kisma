<?php
use Kisma\Core\Enums\CoreSettings;
use Kisma\Core\TestCase;

/**
 * KismaTest
 */
class KismaTest extends TestCase
{
	/**
	 * @covers \Kisma::__callStatic
	 */
	public function testCallStatic()
	{
		$this->assertEquals( 'App', \Kisma::getName() );
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
		$this->assertTrue( true === \Kisma::getConception() );
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
