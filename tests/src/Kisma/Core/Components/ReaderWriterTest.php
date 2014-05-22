<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
 *
 * Kisma(tm) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kisma(tm) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kisma(tm).  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Kisma\Core\Components;

use Kisma\Core\Enums\DataEnclosure;
use Kisma\Core\Enums\DataSeparator;
use Kisma\Core\Enums\EscapeStyle;

/**
 * Class ReaderWriterTest
 */
class ReaderWriterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param array $config
	 *
	 * @return \Kisma\Core\Components\ParsingLineReader
	 */
	protected function _getCsvReader( array $config = array() )
	{
		return new ParsingLineReader(
			array_merge(
				array(
					'file_name'    => __DIR__ . '/test-data.csv',
					'escape_style' => EscapeStyle::DOUBLED,
					'enclosure'    => DataEnclosure::DOUBLE_QUOTE,
					'separator'    => DataSeparator::COMMA,
				),
				$config
			)
		);
	}

	/**
	 * @param array $config
	 *
	 * @return \Kisma\Core\Components\ParsingLineReader
	 */
	protected function _getTsvReader( array $config = array() )
	{
		return new ParsingLineReader(
			array_merge(
				array(
					'file_name'    => __DIR__ . '/test-data.tsv',
					'escape_style' => EscapeStyle::DOUBLED,
					'enclosure'    => DataEnclosure::DOUBLE_QUOTE,
					'separator'    => DataSeparator::TAB,
				),
				$config
			)
		);
	}

	/**
	 * @param array $config
	 *
	 * @return \Kisma\Core\Components\ParsingLineReader
	 */
	protected function _getPsvReader( array $config = array() )
	{
		return new ParsingLineReader(
			array_merge(
				array(
					'file_name'    => __DIR__ . '/test-data.psv',
					'escape_style' => EscapeStyle::NONE,
					'enclosure'    => DataEnclosure::NONE,
					'separator'    => DataSeparator::PIPE,
				),
				$config
			)
		);
	}

	public function testLineReader()
	{
		$_lines = 0;
		$_reader = $this->_getCsvReader();

		foreach ( $_reader as $_row )
		{
			$_lines++;
			$this->assertEquals( 9, count( $_row ) );
		}

		$this->assertEquals( 14, $_lines );
	}

	public function testReadCsv()
	{
		$_lines = 0;
		$_reader = $this->_getCsvReader();

		foreach ( $_reader as $_row )
		{
			$_lines++;
			$this->assertEquals( 9, count( $_row ) );
		}

		$this->assertEquals( 14, $_lines );
	}

	public function testReadPsv()
	{
		$_lines = 0;
		$_reader = $this->_getPsvReader();

		foreach ( $_reader as $_row )
		{
			$_lines++;
			$this->assertEquals( 9, count( $_row ) );
		}

		$this->assertEquals( 14, $_lines );
	}

	public function testReadTsv()
	{
		$_lines = 0;
		$_reader = $this->_getTsvReader();

		foreach ( $_reader as $_row )
		{
			$_lines++;
//			echo implode( ', ', $_row ) . PHP_EOL;
		}

//		echo PHP_EOL;
//		echo 'Read ' . $_lines . ' rows (not including header).' . PHP_EOL;

		$this->assertEquals( 14, $_lines );
	}

	public function testWriteCsv()
	{
		$_reader = $this->_getCsvReader();

		$_tsvWriter = new LineWriter(
			array(
				'fileName'  => __DIR__ . '/write-test-out-test-data.tsv',
				'keys'      => $_reader->getKeys(),
				'separator' => DataSeparator::TAB,
			)
		);

		$_csvWriter = new LineWriter(
			array(
				'fileName'  => __DIR__ . '/write-test-out-test-data.csv',
				'keys'      => $_reader->getKeys(),
				'separator' => DataSeparator::COMMA,
			)
		);

		$_psvWriter = new LineWriter(
			array(
				'fileName'  => __DIR__ . '/write-test-out-test-data.psv',
				'keys'      => $_reader->getKeys(),
				'separator' => DataSeparator::PIPE,
			)
		);

		$_lines = 0;

		foreach ( $_reader as $_row )
		{
			$_lines++;
			$_csvWriter->writeRow( $_row );
			$_tsvWriter->writeRow( $_row );
			$_psvWriter->writeRow( $_row );
		}

//		echo PHP_EOL;
//		echo 'Read ' . $_lines . ' rows (not including header).' . PHP_EOL;
//		echo PHP_EOL;
//		echo 'Wrote ' . $_csvWriter->getRowsOut() . ' CSV rows (including header).' . PHP_EOL;
//		echo 'Wrote ' . $_tsvWriter->getRowsOut() . ' TSV rows (including header).' . PHP_EOL;
//		echo 'Wrote ' . $_psvWriter->getRowsOut() . ' PSV rows (including header).' . PHP_EOL;

		$this->assertEquals( 14, $_lines );
		$this->assertEquals( 14, $_csvWriter->getRowsOut() );
		$this->assertEquals( 14, $_tsvWriter->getRowsOut() );
		$this->assertEquals( 14, $_psvWriter->getRowsOut() );
	}
}
