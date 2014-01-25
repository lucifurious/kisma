<?php
namespace Kisma\Tests\Core\Utility;

use Kisma\Core\Utility\Inflector;

/**
 * InflectorTest
 *
 * @package Kisma\Core\Utility
 */
class InflectorTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @covers Inflector::pluralize
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
		);

		foreach ( $_words as $_word => $_expected )
		{
			$this->assertEquals( $_expected, Inflector::pluralize( $_word ) );
		}
	}
}
