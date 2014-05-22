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
use Kisma\Core\Exceptions\FileSystemException;

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

			$this->_currentLine[ $_key['value'] ] = $_column;
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
	 * @param string $line
	 *
	 * @throws \Kisma\Core\Exceptions\FileSystemException
	 * @return array|bool
	 */
	protected function _parseLine( $line )
	{
		$_result = str_getcsv(
			$line,
			$this->_separator,
			$this->_enclosure,
			EscapeStyle::SLASHED == $this->_escapeStyle ? '\\' : EscapeStyle::DOUBLED == $this->_escapeStyle ? '"' : ''
		);

		return $_result;
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

}