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

use Kisma\Core\Interfaces\UtilityLike;

/**
 * Scalar
 * Scalar utility class
 */
class Scalar implements UtilityLike
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Ensures the end of a string has only one of something
	 *
	 * @param string $search
	 * @param string $oneWhat
	 *
	 * @return string
	 */
	public static function trimSingle( $search, $oneWhat = ' ' )
	{
		return trim( $oneWhat . $search . $oneWhat, $oneWhat );
	}

	/**
	 * Ensures the end of a string has only one of something
	 *
	 * @param string $search
	 * @param string $oneWhat
	 *
	 * @return string
	 */
	public static function rtrimSingle( $search, $oneWhat = ' ' )
	{
		return rtrim( $search . $oneWhat, $oneWhat );
	}

	/**
	 * Ensures the front of a string has only one of something
	 *
	 * @param string $search
	 * @param string $oneWhat
	 *
	 * @return string
	 */
	public static function ltrimSingle( $search, $oneWhat = ' ' )
	{
		return ltrim( $oneWhat . $search, $oneWhat );
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public static function boolval( $value )
	{
		if ( \is_bool( $value ) )
		{
			return $value;
		}

		$_value = \strtolower( (string)$value );

		//	FILTER_VALIDATE_BOOLEAN doesn't catch 'Y' or 'N', so convert to full words...
		if ( 'y' == $_value )
		{
			$_value = 'yes';
		}
		elseif ( 'n' == $_value )
		{
			$_value = 'no';
		}

		return \filter_var( $_value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Multi-argument is_array helper
	 *
	 * Usage: is_array( $array1[, $array2][, ...])
	 *
	 * @param mixed      $possibleArray
	 * @param mixed|null $_ [optional]
	 *
	 * @return bool
	 */
	public static function is_array( $possibleArray, $_ = null )
	{
		foreach ( func_get_args() as $_argument )
		{
			if ( !is_array( $_argument ) )
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Prepend an array
	 *
	 * @param array  $array
	 * @param string $string
	 * @param bool   $deep
	 *
	 * @return array
	 */
	public static function array_prepend( $array, $string, $deep = false )
	{
		if ( empty( $array ) || empty( $string ) )
		{
			return $array;
		}

		foreach ( $array as $key => $element )
		{
			if ( is_array( $element ) )
			{
				if ( $deep )
				{
					$array[$key] = self::array_prepend( $element, $string, $deep );
				}
				else
				{
					trigger_error( 'array_prepend: array element', E_USER_WARNING );
				}
			}
			else
			{
				$array[$key] = $string . $element;
			}
		}

		return $array;
	}

	/**
	 * Takes a list of things and returns them in an array as the values. Keys are maintained.
	 *
	 * @param ...
	 *
	 * @return array
	 */
	public static function argsToArray()
	{
		$_array = array();

		foreach ( func_get_args() as $_key => $_argument )
		{
			$_array[$_key] = $_argument;
		}

		//	Return the fresh array...
		return $_array;
	}

	/**
	 * Returns the first non-empty argument or null if none found.
	 * Allows for multiple nvl chains. Example:
	 *
	 *<code>
	 *    if ( null !== Option::nvl( $x, $y, $z ) ) {
	 *        //    none are null
	 *    } else {
	 *        //    One of them is null
	 *    }
	 *
	 * IMPORTANT NOTE!
	 * Since PHP evaluates the arguments before calling a function, this is NOT a short-circuit method.
	 *
	 * @return mixed
	 */
	public static function nvl()
	{
		$_default = null;
		$_args = func_num_args();
		$_haystack = func_get_args();

		for ( $_i = 0; $_i < $_args; $_i++ )
		{
			if ( null !== ( $_default = Option::get( $_haystack, $_i ) ) )
			{
				break;
			}
		}

		return $_default;
	}

	/**
	 * Convenience "in_array" method. Takes variable args.
	 *
	 * The first argument is the needle, the rest are considered in the haystack. For example:
	 *
	 * Option::in( 'x', 'x', 'y', 'z' ) returns true
	 * Option::in( 'a', 'x', 'y', 'z' ) returns false
	 *
	 * @internal param mixed $needle
	 * @internal param mixed $haystack
	 *
	 * @return bool
	 */
	public static function in()
	{
		$_haystack = func_get_args();

		if ( !empty( $_haystack ) && count( $_haystack ) > 1 )
		{
			$_needle = array_shift( $_haystack );

			return in_array( $_needle, $_haystack );
		}

		return false;
	}

	/**
	 * Shortcut for str(i)pos
	 *
	 * @static
	 *
	 * @param string $haystack
	 * @param string $needle
	 * @param bool   $caseSensitive
	 * @param int    $offset
	 *
	 * @return bool
	 */
	public static function within( $haystack, $needle, $offset = 0, $caseSensitive = false )
	{
		if ( false === $caseSensitive )
		{
			//	Case-insensitive
			return false !== stripos( $haystack, $needle, $offset );
		}

		//	Case-sensitive
		return false !== strpos( $haystack, $needle, $offset );
	}

	/**
	 * A case-insensitive "in_array" for all intents and purposes. Works with objects too!
	 *
	 * @param string       $needle
	 * @param array|object $haystack
	 * @param bool         $strict
	 *
	 * @return bool Returns true if found, false otherwise. Just like in_array
	 */
	public static function contains( $needle, $haystack, $strict = false )
	{
		foreach ( $haystack as $_index => $_value )
		{
			if ( is_string( $_value ) )
			{
				if ( 0 === strcasecmp( $needle, $_value ) )
				{
					return true;
				}
			}
			else if ( in_array( $needle, $_value, $strict ) )
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Takes the arguments and concatenates them with $separator in between.
	 *
	 * @param string $separator
	 *
	 * @return string
	 */
	public static function glue( $separator )
	{
		return implode( $separator, func_get_args() );
	}

	/**
	 * Generic array sorter
	 *
	 * To sort a column in descending order, assign 'desc' to the column's value in the defining array:
	 *
	 * $_columnsToSort = array(
	 *    'date' => 'desc',
	 *    'lastName' => 'asc',
	 *    'firstName' => 'asc',
	 * );
	 *
	 * @param array $arrayToSort
	 * @param array $columnsToSort Array of columns in $arrayToSort to sort.
	 *
	 * @return boolean
	 */
	public static function arraySort( &$arrayToSort, $columnsToSort = array() )
	{
		//	Convert to an array
		if ( !empty( $columnsToSort ) && !is_array( $columnsToSort ) )
		{
			$columnsToSort = array( $columnsToSort );
		}

		//	Any fields?
		if ( !empty( $columnsToSort ) )
		{
			return usort( $arrayToSort,
				function ( $a, $b ) use ( $columnsToSort )
				{
					$_result = null;

					foreach ( $columnsToSort as $_column => $_order )
					{
						$_order = trim( strtolower( $_order ) );

						if ( is_numeric( $_column ) && !static::in( $_order, 'asc', 'desc' ) )
						{
							$_column = $_order;
							$_order = null;
						}

						if ( 'desc' == strtolower( $_order ) )
						{
							return strnatcmp( $b[$_column], $a[$_column] );
						}

						return strnatcmp( $a[$_column], $b[$_column] );
					}
				} );
		}

		return false;
	}

	/**
	 * Sorts an array by a single column
	 *
	 * @param array  $sourceArray
	 * @param string $column
	 * @param int    $sortDirection
	 *
	 * @return bool
	 */
	public static function array_multisort_column( &$sourceArray, $column, $sortDirection = SORT_ASC )
	{
		$_sortColumn = array();

		foreach ( $sourceArray as $_key => $_row )
		{
			$_sortColumn[$_key] = ( isset( $_row[$column] ) ? $_row[$column] : null );
		}

		return \array_multisort( $_sortColumn, $sortDirection, $sourceArray );
	}
}
