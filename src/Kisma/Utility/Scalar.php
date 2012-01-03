<?php
/**
 * Scalar.php
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link		  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license	   http://github.com/Pogostick/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @category	  Kisma_Utility
 * @package	   kisma.utility
 * @namespace	 \Kisma\Utility
 * @since		 v1.0.0
 * @filesource
 */

namespace Kisma\Utility;

use Kisma\Kisma;

/**
 * Scalar
 * Helpers for working with scalars
 */
class Scalar implements \Kisma\IUtility
{
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
	 * Generic super-easy/lazy way to convert lots of different things (like SimpleXmlElement) to an array
	 *
	 * @param object $object
	 *
	 * @return array|object
	 * @return array|false
	 */
	public static function toArray( $object )
	{
		if ( is_object( $object ) )
		{
			$object = (array)$object;
		}

		if ( !is_array( $object ) )
		{
			return $object;
		}

		$_result = array();

		foreach ( $object as $_key => $_value )
		{
			$_result[preg_replace( "/^\\0(.*)\\0/", "", $_key )] = self::toArray( $_value );
		}

		return $_result;
	}

	/**
	 * NVL = Null VaLue. Copycat function from PL/SQL. Pass in a list of arguments and the first non-null
	 * item is returned. Good for setting default values, etc. Last non-null value in list becomes the
	 * new "default value".
	 * NOTE: Since PHP evaluates the arguments before calling a function, this is NOT a short-circuit method.
	 *
	 * @param mixed [optional]
	 *
	 * @return mixed
	 */
	public static function nvl()
	{
		$_defaultValue = null;

		foreach ( func_get_args() as $_argument )
		{
			if ( null === $_argument )
			{
				continue;
			}

			$_defaultValue = $_argument;
		}

		return $_defaultValue;
	}

	/**
	 * Convenience "in_array" method. Takes variable args.
	 * The first argument is the needle, the rest are considered in the haystack. For example:
	 * Kisma::in( 'x', 'x', 'y', 'z' ) returns true
	 * Kisma::in( 'a', 'x', 'y', 'z' ) returns false
	 *
	 * @param mixed [optional]
	 *
	 * @return boolean
	 */
	public static function in()
	{
		//	Clever or dumb? Dunno...
		$_haystack = func_get_args();
		$_needle = array_shift( $_haystack );

		return in_array( $_needle, $_haystack );
	}

	/**
	 * Shortcut for str(i)pos
	 *
	 * @static
	 *
	 * @param string $haystack
	 * @param string $needle
	 * @param bool   $caseSensitive
	 * @param int	$offset
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
	 * Tests if a value needs unserialization
	 *
	 * @param string $value
	 *
	 * @return boolean
	 */
	public static function isSerialized( $value )
	{
		$_result = @unserialize( $value );
		return !( false === $_result && $value != serialize( false ) );
	}

	/**
	 * Sets a value within an array only if the value is not set (SetIfNotSet=SINS).
	 * You can pass in an array of key value pairs and do many at once.
	 *
	 * @param \stdClass|array $options
	 * @param string		  $key
	 * @param mixed		   $value
	 *
	 * @return bool
	 */
	public static function sins( &$options = array(), $key, $value = null )
	{
		//	Accept an array as input or single KVP
		if ( !is_array( $key ) )
		{
			$key = array( $key => $value );
		}

		foreach ( $key as $_key => $_value )
		{
			$_key = \Kisma\Utility\Inflector::tag( $_key, true );

			//	If the key is set, we bail...
			if ( is_array( $options ) )
			{
				if ( isset( $options[$_key] ) )
				{
					return false;
				}

				$options[$_key] = $_value;
				return true;
			}

			if ( is_object( $options ) )
			{
				if ( isset( $options->{$_key} ) )
				{
					return false;
				}

				$options->{$_key} = $_value;
				return true;
			}
		}

		//	Sorry charlie...
		return false;
	}

}
