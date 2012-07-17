<?php
/**
 * @file
 *            Provides ...
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license   http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author    Jerry Ablan <kisma@pogostick.com>
 * @category  Framework
 * @package   kisma
 * @since     1.0.0
 *
 * @ingroup   framework
 */
namespace Kisma\Utility;

/**
 * Option
 * Provides methods to manipulate option arrays and object
 */
class Option
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array      $options
	 * @param string     $key
	 * @param mixed|null $defaultValue
	 * @param boolean    $unsetValue
	 *
	 * @return mixed
	 */
	public static function get( &$options = array(), $key, $defaultValue = null, $unsetValue = false )
	{
		return self::o( $options, $key, $defaultValue, $unsetValue );
	}

	/**
	 * Retrieves an option from the given array. $defaultValue is set and returned if $_key is not 'set'.
	 * Optionally will unset option in array.
	 *
	 * @param array      $options
	 * @param string     $key
	 * @param mixed|null $defaultValue
	 * @param boolean    $unsetValue
	 *
	 * @return mixed
	 */
	public static function o( &$options = array(), $key, $defaultValue = null, $unsetValue = false )
	{
		$_originalKey = $key;

		//	Inflect pain!
		$key = Inflector::tag( $key, true );

		//	Set the default value
		$_newValue = $defaultValue;

		//	Get array value if it exists
		if ( is_array( $options ) )
		{
			//	Check for the original key too
			if ( array_key_exists( $_originalKey, $options ) )
			{
				$key = $_originalKey;
			}

			if ( array_key_exists( $key, $options ) )
			{
				$_newValue = $options[$key];

				if ( $unsetValue )
				{
					unset( $options[$key] );
				}
			}

			//	Set it in the array if not an unsetter...
			if ( !$unsetValue )
			{
				$options[$key] = $_newValue;
			}
		}
		//	Also now handle accessible object properties
		else
		{
			if ( is_object( $options ) )
			{
				if ( property_exists( $options, $_originalKey ) )
				{
					$key = $_originalKey;
				}

				if ( property_exists( $options, $key ) )
				{
					if ( isset( $options->$key ) )
					{
						$_newValue = $options->$key;

						if ( $unsetValue )
						{
							unset( $options->$key );
						}
					}

					if ( !$unsetValue )
					{
						$options->$key = $_newValue;
					}
				}
			}
		}

		//	Return...
		return $_newValue;
	}

	/**
	 * @param array      $options
	 * @param string     $key
	 * @param string     $subKey
	 * @param mixed      $defaultValue Only applies to target value
	 * @param boolean    $unsetValue   Only applies to target value
	 *
	 * @return mixed
	 */
	public static function oo( &$options = array(), $key, $subKey, $defaultValue = null, $unsetValue = false )
	{
		return self::o( self::o( $options, $key, array() ), $subKey, $defaultValue, $unsetValue );
	}

	/**
	 * @param array  $options
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	public static function set( &$options = array(), $key, $value = null )
	{
		return self::so( $options, $key, $value );
	}

	/**
	 * Sets an value in the given array at key.
	 *
	 * @param array|object $options
	 * @param string       $key
	 * @param mixed|null   $value
	 *
	 * @return mixed The new value of the key
	 */
	public static function so( &$options = array(), $key, $value = null )
	{
		$_originalKey = $key;

		$key = Inflector::tag( $key, true );

		if ( is_array( $options ) )
		{
			//	Check for the original key too
			if ( !array_key_exists( $key, $options ) && array_key_exists( $_originalKey, $options ) )
			{
				$key = $_originalKey;
			}

			if ( null === $value )
			{
				unset( $options[$key] );
			}
			else
			{
				return $options[$key] = $value;
			}
		}
		else
		{
			if ( is_object( $options ) )
			{
				if ( !property_exists( $options, $key ) && property_exists( $options, $_originalKey ) )
				{
					$key = $_originalKey;
				}

				if ( null === $value )
				{
					unset( $options->{$key} );
				}
				else
				{
					return $options->$key = $value;
				}
			}
		}

		return null;
	}

	/**
	 * @param array  $options
	 * @param string $key
	 *
	 * @return mixed The last value of the key
	 */
	public static function remove( &$options = array(), $key )
	{
		return self::uo( $options, $key );
	}

	/**
	 * Unsets an option in the given array
	 *
	 * @param array  $options
	 * @param string $key
	 *
	 * @return mixed The new value of the key
	 */
	public static function uo( &$options = array(), $key )
	{
		return self::so( $options, $key, null, true );
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
	 * Ensures the argument passed in is actually an array
	 *
	 * @static
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public static function clean( $array = null )
	{
		if ( empty( $array ) || !is_array( $array ) )
		{
			$array = array();
		}

		return $array;
	}

	/**
	 * Merge one or more arrays but ensures each is an array. Basically an idiot-proof array_merge
	 *
	 * @param array $target The destination array
	 *
	 * @return array The resulting array
	 */
	public static function merge( $target )
	{
		$_arrays = self::clean( func_get_args() );
		$_target = self::clean( array_shift( $_arrays ) );

		foreach ( $_arrays as $_array )
		{
			$_target = array_merge(
				$_target,
				self::clean( $_array )
			);

			unset( $_array );
		}

		unset( $_arrays );
		return $_target;
	}

}