<?php
use Kisma\Core\Components\LineReader;
use Kisma\Core\Components\LineWriter;
use Kisma\Core\Components\ParsingLineReader;
use Kisma\Core\Enums\DataSeparator;

/**
 * Class ReaderWriterTest
 *
 * @package DreamFactory\Platform\Services
 */
class ReaderWriterTest extends \PHPUnit_Framework_TestCase
{
	public function testLineReader()
	{
		$_reader = new LineReader(
			array(
				 'fileName'  => __DIR__ . '/test-data.tsv',
				 'enclosure' => null,
				 'separator' => null,
			)
		);

		$_lines = 0;

		foreach ( $_reader as $_row )
		{
			$_lines++;
		}

		echo PHP_EOL;
		echo 'Read ' . $_lines . ' rows (not including header).' . PHP_EOL;
	}

	public function testReadTsv()
	{
		$_reader = new ParsingLineReader(
			array(
				 'fileName'  => __DIR__ . '/test-data.tsv',
				 'enclosure' => null,
				 'separator' => "\t",
			)
		);

		$_lines = 0;

		foreach ( $_reader as $_row )
		{
			$_lines++;
			echo implode( ', ', $_row ) . PHP_EOL;
		}

		echo PHP_EOL;
		echo 'Read ' . $_lines . ' rows (not including header).' . PHP_EOL;
	}

	public function testWriteCsv()
	{
		$_reader = new ParsingLineReader(
			array(
				 'fileName'  => __DIR__ . '/test-data.tsv',
				 'enclosure' => null,
				 'separator' => "\t",
			)
		);

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

		echo PHP_EOL;
		echo 'Read ' . $_lines . ' rows (not including header).' . PHP_EOL;
		echo PHP_EOL;
		echo 'Wrote ' . $_csvWriter->getRowsOut() . ' CSV rows (including header).' . PHP_EOL;
		echo 'Wrote ' . $_tsvWriter->getRowsOut() . ' TSV rows (including header).' . PHP_EOL;
		echo 'Wrote ' . $_psvWriter->getRowsOut() . ' PSV rows (including header).' . PHP_EOL;
	}
}
