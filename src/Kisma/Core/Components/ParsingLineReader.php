<?php
namespace Kisma\Core\Components;

use Kisma\Core\Enums\DataEnclosure;
use Kisma\Core\Enums\DataSeparator;
use Kisma\Core\Exceptions\FileSystemException;
use Kisma\Core\Interfaces\EscapeStyle;

/**
 * ParsingLineReader.php
 * Tabular data reader
 */
class ParsingLineReader extends LineReader
{
	//*************************************************************************
	//	Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	protected $_separator = DataSeparator::COMMA;
	/**
	 * @var string
	 */
	protected $_enclosure = DataEnclosure::DOUBLE_QUOTE;
	/**
	 * @var int
	 */
	protected $_escapeStyle = EscapeStyle::SLASHED;
	/**
	 * @var array
	 */
	protected $_keys = array();
	/**
	 * @var bool
	 */
	protected $_overrideKeys = false;
	/**
	 * @var bool
	 */
	protected $_header = true;

	//*************************************************************************
	//	Methods
	//*************************************************************************

	/**
	 * @return array|bool|mixed|null
	 */
	public function current()
	{
		if ( null !== $this->_currentLine )
		{
			return $this->_currentLine;
		}

		if ( false === ( $_line = $this->_readLine() ) )
		{
			return null;
		}

		if ( null === $this->_keys )
		{
			return $this->_currentLine = $_line;
		}

		$this->_currentLine = array();

		reset( $this->_keys );

		foreach ( $_line as $_column )
		{
			if ( false === ( $_key = each( $this->_keys ) ) )
			{
				break;
			}

			$this->_currentLine[$_key['value']] = $_column;
		}

		return $this->_currentLine;
	}

	/**
	 * @throws \Kisma\Core\Exceptions\FileSystemException
	 */
	public function rewind()
	{
		parent::rewind();

		if ( $this->_header )
		{
			if ( false === ( $_header = $this->_readLine( true ) ) )
			{
				throw new FileSystemException( 'Error reading header row from file: ' . $this->_fileName );
			}

			if ( !$this->_overrideKeys )
			{
				$this->_keys = $_header;
			}
		}
	}

	/**
	 * @param array $keys
	 */
	public function setKeys( $keys )
	{
		$this->_keys = $keys;
		$this->_overrideKeys = true;
	}

	/**
	 * @return array
	 */
	public function getKeys()
	{
		if ( !$this->_rewound )
		{
			$this->rewind();
		}

		return $this->_keys;
	}

	/**
	 * @param string $enclosure
	 *
	 * @return ParsingLineReader
	 */
	public function setEnclosure( $enclosure )
	{
		$this->_enclosure = $enclosure;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEnclosure()
	{
		return $this->_enclosure;
	}

	/**
	 * @param int $escapeStyle
	 *
	 * @return ParsingLineReader
	 */
	public function setEscapeStyle( $escapeStyle )
	{
		$this->_escapeStyle = $escapeStyle;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getEscapeStyle()
	{
		return $this->_escapeStyle;
	}

	/**
	 * @param boolean $header
	 *
	 * @return ParsingLineReader
	 */
	public function setHeader( $header )
	{
		$this->_header = $header;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getHeader()
	{
		return $this->_header;
	}

	/**
	 * @param boolean $overrideKeys
	 *
	 * @return ParsingLineReader
	 */
	public function setOverrideKeys( $overrideKeys )
	{
		$this->_overrideKeys = $overrideKeys;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getOverrideKeys()
	{
		return $this->_overrideKeys;
	}

	/**
	 * @param string $separator
	 *
	 * @return ParsingLineReader
	 */
	public function setSeparator( $separator )
	{
		$this->_separator = $separator;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSeparator()
	{
		return $this->_separator;
	}

	/**
	 * @param string $line
	 *
	 * @throws \Kisma\Core\Exceptions\FileSystemException
	 * @return array|bool
	 */
	protected function _parseLine( $line )
	{
		if ( empty( $this->_enclosure ) )
		{
			$_result = $this->_parseNone( $line );
		}
		else
		{
			switch ( $this->_escapeStyle )
			{
				case EscapeStyle::DOUBLED:
					$_result = $this->_parseDoubled( $line );
					break;

				case EscapeStyle::SLASHED:
					$_result = $this->_parseSlashed( $line );
					break;

				case EscapeStyle::NONE:
					$_result = $this->_parseNonEscaped( $line );
					break;
				default:
					$_result = $this->_parseNone( $line );
					break;
			}
		}

		return $_result;
	}

	/**
	 * @param string $line
	 * @param string $regex
	 *
	 * @throws \Kisma\Core\Exceptions\FileSystemException
	 * @return array|bool
	 */
	protected function _parsePattern( $line, $regex )
	{
		$regex = str_replace( '#', '\\#', $regex );
		$_wrap = preg_quote( $this->_enclosure, '#' );
		$_sep = preg_quote( $this->_separator, '#' );
		$_regexp = '#^(?:' . $regex . ',)*' . $regex . '(?:\\r\\n|\\n)$#s';
		$_regexp = str_replace( array( '"', ',' ), array( $_wrap, $_sep ), $_regexp );

		if ( false === ( $_result = preg_match( $_regexp, $line ) ) )
		{
			throw new FileSystemException( 'Pattern matching error while processing line' );
		}

		if ( !$_result )
		{
			return false;
		}

		$regex = '#' . $regex . '(?:,|\\r\\n|\\n)#s';
		$regex = str_replace( array( '"', ',' ), array( $_wrap, $_sep ), $regex );

		if ( false === ( $_count = preg_match_all( $regex, $line, $matches, PREG_SET_ORDER ) ) )
		{
			throw new FileSystemException( 'Pattern matching error while processing line' );
		}

		$_response = array();

		for ( $_i = 0; $_i < $_count; $_i++ )
		{
			unset( $matches[$_i][0] );
			$_response[] = implode( '', $matches[$_i] );
		}

		return $_response;
	}

	/**
	 * @param string $line
	 *
	 * @return array|bool
	 */
	protected function _parseDoubled( $line )
	{
		$_result = $this->_parsePattern( $line, '(?:"((?:""|[^"])*)"|((?U)[^,"]*))' );

		if ( $_result )
		{
			array_walk(
				$_result,
				function ( &$value, $enclosure )
				{
					$value = str_replace( $enclosure . $enclosure, $enclosure, $value );
				},
				$this->_enclosure
			);
		}

		return $_result;
	}

	/**
	 * @param string $line
	 *
	 * @return mixed
	 */
	protected function _parseSlashed( $line )
	{
		$_result = $this->_parsePattern( $line, '(?:"((?:\\\\"|[^"])*)"|((?U)[^,"]*))' );

		if ( $_result )
		{
			array_walk(
				$_result,
				function ( &$value, $enclosure )
				{
					$value = str_replace( '\\' . $enclosure, $enclosure, $value );
				},
				$this->_enclosure
			);
		}

		return $_result;
	}

	/**
	 * @param string $line
	 *
	 * @return array|bool
	 */
	protected function _parseNonEscaped( $line )
	{
		return $this->_parsePattern( $line, '(?:"((?U).*)")' );
	}

	/**
	 * @param string $line
	 *
	 * @return array
	 */
	protected function _parseNone( $line )
	{
		$_line = preg_replace( '#\\r?\\n$#', '', $line );

		return empty( $this->_separator ) ? $_line : explode( $this->_separator, $_line );
	}
}