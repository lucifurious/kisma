<?php
use Kisma\Core\TestCase;

require_once __DIR__ . '/bootstrap.php';

/**
 * KismaTest
 */
class KismaTest extends TestCase
{
	public function testCallStatic()
	{
		$this->assertEquals( 'App', \Kisma::get( 'app.name' ) );
	}

	/**
	 * @covers Kisma::conceive
	 */
	public function testConceive()
	{
		$this->assertTrue( \Kisma::getConception() );
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
