<?php
/**
 * @file
 *            Provides Inflector manipulation utilities
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license   http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author    Jerry Ablan <kisma@pogostick.com>
 * @category  Utilities
 * @package   kisma.utility
 * @since     1.0.0
 *
 * @ingroup   utilities
 */
namespace Kisma\Core\Utility;

/**
 * Inflector
 * Provides Inflector manipulation routines
 */
class Inflector implements \Kisma\Core\Interfaces\UtilityLike
{
	//*************************************************************************
	//* Public Methods
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

		$_parts = preg_split( "/(\w\\\.\/)+/", $item );

		array_walk( $_parts,
			function ( &$part )
			{
				//      Clean
				$part = static::decamelize( $part );
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
	 * @param string      $tag
	 *
	 * @return string
	 * @deprecated Please use neutralize()
	 */
	public static function untag( $tag )
	{
		return static::neutralize( $tag );
//
//		$_parts = explode( '\\', $tag );
//
//		array_walk( $_parts,
//			function ( &$part )
//			{
//				//      Replace
//				$part = strtolower(
//					$part == strtoupper( $part )
//						?
//						$part
//						:
//						preg_replace( '/(?<=\\w)([A-Z])/', '_\\1', $part )
//				);
//			}
//		);
//
//		return implode( '.', $_parts );
	}

	/**
	 * Given a string, return it to non-neutral format (delimited camel-case)
	 *
	 * @param string $item      The string to deneutralize
	 * @param string $delimiter Will be used to reconstruct the string
	 *
	 * @return string
	 */
	public static function deneutralize( $item, $delimiter = '\\' )
	{
		$_result = static::camelize(
			str_replace(
				' ',
				null,
				str_replace(
					array( '_', '.' ),
					array( ' ', $delimiter ),
					$item
				)
			)
		);

		return $_result;
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
	 * @param string      $tag
	 * @param bool        $isKey           If true, the $tag will be neutralized
	 * @param string      $strip           If provided, it's value is removed from item before it's neutralized.
	 *                                     Example: "REQUEST_URI" would be "URI" with $strip = "REQUEST_"
	 *
	 * @return string
	 */
	public static function tag( $tag, $isKey = false, $strip = null )
	{
		if ( false !== $isKey )
		{
			return static::neutralize( $tag, $strip );
		}

		$_tag = static::deneutralize( $tag );

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
	 *
	 * @return string
	 */
	public static function camelize( $string, $separator = '_', $preserveWhiteSpace = false )
	{
		$_newString = ucwords( str_replace( $separator, ' ', $string ) );

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
		static $_rules = array(
			'/move$/i'                       => 'moves',
			'/foot$/i'                       => 'feet',
			'/child$/i'                      => 'children',
			'/human$/i'                      => 'humans',
			'/man$/i'                        => 'men',
			'/tooth$/i'                      => 'teeth',
			'/person$/i'                     => 'people',
			'/([m|l])ouse$/i'                => '\1ice',
			'/(x|ch|ss|sh|us|as|is|os)$/i'   => '\1es',
			'/([^aeiouy]|qu)y$/i'            => '\1ies',
			'/(?:([^f])fe|([lr])f)$/i'       => '\1\2ves',
			'/(shea|lea|loa|thie)f$/i'       => '\1ves',
			'/([ti])um$/i'                   => '\1a',
			'/(tomat|potat|ech|her|vet)o$/i' => '\1oes',
			'/(bu)s$/i'                      => '\1ses',
			'/(ax|test)is$/i'                => '\1es',
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
	 * @internal param bool $dotted If true, dots will be replaced with spaces before the UCing
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
