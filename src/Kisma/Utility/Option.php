<?php
/**
 * @file
 * Provides ...
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Framework
 * @package kisma
 * @since 1.0.0
 *
 * @ingroup framework
 */

namespace Kisma\Utility;

//*************************************************************************
//* Aliases
//*************************************************************************

//*************************************************************************
//* Requirements
//*************************************************************************

/**
 * Option
 * Provides methods to manipulate option arrays and object
 */
class Option extends \Kisma\Components\Seed implements \Kisma\IUtility
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Alias for {@link \Kisma\Kisma::o)
	 *
	 * @param array	  $options
	 * @param string	 $key
	 * @param mixed|null $defaultValue
	 * @param boolean	$unsetValue
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
	 * @param array	  $options
	 * @param string	 $key
	 * @param mixed|null $defaultValue
	 * @param boolean	$unsetValue
	 *
	 * @return mixed
	 */
	public static function o( &$options = array(), $key, $defaultValue = null, $unsetValue = false )
	{
		$_originalKey = $key;

		//	Set the default value
		$_newValue = $defaultValue;

		//	Get array value if it exists
		if ( is_array( $options ) )
		{
			//	Check for the original key too
			if ( isset( $options[$_originalKey] ) )
			{
				$key = $_originalKey;
			}

			if ( isset( $options[$key] ) )
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
		else if ( is_object( $options ) )
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

		//	Return...
		return $_newValue;
	}

	/**
	 * Similar to {@link \Kisma\Kisma::o} except it will pull a value from a nested array.
	 *
	 * @param array	  $options
	 * @param string	 $key
	 * @param string	 $subKey
	 * @param mixed	  $defaultValue Only applies to target value
	 * @param boolean	$unsetValue   Only applies to target value
	 *
	 * @return mixed
	 */
	public static function oo( &$options = array(), $key, $subKey, $defaultValue = null, $unsetValue = false )
	{
		return self::o( self::o( $options, $key, array() ), $subKey, $defaultValue, $unsetValue );
	}

	/**
	 * Alias for {@link \Kisma\Kisma::so}
	 *
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
	 * @param string	   $key
	 * @param mixed|null   $value
	 *
	 * @return mixed The new value of the key
	 */
	public static function so( &$options = array(), $key, $value = null )
	{
		if ( is_array( $options ) )
		{
			return $options[$key] = $value;
		}
		else if ( is_object( $options ) )
		{
			return $options->$key = $value;
		}

		return null;
	}

	/**
	 * Alias of {@link \Kisma\Kisma::unsetOption}
	 *
	 * @param array  $options
	 * @param string $key
	 *
	 * @return mixed The last value of the key
	 */
	public static function unsetOption( &$options = array(), $key )
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
		return self::o( $options, $key, null, true );
	}

}