<?php
namespace Kisma;

use Kisma\Core\TestCase;

/**
 * KismaTest
 */
class KismaTest extends TestCase
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
	 * @covers \Kisma\Kisma::getAutoLoader
	 */
	public function testGetAutoLoader()
	{
		$_autoloader = Kisma::getAutoLoader();

		$this->assertInstanceOf( 'Composer\\Autoload\\ClassLoader', $_autoloader );
	}

	/**
	 * @covers \Kisma\Kisma::getBasePath
	 */
	public function testGetBasePath()
	{
		echo 'path = ' . $_path = Kisma::get( 'app.base_path' );
		$this->assertTrue( realpath( __DIR__ . '/..' ) == realpath( $_path ) );
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
