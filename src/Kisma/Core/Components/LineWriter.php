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

use Kisma\Core\Enums\EscapeStyle;
use Kisma\Core\Enums\LineBreak;
use Kisma\Core\Exceptions\FileSystemException;
use Kisma\Core\Interfaces\WriterLike;

/**
 * LineWriter.php
 * Tabular data writer
 */
class LineWriter extends ParsingLineReader implements WriterLike
{
	//*************************************************************************
	//	Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	protected $_rowsOut = 0;
	/**
	 * @var int
	 */
	protected $_linesOut = 0;
	/**
	 * @var string|null
	 */
	protected $_nullValue = null;
	/**
	 * @var string
	 */
	protected $_lineBreak = LineBreak::Linux;
	/**
	 * @var bool
	 */
	protected $_autoWriteHeader = true;
	/**
	 * @var bool
	 */
	protected $_appendEOL = true;
	/**
	 * @var bool
	 */
	protected $_wrapWhitespace = false;
	/**
	 * @var bool
	 */
	protected $_lazyWrap = false;

	//*************************************************************************
	//	Methods
	//*************************************************************************

	/**
	 * @param array $settings
	 *
	 * @throws \Kisma\Core\Exceptions\FileSystemException
	 * @throws \InvalidArgumentException
	 */
	public function __construct( $settings = array() )
	{
		parent::__construct( $settings );

		if ( false === ( $this->_handle = fopen( $this->_fileName, 'w' ) ) )
		{
			throw new FileSystemException( 'Cannot open file "' . $this->_fileName . '" for writing.' );
		}
	}

	/**
	 * Choose your destructor!
	 */
	public function __destruct()
	{
		if ( is_resource( $this->_handle ) )
		{
			$this->_writeHeader( true );
			@fclose( $this->_handle );
			$this->_handle = null;
		}
	}

	/**
	 * @param array $data
	 */
	public function writeRow( array $data )
	{
		$this->_writeHeader( true );

		if ( empty( $this->_keys ) )
		{
			$_data = $data;
		}
		else
		{
			$_data = array();

			foreach ( $this->_keys as $_key )
			{
				$_data[] = isset( $data[ $_key ] ) ? $data[ $_key ] : null;
			}
		}

		$this->_write( $_data );
		$this->_rowsOut++;
	}

	/**
	 * @throws \Kisma\Core\Exceptions\FileSystemException
	 */
	public function close()
	{
		if ( is_resource( $this->_handle ) )
		{
			$this->_writeHeader( true );

			if ( !fclose( $this->_handle ) )
			{
				throw new FileSystemException( 'Error closing file: ' . $this->_fileName );
			}

			$this->_handle = null;
		}

		return $this->_rowsOut;
	}

	/**
	 * @param array $data
	 *
	 * @throws \Kisma\Core\Exceptions\FileSystemException
	 */
	protected function _write( $data )
	{
		if ( null === $this->_handle )
		{
			throw new FileSystemException( 'The file must be open to write data.' );
		}

		$_values = array();

		foreach ( $data as $_value )
		{
			if ( null === $_value )
			{
				if ( null !== $this->_nullValue )
				{
					$_values[] = $this->_nullValue;
					continue;
				}

				$_values[] = !$this->_wrapWhitespace ? null : ( $this->_enclosure . $this->_enclosure );
				continue;
			}

			if ( $this->_lazyWrap &&
				 false === strpos( $_value, $this->_separator ) &&
				 ( empty( $this->_enclosure ) || false === strpos( $_value, $this->_enclosure ) )
			)
			{
				$_values[] = $_value;
				continue;
			}

			switch ( $this->_escapeStyle )
			{
				case EscapeStyle::DOUBLED:
					$_value = str_replace( $this->_enclosure, $this->_enclosure . $this->_enclosure, $_value );
					break;

				case EscapeStyle::SLASHED:
					$_value = str_replace( $this->_enclosure, '\\' . $this->_enclosure, str_replace( '\\', '\\\\', $_value ) );
					break;
			}

			$_values[] = $this->_enclosure . $_value . $this->_enclosure;
		}

		$_line = implode( $this->_separator, $_values );

		if ( !$this->_appendEOL )
		{
			$_line .= $this->_lineBreak;
		}
		else if ( $this->_linesOut > 0 )
		{
			$_line = $this->_lineBreak . $_line;
		}

		$_lineSize = function_exists( 'mb_strlen' ) ? mb_strlen( $_line ) : strlen( $_line );

		if ( false === ( $_byteCount = fwrite( $this->_handle, $_line ) ) )
		{
			throw new FileSystemException( 'Error writing to file: ' . $this->_fileName );
		}

		if ( $_byteCount != $_lineSize )
		{
			throw new FileSystemException( 'Failed to write entire buffer to file: ' . $this->_fileName );
		}

		$this->_linesOut++;
	}

	/**
	 * @param bool  $autoOnly
	 * @param array $header
	 */
	protected function _writeHeader( $autoOnly = false, array $header = null )
	{
		if ( $autoOnly && !$this->_autoWriteHeader )
		{
			return;
		}

		if ( null === $header )
		{
			$header = $this->_keys;
		}

		if ( !is_array( $header ) )
		{
			$header = array( (string)$header );
		}

		$this->_write( $header );
		$this->_autoWriteHeader = false;
	}

	/**
	 * @param boolean $appendEOL
	 *
	 * @return LineWriter
	 */
	public function setAppendEOL( $appendEOL )
	{
		$this->_appendEOL = $appendEOL;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getAppendEOL()
	{
		return $this->_appendEOL;
	}

	/**
	 * @param boolean $autoWriteHeader
	 *
	 * @return LineWriter
	 */
	public function setAutoWriteHeader( $autoWriteHeader )
	{
		$this->_autoWriteHeader = $autoWriteHeader;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getAutoWriteHeader()
	{
		return $this->_autoWriteHeader;
	}

	/**
	 * @param boolean $lazyWrap
	 *
	 * @return LineWriter
	 */
	public function setLazyWrap( $lazyWrap )
	{
		$this->_lazyWrap = $lazyWrap;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getLazyWrap()
	{
		return $this->_lazyWrap;
	}

	/**
	 * @param string $lineBreak
	 *
	 * @return LineWriter
	 */
	public function setLineBreak( $lineBreak )
	{
		$this->_lineBreak = $lineBreak;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLineBreak()
	{
		return $this->_lineBreak;
	}

	/**
	 * @return int
	 */
	public function getLinesOut()
	{
		return $this->_linesOut;
	}

	/**
	 * @param null|string $nullValue
	 *
	 * @return LineWriter
	 */
	public function setNullValue( $nullValue )
	{
		$this->_nullValue = $nullValue;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getNullValue()
	{
		return $this->_nullValue;
	}

	/**
	 * @return int
	 */
	public function getRowsOut()
	{
		return $this->_rowsOut;
	}

	/**
	 * @param boolean $wrapWhitespace
	 *
	 * @return LineWriter
	 */
	public function setWrapWhitespace( $wrapWhitespace )
	{
		$this->_wrapWhitespace = $wrapWhitespace;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getWrapWhitespace()
	{
		return $this->_wrapWhitespace;
	}
}
