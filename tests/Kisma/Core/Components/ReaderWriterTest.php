<?php
namespace Kisma\Core\Components;

use Kisma\Core\Components\LineWriter;
use Kisma\Core\Components\ParsingLineReader;
use Kisma\Core\Enums\DataEnclosure;
use Kisma\Core\Enums\DataSeparator;
use Kisma\Core\Interfaces\EscapeStyle;

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
		return new ParsingLineReader( array_merge(
			array(
				'file_name'    => __DIR__ . '/test-data.csv',
				'escape_style' => EscapeStyle::DOUBLED,
				'enclosure'    => DataEnclosure::DOUBLE_QUOTE,
				'separator'    => DataSeparator::COMMA,
			),
			$config
		) );
	}

	/**
	 * @param array $config
	 *
	 * @return \Kisma\Core\Components\ParsingLineReader
	 */
	protected function _getTsvReader( array $config = array() )
	{
		return new ParsingLineReader( array_merge(
			array(
				'file_name'    => __DIR__ . '/test-data.tsv',
				'escape_style' => EscapeStyle::DOUBLED,
				'enclosure'    => DataEnclosure::DOUBLE_QUOTE,
				'separator'    => DataSeparator::TAB,
			),
			$config
		) );
	}

	/**
	 * @param array $config
	 *
	 * @return \Kisma\Core\Components\ParsingLineReader
	 */
	protected function _getPsvReader( array $config = array() )
	{
		return new ParsingLineReader( array_merge(
			array(
				'file_name'    => __DIR__ . '/test-data.psv',
				'escape_style' => EscapeStyle::NONE,
				'enclosure'    => DataEnclosure::NONE,
				'separator'    => DataSeparator::PIPE,
			),
			$config
		) );
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

		$_tsvWriter = new LineWriter( array(
			'fileName'  => __DIR__ . '/write-test-out-test-data.tsv',
			'keys'      => $_reader->getKeys(),
			'separator' => DataSeparator::TAB,
		) );

		$_csvWriter = new LineWriter( array(
			'fileName'  => __DIR__ . '/write-test-out-test-data.csv',
			'keys'      => $_reader->getKeys(),
			'separator' => DataSeparator::COMMA,
		) );

		$_psvWriter = new LineWriter( array(
			'fileName'  => __DIR__ . '/write-test-out-test-data.psv',
			'keys'      => $_reader->getKeys(),
			'separator' => DataSeparator::PIPE,
		) );

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
