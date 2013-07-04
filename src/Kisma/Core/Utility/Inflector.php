<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2013 Jerry Ablan <jerryablan@gmail.com>
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

use Kisma\Core\Interfaces\UtilityLike;

/**
 * Inflector
 * Provides Inflector manipulation routines
 */
class Inflector implements UtilityLike
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Given a string, return it to neutral format (lowercase, period and underscores)
	 *
	 * @param string $item  The string to neutralize
	 * @param string $strip If provided, it's value is removed from item before it's neutralized.
	 *                      Example: "REQUEST_URI" would be "URI" with $strip = "REQUEST_"
	 *
	 * @return string
	 */
	public static function neutralize( $item, $strip = null )
	{
		if ( null !== $strip )
		{
			$item = str_ireplace( $strip, null, $item );
		}

		//	Split by forward slash, backslash, period, or space...
		$_parts = preg_split( "/[. \/\\\\]+/", $item );

		array_walk(
			$_parts,
			function ( &$part )
			{
				//      Clean
				$part = Inflector::decamelize( $part );
			}
		);

		return implode( '.', $_parts );
	}

	/**
	 * Given a Kisma identifier, return it to neutral format (lowercase, period and underscores)
	 *
	 * Examples:
	 *       Class Name:            \Kisma\Core\Events\SeedEvent becomes "kisma.components.component_event"
	 *
	 * @param string $tag
	 *
	 * @return string
	 * @deprecated Please use neutralize()
	 */
	public static function untag( $tag )
	{
		return self::neutralize( $tag );
	}

	/**
	 * Given a string, return it to non-neutral format (delimited camel-case)
	 *
	 * @param string $item      The string to deneutralize
	 * @param bool   $isKey     True if the string is an array/object key/tag
	 * @param string $delimiter Will be used to reconstruct the string
	 *
	 * @return string
	 */
	public static function deneutralize( $item, $isKey = false, $delimiter = '\\' )
	{
		return self::camelize(
			str_replace(
				array( '_', '.', $delimiter ),
				' ',
				$item
			),
			'_',
			false,
			true
		);
	}

	/**
	 * Given a simple name, clean it up to a Kisma standard, camel-cased, format.
	 *
	 * Periods should be used to separate namespaces.
	 * Underscores should be used to separate identifier words
	 *
	 * Examples:
	 *       Class Name:            kisma.aspects.event_handling => \Kisma\Aspects\EventHandling
	 *       Array Key:            my_event => MyEvent
	 *
	 * @param string $tag
	 * @param bool   $isKey                If true, the $tag will be neutralized
	 * @param string $strip                If provided, it's value is removed from item before it's neutralized.
	 *                                     Example: "REQUEST_URI" would be "URI" with $strip = "REQUEST_"
	 *
	 * @return string
	 */
	public static function tag( $tag, $isKey = false, $strip = null )
	{
		if ( false !== $isKey )
		{
			return self::neutralize( $tag, $strip );
		}

		$_tag = self::deneutralize( $tag );

		return $_tag;
	}

	/**
	 * @param string $tag
	 * @param string $delimiter
	 *
	 * @return string
	 */
	public static function baseName( $tag, $delimiter = '\\' )
	{
		return @end( @explode( $delimiter, $tag ) );
	}

	/**
	 * Converts a separator delimited string to camel case
	 *
	 * @param string  $string
	 * @param string  $separator
	 * @param boolean $preserveWhiteSpace
	 * @param bool    $isKey If true, first word is lower-cased
	 *
	 * @return string
	 */
	public static function camelize( $string, $separator = '_', $preserveWhiteSpace = false, $isKey = false )
	{
		$_newString = ucwords( str_replace( $separator, ' ', $string ) );

		if ( false !== $isKey )
		{
			$_newString = lcfirst( $_newString );
		}

		return ( false === $preserveWhiteSpace ? str_replace( ' ', null, $_newString ) : $_newString );
	}

	/**
	 * Converts a camel-cased word to a delimited lowercase string
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function decamelize( $string )
	{
		return strtolower( preg_replace( "/([a-z])([A-Z])/", "\\1_\\2", $string ) );
	}

	/**
	 * Converts a word to its plural form. Totally swiped from Yii
	 *
	 * @param string $name the word to be pluralized
	 *
	 * @return string the pluralized word
	 */
	public static function pluralize( $name )
	{
		static $_rules
		= array(
			'/(quiz)$/i'                     => '1zes',
			'/^(ox)$/i'                      => '1en',
			'/([m|l])ouse$/i'                => '1ice',
			'/(matr|vert|ind)ix|ex$/i'       => '1ices',
			'/(x|ch|ss|sh)$/i'               => '1es',
			'/([^aeiouy]|qu)ies$/i'          => '1y',
			'/([^aeiouy]|qu)y$/i'            => '1ies',
			'/(hive)$/i'                     => '1s',
			'/(?:([^f])fe|([lr])f)$/i'       => '12ves',
			'/sis$/i'                        => 'ses',
			'/([ti])um$/i'                   => '1a',
			'/(buffal|tomat)o$/i'            => '1oes',
			'/(bu)s$/i'                      => '1ses',
			'/(alias|status)/i'              => '1es',
			'/(octop|vir)us$/i'              => '1i',
			'/(ax|test)is$/i'                => '1es',
			'/s$/i'                          => 's',
			'/$/'                            => 's',
			'/move$/i'                       => 'moves',
			'/foot$/i'                       => 'feet',
			'/child$/i'                      => 'children',
			'/human$/i'                      => 'humans',
			'/man$/i'                        => 'men',
			'/tooth$/i'                      => 'teeth',
			'/person$/i'                     => 'people',
			'/(x|ch|ss|sh|us|as|is|os)$/i'   => '\1es',
			'/(shea|lea|loa|thie)f$/i'       => '\1ves',
			'/(tomat|potat|ech|her|vet)o$/i' => '\1oes',
			'/s$/'                           => 's',
		);

		foreach ( $_rules as $_rule => $_replacement )
		{
			if ( preg_match( $_rule, $name ) )
			{
				return preg_replace( $_rule, $_replacement, $name );
			}
		}

		return $name . 's';
	}

	/**
	 * Smarter ucwords()
	 *
	 * @param string $words
	 * @param string $convertDelimiter Set to the delimiter you'd like replaced with spaces before conversion
	 *
	 * @return string
	 */
	public static function ucWordsBetter( $words, $convertDelimiter = null )
	{
		$_cleaned = null;

		if ( null !== $convertDelimiter )
		{
			//	Convert dots to spaces, then spaces to namespace separators.
			$words = trim( str_replace( $convertDelimiter, ' ', $words ) );
		}

		$_parts = explode( ' ', $words );

		if ( !empty( $_parts ) )
		{
			foreach ( $_parts as $_part )
			{
				$_cleaned .= ' ' . ( $_part != strtoupper( $_part ) ? ucwords( $_part ) : $_part );
			}
		}

		return trim( $_cleaned );
	}
}
