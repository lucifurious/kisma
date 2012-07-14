<?php
/**
 * @file
 * Provides Inflector manipulation utilities
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Utilities
 * @package kisma.utility
 * @since 1.0.0
 *
 * @ingroup utilities
 */

namespace Kisma\Utility;

/**
 * Inflector
 * Provides Inflector manipulation routines
 */
class Inflector implements \Kisma\IUtility
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Given a Kisma identifier, return it to neutral format (lowercase, period and underscores)
	 *
	 * Examples:
	 *	   Class Name:			\Kisma\Components\ComponentEvent becomes "kisma.components.component_event"
	 *
	 * @param string $tag
	 *
	 * @return string
	 */
	public static function untag( $tag )
	{
		$_parts = explode( '\\', $tag );

		array_walk( $_parts, function( &$part )
		{
			//      Replace
			$part = strtolower( preg_replace( '/(?<=\\w)([A-Z])/', '_\\1', $part ) );
		} );

		return implode( '.', $_parts );
	}

	/**
	 * Given a simple name, clean it up to a Kisma standard, camel-cased, format.
	 *
	 * Periods should be used to separate namespaces.
	 * Underscores should be used to separate identifier words
	 *
	 * Examples:
	 *	   Class Name:			kisma.aspects.event_handling => \Kisma\Aspects\EventHandling
	 *	   Array Key:			my_event => MyEvent
	 *
	 * @param string $tag
	 * @param bool   $isKey If true, the $tag will be converted to a format suitable for use as an array key
	 * @param bool   $baseNameOnly If true, only the final, base of the tag will be returned.
	 * @param array  $keyParts
	 *
	 * @return string
	 */
	public static function tag( $tag, $isKey = false, $baseNameOnly = false, &$keyParts = array() )
	{
		//	If we're dotted, clean up
		if ( false !== strpos( $tag, '.' ) )
		{
			//	Convert dots to spaces, then spaces to namespace separators.
			$tag = str_replace( ' ', '\\', ucwords( trim( str_replace( '.', ' ', $tag ) ) ) );
		}

		//	Convert underscores to spaces, then remove spaces
		$_tag = str_replace( ' ', null, ucwords( trim( str_replace( '_', ' ', $tag ) ) ) );

		//	Only the base?
		if ( false !== $baseNameOnly )
		{
			//	If this is a key, just get the last part
			$_tag = end( explode( '\\', $_tag ) );
		}

		//	Make it a key?
		if ( false !== $isKey && isset( $keyParts ) )
		{
			//	Convert namespace separators to dots
			$keyParts = explode( '.', $_tag = self::untag( $_tag ) );
		}

		return $_tag;
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
		return ( false === $preserveWhiteSpace ? str_replace( ' ', '', $_newString ) : $_newString );
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

}
