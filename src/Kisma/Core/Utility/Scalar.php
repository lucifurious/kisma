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
			if ( null !== ( $_default = self::o( $_haystack, $_i ) ) )
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
