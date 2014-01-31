<?php
namespace Kisma;

/**
 * KismaTest
 */
class KismaTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @covers \Kisma\Kisma::__callStatic
	 */
	public function testCallStatic()
	{
		$this->assertEquals( 'App', Kisma::get( 'app.name' ) );
	}

	/**
	 * @covers \Kisma\Kisma::conceive
	 */
	public function testConceive()
	{
		$this->assertTrue( Kisma::getConception() );
	}

	/**
	 * @covers \Kisma\Kisma::get
	 */
	public function testGetAutoLoader()
	{
		$_autoloader = Kisma::getAutoLoader();

		$this->assertInstanceOf( 'Composer\\Autoload\\ClassLoader', $_autoloader );
	}

	/**
	 * @covers \Kisma\Kisma::get
	 */
	public function testGetBasePath()
	{
		$_path = realpath( Kisma::get( 'app.base_path' ) );
		$_testPath = realpath( dirname( dirname( __DIR__ ) ) ) . '/src';

		$this->assertTrue( $_path == $_testPath );
	}

	/**
	 * @covers \Kisma\Kisma::set
	 * @covers \Kisma\Kisma::get
	 */
	public function testSet()
	{
		Kisma::set( 'testSetOption', true );
		$this->assertTrue( Kisma::get( 'testSetOption' ) );
	}

	/**
	 * @covers \Kisma\Kisma::get
	 */
	public function testGet()
	{
		$this->assertTrue( Kisma::get( 'testSetOption' ) );
	}

}
