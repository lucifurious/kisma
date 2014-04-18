<?php
namespace Kisma\Core\Utility;

/**
 * HasherTest
 * Tests methods in the Hasher class
 *
 * @package Kisma\Core\Utility
 */
class HasherTest extends \PHPUnit_Framework_TestCase
{

	public function testGenerateUnique()
	{
		\Kisma::set( 'debug.kisma.core.utility.hasher::generate_unique', true );

		$_hash1 = Hasher::generateUnique();
		$_hash2 = Hasher::generateUnique( 'someemailaddress@somewhere.com' );

		$_hash3 = Hasher::generateUnique();
		$_hash4 = Hasher::generateUnique( 'someemailaddress@somewhere.com' );

		$this->assertTrue( $_hash1 != $_hash3 && $_hash2 != $_hash4 );
	}
}
