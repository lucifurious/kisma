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
 * Option
 * Super kick-ass class to manipulate array and object properties in a uniform manner
 */
class Option
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param array  $options
	 * @param string $key
	 *
	 * @return bool
	 */
	public static function contains( $options = array(), $key )
	{
		$_key = static::_cleanKey( $key );

		//	Check both the raw and cooked keys
		return
			( is_array( $options ) && ( isset( $options[$key] ) || isset( $options[$_key] ) ) ) ||
			( is_object( $options ) && ( property_exists( $options, $key ) || property_exists( $options, $_key ) ) );
	}

	/**
	 * @param array   $options
	 * @param array   $keys
	 * @param mixed   $defaultValue
	 * @param boolean $unsetValue        If true, the $key will be removed from $options after retrieval
	 * @param bool    $emptyStringIsNull If true, empty() values will always return as NULL
	 *
	 * @return array
	 */
	public static function getMany( &$options = array(), $keys, $defaultValue = null, $unsetValue = false, $emptyStringIsNull = false )
	{
		$_results = array();
		$_keys = static::collapse( $keys, $defaultValue );

		foreach ( $_keys as $_key )
		{
			$_results[$_key] = static::get( $options, $_key, $defaultValue, $unsetValue, $emptyStringIsNull );
		}

		return $_results;
	}

	/**
	 * Retrieves an option from the given array.
	 *
	 * $defaultValue is returned if $key is not found.
	 * Can optionally delete $key from $options.
	 *
	 * @param array|\ArrayAccess|object $options           An array or object from which to get $key's value
	 * @param string                    $key               The array index or property to retrieve from $options
	 * @param mixed                     $defaultValue      The value to return if $key is not found
	 * @param boolean                   $unsetValue        If true, the $key will be removed from $options after retrieval
	 * @param bool                      $emptyStringIsNull If true, empty() values will always return as NULL
	 *
	 * @return mixed
	 */
	public static function get( &$options = array(), $key, $defaultValue = null, $unsetValue = false, $emptyStringIsNull = false )
	{
		//	Get many?
		if ( is_array( $key ) )
		{
			return static::getMany( $options, $key, $defaultValue, $unsetValue );
		}

		//	Now a deep search
		$_originalKey = $key;

		//	Inflect pain!
		$key = static::_cleanKey( $key );

		//	Set the default value
		$_newValue = $defaultValue;

		//	Get array value if it exists
		if ( is_array( $options ) || $options instanceof \ArrayAccess )
		{
			//	Check for the original key too
			if ( !isset( $options[$key] ) && isset( $options[$_originalKey] ) )
			{
				$key = $_originalKey;
			}

			if ( isset( $options[$key] ) )
			{
				$_newValue = $options[$key];

				if ( false !== $unsetValue )
				{
					unset( $options[$key] );
				}

				return $emptyStringIsNull && empty( $_newValue ) ? null : $_newValue;
			}
		}

		if ( is_object( $options ) )
		{
			if ( !property_exists( $options, $key ) && property_exists( $options, $_originalKey ) )
			{
				$key = $_originalKey;
			}

			if ( isset( $options->{$key} ) )
			{
				$_newValue = $options->{$key};

				if ( false !== $unsetValue )
				{
					unset( $options->{$key} );
				}

				return $emptyStringIsNull && empty( $_newValue ) ? null : $_newValue;
			}
			else if ( method_exists( $options, 'get' . $key ) )
			{
				$_getter = 'get' . Inflector::deneutralize( $key );
				$_setter = 'set' . Inflector::deneutralize( $key );

				$_newValue = $options->{$_getter}();

				if ( false !== $unsetValue && method_exists( $options, $_setter ) )
				{
					$options->{$_setter}( null );
				}
			}
		}

		//	Return the default...
		return $emptyStringIsNull && empty( $_newValue ) ? null : $_newValue;
	}

	/**
	 * @param array|\ArrayAccess|object $options
	 * @param string                    $key
	 * @param string                    $subKey
	 * @param mixed                     $defaultValue      Only applies to target value
	 * @param boolean                   $unsetValue        If true, the $key will be removed from $options after retrieval
	 * @param bool                      $emptyStringIsNull If true, empty() values will always return as NULL
	 *
	 * @return mixed
	 */
	public static function getDeep( &$options = array(), $key, $subKey, $defaultValue = null, $unsetValue = false, $emptyStringIsNull = false )
	{
		$_deep = static::get( $options, $key, array(), $unsetValue, $emptyStringIsNull );

		return static::get( $_deep, $subKey, $defaultValue, $unsetValue, $emptyStringIsNull );
	}

	/**
	 * Retrieves a boolean option from the given array. $defaultValue is set and returned if $_key is not 'set'.
	 * Optionally will unset option in array.
	 *
	 * Returns TRUE for "1", "true", "on", "yes" and "y". Returns FALSE otherwise.
	 *
	 * @param array|\ArrayAccess|object $options
	 * @param string                    $key
	 * @param boolean                   $defaultValue Defaults to false
	 * @param boolean                   $unsetValue   If true, the $key will be removed from $options after retrieval
	 *
	 * @return mixed
	 */
	public static function getBool( &$options = array(), $key, $defaultValue = false, $unsetValue = false )
	{
		return Scalar::boolval( static::get( $options, $key, $defaultValue, $unsetValue ) );
	}

	/**
	 * Adds a value to a property array
	 *
	 * @param array  $source
	 * @param string $key
	 * @param string $subKey
	 * @param mixed  $value
	 *
	 * @return array The new array
	 */
	public static function addTo( &$source, $key, $subKey, $value = null )
	{
		$_target = static::clean( static::get( $source, $key, array() ) );
		static::set( $_target, $subKey, $value );
		static::set( $source, $key, $_target );

		return $_target;
	}

	/**
	 * Removes a value from a property array
	 *
	 * @param array  $source
	 * @param string $key
	 * @param string $subKey
	 *
	 * @return mixed The original value of the removed key
	 */
	public static function removeFrom( &$source, $key, $subKey )
	{
		$_target = static::clean( static::get( $source, $key, array() ) );
		$_result = static::remove( $_target, $subKey );
		static::set( $source, $key, $_target );

		return $_result;
	}

	/**
	 * @param array $target
	 * @param array $data Array of key => value pairs to set
	 *
	 * @return bool
	 */
	public static function setMany( &$target = array(), $data )
	{
		foreach ( Option::clean( $data ) as $_key => $_value )
		{
			static::set( $target, $_key, $_value );
		}

		return true;
	}

	/**
	 * Sets an value in the given array at key.
	 *
	 * @param array|\ArrayAccess|object $options           The array or object from which to set $key's $value
	 * @param string|array              $key               The array index or property to set
	 * @param mixed                     $value             The value to set
	 * @param bool                      $emptyStringIsNull If true, empty() values will always be set as NULL
	 *
	 * @return array|string
	 */
	public static function set( &$options = array(), $key, $value = null, $emptyStringIsNull = false )
	{
		if ( is_array( $key ) )
		{
			return static::setMany( $options, $key );
		}

		$_options = static::collapse( $key, $value );

		foreach ( $_options as $_key => $_value )
		{
			$_cleanKey = static::_cleanKey( $_key );

			if ( is_array( $options ) )
			{
				//	Check for the original key too
				if ( !array_key_exists( $_key, $options ) && array_key_exists( $_cleanKey, $options ) )
				{
					$_key = $_cleanKey;
				}

				$options[$_key] = $emptyStringIsNull && empty( $_value ) ? null : $_value;

				continue;
			}

			if ( is_object( $options ) )
			{
				$_setter = 'set' . Inflector::deneutralize( $_key );

				//	Prefer setter, if one...
				if ( method_exists( $options, $_setter ) )
				{
					$options->{$_setter}( $emptyStringIsNull && empty( $_value ) ? null : $_value );
				}
				else
				{
					if ( !property_exists( $options, $_key ) && property_exists( $options, $_cleanKey ) )
					{
						$_key = $_cleanKey;
					}

					//	Set it verbatim
					$options->{$_key} = $emptyStringIsNull && empty( $_value ) ? null : $_value;
				}
			}
		}
	}

	/**
	 * Unsets an option in the given array
	 *
	 * @param array  $options
	 * @param string $key
	 *
	 * @return mixed
	 */
	public static function remove( &$options = array(), $key )
	{
		$_originalValue = null;

		if ( static::contains( $options, $key ) )
		{
			$_cleanedKey = static::_cleanKey( $key );

			if ( is_array( $options ) )
			{
				if ( !isset( $options[$key] ) && isset( $options[$_cleanedKey] ) )
				{
					$key = $_cleanedKey;
				}

				if ( isset( $options[$key] ) )
				{
					$_originalValue = $options[$key];
					unset( $options[$key] );
				}
			}
			else
			{
				if ( !isset( $options->{$key} ) && isset( $options->{$_cleanedKey} ) )
				{
					$key = $_cleanedKey;
				}

				if ( isset( $options->{$key} ) )
				{
					$_originalValue = $options->{$key};
				}

				unset( $options->{$key} );
			}
		}

		return $_originalValue;
	}

	/**
	 * Ensures the argument passed in is actually an array with optional iteration callback
	 *
	 * @static
	 *
	 * @param array             $array
	 * @param callable|\Closure $callback
	 *
	 * @return array
	 */
	public static function clean( $array = null, $callback = null )
	{
		$_result = ( empty( $array ) ? array() : ( !is_array( $array ) ? array( $array ) : $array ) );

		if ( null === $callback || !is_callable( $callback ) )
		{
			return $_result;
		}

		$_response = array();

		foreach ( $_result as $_item )
		{
			$_response[] = call_user_func( $callback, $_item );
		}

		return $_response;
	}

	/**
	 * Converts $key and $value into array($key => $value) if $key is not already an array.
	 *
	 * @static
	 *
	 * @param string|array $key
	 * @param mixed        $value
	 *
	 * @return array
	 */
	public static function collapse( $key, $value = null )
	{
		return ( is_array( $key ) && null === $value )
			? $key
			: array(
				$key => $value
			);
	}

	/**
	 * Merge one or more arrays but ensures each is an array. Basically an idiot-proof array_merge
	 *
	 * @param array $target The destination array
	 *
	 * @return array The resulting array
	 * @return array
	 */
	public static function merge( $target )
	{
		$_arrays = static::clean( func_get_args() );
		$_target = static::clean( array_shift( $_arrays ) );

		foreach ( $_arrays as $_array )
		{
			$_target = array_merge( $_target,
				static::clean( $_array ) );

			unset( $_array );
		}

		unset( $_arrays );

		return $_target;
	}

	/**
	 * Wrapper for a static::get on $_SERVER
	 *
	 * @param string $key
	 * @param string $defaultValue
	 * @param bool   $unsetValue
	 *
	 * @return mixed
	 */
	public static function server( $key, $defaultValue = null, $unsetValue = false )
	{
		return static::get( $_SERVER, $key, $defaultValue, $unsetValue );
	}

	/**
	 * Wrapper for a static::get on $_REQUEST
	 *
	 * @param string $key
	 * @param string $defaultValue
	 * @param bool   $unsetValue
	 *
	 * @return mixed
	 */
	public static function request( $key, $defaultValue = null, $unsetValue = false )
	{
		return static::get( $_REQUEST, $key, $defaultValue, $unsetValue );
	}

	/**
	 * Sets a value within an array only if the value is not set (SetIfNotSet=SINS).
	 * You can pass in an array of key value pairs and do many at once.
	 *
	 * @param \stdClass|array $options
	 * @param string          $key
	 * @param mixed           $value
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
			if ( !static::contains( $options, $_key ) )
			{
				static::set( $options, $_key, $_value );
			}
		}
	}

	/**
	 * Converts key to a neutral format if not already...
	 *
	 * @param string $key
	 * @param bool   $opposite If true, the key is switched back to it's neutral or deneutral format
	 *
	 * @return string
	 */
	protected static function _cleanKey( $key, $opposite = true )
	{
		if ( $key == ( $_cleaned = Inflector::neutralize( $key ) ) )
		{
			if ( false !== $opposite )
			{
				return Inflector::deneutralize( $key, true );
			}
		}

		return $_cleaned;
	}

	/**
	 * Spins through an array and prefixes the keys with a string
	 *
	 * @param string $prefix
	 * @param array  $data
	 *
	 * @return mixed
	 */
	public static function prefixKeys( $prefix, array $data = array() )
	{
		foreach ( static::clean( $data ) as $_key => $_value )
		{
			if ( is_numeric( $_key ) )
			{
				continue;
			}

			if ( is_array( $_value ) )
			{
				$_value = static::prefixKeys( $prefix, $_value );
			}

			$data[$prefix . $_key] = $_value;
			unset( $data[$_key] );
		}

		return $data;
	}
}
