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
namespace Kisma\Core\Utility;

/**
 * Markup
 * Markup related functions
 */
class Markup
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int The delimiter position
	 */
	const OpenStart = 0;
	/**
	 * @var int The delimiter position
	 */
	const OpenEnd = 1;
	/**
	 * @var int The delimiter position
	 */
	const CloseStart = 2;
	/**
	 * @var int The delimiter position
	 */
	const CloseEnd = 3;
	/**
	 * @var int The delimiter position
	 */
	const SelfCloseStart = 4;
	/**
	 * @var int The delimiter position
	 */
	const SelfCloseEnd = 5;
	/**
	 * @var string Used internally for replacements
	 */
	const ValuePlaceholder = '%%__VALUE__%%';

	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var array The delimiters for the tags in this markup. This should handle any SGML-based markup
	 */
	protected static $_delimiters = array(
		self::OpenStart      => '<',
		self::OpenEnd        => '>',
		self::CloseStart     => '</',
		self::CloseEnd       => '>',
		self::SelfCloseStart => '<',
		self::SelfCloseEnd   => '/>',
	);
	/**
	 * @var bool If true, tags and attributes will be uppercased
	 */
	protected static $_uppercaseTags = false;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Quickie wrap-per
	 *
	 * @param string $tag
	 * @param string $value
	 * @param array  $attributes
	 *
	 * @return string
	 */
	public static function wrap( $tag, $value = null, $attributes = array() )
	{
		return static::tag( $tag, $attributes, $value );
	}

	/**
	 * @param string       $name
	 * @param string       $value
	 * @param string|array $attributes
	 * @param bool         $close
	 * @param bool         $selfClose
	 *
	 * @return string
	 */
	public static function tag( $name, $attributes = array(), $value = null, $close = true, $selfClose = false )
	{
		//	Nah nah...
		if ( true !== $close )
		{
			$close = $selfClose = false;
		}

		$_html = static::openTag( $name, $attributes, $selfClose );

		if ( false !== $selfClose )
		{
			return $_html;
		}

		if ( null !== $value )
		{
			$_html .= trim( $value );
		}

		if ( $close )
		{
			$_html .= static::closeTag( $name );
		}

		return trim( $_html );
	}

	/**
	 * @param string       $tag
	 * @param string|array $attributes
	 * @param bool         $selfClose
	 *
	 * @return string
	 */
	public static function openTag( $tag, $attributes = array(), $selfClose = false )
	{
		if ( is_array( $attributes ) )
		{
			$attributes = Convert::kvpToString( $attributes, static::$_uppercaseTags );
		}

		return str_replace(
			static::ValuePlaceholder,
			static::_cleanTag( $tag ) . ' ' . $attributes,
			static::_tagPattern( true, $selfClose )
		);
	}

	/**
	 * @param $tag
	 *
	 * @return string
	 */
	public static function closeTag( $tag )
	{
		return str_replace(
			static::ValuePlaceholder,
			static::_cleanTag( $tag ),
			static::_tagPattern( false )
		);
	}

	/**
	 * Wraps value in a CDATA tag
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public static function cdata( $value )
	{
		return '<![CDATA[' . $value . ']]>';
	}

	/**
	 * @param int    $which
	 * @param string $what
	 *
	 * @return Markup
	 */
	public function setDelimiter( $which, $what )
	{
		Option::set( static::$_delimiters, $which, $what );

		return $this;
	}

	/**
	 * @param array $delimiters
	 *
	 * @return Markup
	 */
	public function setDelimiters( $delimiters )
	{
		static::$_delimiters = $delimiters;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getDelimiters()
	{
		return static::$_delimiters;
	}

	/**
	 * @param boolean $uppercaseTags
	 */
	public static function setUppercaseTags( $uppercaseTags )
	{
		static::$_uppercaseTags = $uppercaseTags;
	}

	/**
	 * @return boolean
	 */
	public static function getUppercaseTags()
	{
		return static::$_uppercaseTags;
	}

	/**
	 * @param string|array $source
	 * @param string|array $value
	 * @param string       $delimiter If you are getting back a string, this is the implosion delimiter
	 *
	 * @return array|string
	 */
	public static function addValue( $source, $value, $delimiter = ' ' )
	{
		$_values = ( !is_array( $source ) ) ? explode( ' ', trim( $source ) ) : $source;
		$_newValues = Convert::createArray( $value );

		foreach ( $_newValues as $_newValue )
		{
			if ( !in_array( $_newValue, $_values ) )
			{
				$_values[] = $_newValue;
			}
		}

		return is_array( $source ) ? $_values : ltrim( implode( $delimiter, $_values ), $delimiter );
	}

	/**
	 * Cleans and trims a tag
	 *
	 * @param string $tag
	 *
	 * @return string
	 */
	protected static function _cleanTag( $tag )
	{
		return trim( static::$_uppercaseTags ? strtoupper( $tag ) : strtolower( $tag ) );
	}

	/**
	 * Cleans and trims a tag
	 *
	 * @param bool $open      Set to false to get the end tag
	 * @param bool $selfClose True if this is a self-closing open tag
	 *
	 * @internal param string $tag
	 *
	 * @return string
	 */
	protected static function _tagPattern( $open = true, $selfClose = false )
	{
		if ( true === $open )
		{
			return static::_openStartDelimiter( $selfClose ) . self::ValuePlaceholder . static::_openEndDelimiter( $selfClose );
		}

		return static::_closeStartDelimiter() . self::ValuePlaceholder . static::_closeEndDelimiter();
	}

	/**
	 * @param bool $selfClose
	 *
	 * @return string
	 */
	protected static function _openStartDelimiter( $selfClose = false )
	{
		return static::$_delimiters[( $selfClose ? static::SelfCloseStart : static::OpenStart )];
	}

	/**
	 * @param bool $selfClose
	 *
	 * @return string
	 */
	protected static function _openEndDelimiter( $selfClose = false )
	{
		return static::$_delimiters[( $selfClose ? static::SelfCloseEnd : static::OpenEnd )];
	}

	/**
	 * @return string
	 */
	protected static function _closeStartDelimiter()
	{
		return static::$_delimiters[static::CloseStart];
	}

	/**
	 * @return string
	 */
	protected static function _closeEndDelimiter()
	{
		return static::$_delimiters[static::CloseEnd];
	}
}
