<?php
namespace Kisma\Core\Utility;

use Kisma\Core\Enums\Verbosity;

/**
 * InflectorTest
 *
 * @package Kisma\Core\Utility
 */
class InflectorTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @covers \Kisma\Core\Utility\Inflector::pluralize
	 */
	public function testPluralize()
	{
		$_words = array(
			'mouse'   => 'mice',
			'dizzy'   => 'dizzies',
			'history' => 'histories',
			'child'   => 'children',
			'quiz'    => 'quizzes',
			'person'  => 'people',
			'wart'    => 'warts',
			'ox'      => 'oxen',
			'louse'   => 'lice',
			'matrix'  => 'matrices',
			'vertex'  => 'vertices',
			'hive'    => 'hives',
			'thief'   => 'thieves',
			'tomato'  => 'tomatoes',
			'glass'   => 'glasses',
			'cows'    => 'cows',
			'geese'   => 'geese',
			'deer'    => 'deer',
		);

		foreach ( $_words as $_word => $_expected )
		{
			$this->assertEquals( $_expected, Inflector::pluralize( $_word ) );
		}
	}

	/**
	 * @covers \Kisma\Core\Enums\SeedEnum::prettyNameOf
	 */
	public function testPrettyNameOf()
	{
		$_tests = array(
			Verbosity::VERBOSE      => 'Verbose',
			Verbosity::VERY_VERBOSE => 'Very Verbose',
			Verbosity::DEBUG        => 'Debug',
		);

		foreach ( $_tests as $_constant => $_expected )
		{
			$this->assertEquals( $_expected, Verbosity::prettyNameOf( $_constant ) );
		}
	}

}
