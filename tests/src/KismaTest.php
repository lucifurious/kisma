<?php
use Kisma\Core\Enums\CoreSettings;
use Kisma\Core\TestCase;

/**
 * KismaTest
 */
class KismaTest extends TestCase
{
	/**
	 * @covers Kisma::conceive
	 */
	public function testConceive()
	{
		$this->assertTrue( Kisma::get( CoreSettings::CONCEPTION ) );
	}

	/**
	 * @covers Kisma::set
	 * @covers Kisma::get
	 */
	public function testSet()
	{
		\Kisma::set( 'testSetOption', true );
		$this->assertTrue( \Kisma::get( 'testSetOption' ) );
	}

	/**
	 * @covers Kisma::get
	 */
	public function testGet()
	{
		$this->assertTrue( \Kisma::get( 'testSetOption' ) );
	}

}
