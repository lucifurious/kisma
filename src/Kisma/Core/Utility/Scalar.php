<?php
/**
 * Scalar.php
 */
namespace Kisma\Core\Utility;
/**
 * Scalar
 * Scalar utility class
 */
class Scalar implements \Kisma\Core\Interfaces\SeedUtility
{
	//*************************************************************************
	//* Public Methods
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
	 * Tests if a value has been serialized
	 *
	 * @param string $value
	 *
	 * @return boolean
	 */
	public static function serialized( $value )
	{
		$_result = @unserialize( $value );

		return !( false === $_result && $value != serialize( false ) );
	}

	/**
	 * Sets a value within an array only if the value is not set (SetIfNotSet=SINS).
	 * You can pass in an array of key value pairs and do many at once.
	 *
	 * @param \stdClass|array $options
	 * @param string          $key
	 * @param mixed           $value
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
			$_key = \Kisma\Core\Utility\Inflector::tag( $_key, true );

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
