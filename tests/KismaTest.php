<?php
require_once  dirname( __DIR__ ) . '/vendor/autoload.php';

class KismaTest extends \Kisma\Core\TestCase
{
	public function testCallStatic()
	{
		$this->assertEquals( 'App', \Kisma::getName() );
	}

	/**
	 * @covers Kisma::conceive
	 */
	public function testConceive()
	{
		$this->assertTrue( \Kisma::getConception() );
	}

	/**
	 * @covers Kisma::getAutoLoader
	 */
	public function testGetAutoLoader()
	{
		$_autoloader = \Kisma::getAutoLoader();

		$this->assertInstanceOf( '\\Composer\\Autoload\\ClassLoader', $_autoloader );
	}

	/**
	 * @covers Kisma::getBasePath
	 */
	public function testGetBasePath()
	{
		$this->assertTrue( dirname( __DIR__ ) . '/src' == \Kisma::getBasePath() );
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
