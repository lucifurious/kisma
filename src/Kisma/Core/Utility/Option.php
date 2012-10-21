<?php
/**
 * Option.php
 */
namespace Kisma\Core\Utility;
/**
 * Option
 * Provides methods to manipulate array and object properties
 */
class Option
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array  $options
	 * @param string $key
	 *
	 * @return bool
	 */
	public static function contains( &$options = array(), $key )
	{
		$_key = Inflector::tag( $key, true );

		//	Check both the raw and cooked keys
		return
			( is_array( $options ) && ( isset( $options[$key] ) || isset( $options[$_key] ) ) )
			||
			( is_object( $options ) && ( property_exists( $options, $key ) || property_exists( $options, $_key ) ) );
	}

	/**
	 * @param array $options
	 * @param array $keys
	 * @param mixed $defaultValue
	 *
	 * @return array
	 */
	public static function getMany( &$options = array(), $keys, $defaultValue = null )
	{
		$_results = array();
		$_keys = self::collapse( $keys, $defaultValue );

		foreach ( $_keys as $_key )
		{
			$_results[$_key] = self::get( $options, $_key, $defaultValue );
		}

		return $_results;
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
	public static function get( &$options = array(), $key, $defaultValue = null, $unsetValue = false )
	{
		if ( is_array( $key ) )
		{
			return self::getMany( $options, $key, $defaultValue, $unsetValue );
		}

		$_originalKey = $key;

		//	Inflect pain!
		$key = Inflector::tag( $key, true );

		//	Set the default value
		$_newValue = $defaultValue;

		//	Get array value if it exists
		if ( is_array( $options ) )
		{
			//	Check for the original key too
			if ( !array_key_exists( $key, $options ) && array_key_exists( $_originalKey, $options ) )
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

				return $_newValue;
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

				return $_newValue;
			}
		}

		//	Return the default...
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
	public static function getDeep( &$options = array(), $key, $subKey, $defaultValue = null, $unsetValue = false )
	{
		return self::get( self::get( $options, $key, array() ), $subKey, $defaultValue, $unsetValue );
	}

	/**
	 * Sets an value in the given array at key.
	 *
	 * @param array|object $options
	 * @param string|array $key Pass a single key or an array of KVPs
	 * @param mixed|null   $value
	 *
	 * @return void
	 */
	public static function set( &$options = array(), $key, $value = null )
	{
		$_options = self::collapse( $key, $value );

		foreach ( $_options as $_key => $_value )
		{
			$_originalKey = $_key;
			$_key = Inflector::tag( $_key, true );

			if ( is_array( $options ) )
			{
				//	Check for the original key too
				if ( !array_key_exists( $_key, $options ) && array_key_exists( $_originalKey, $options ) )
				{
					$_key = $_originalKey;
				}

				$options[$_key] = $_value;
			}

			if ( is_object( $options ) )
			{
				if ( !property_exists( $options, $_key ) && property_exists( $options, $_originalKey ) )
				{
					$_key = $_originalKey;
				}

				$options->{$_key} = $_value;
			}
		}
	}

	/**
	 * Unsets an option in the given array
	 *
	 * @param array  $options
	 * @param string $key
	 */
	public static function remove( &$options = array(), $key )
	{
		$_originalValue = null;

		if ( self::contains( $options, $key ) )
		{
			if ( is_array( $options ) )
			{
				$key = Inflector::tag( $key, true );

				if ( isset( $options[$key] ) )
				{
					$_originalValue = $options[$key];
				}

				unset( $options[$key] );
			}
			else
			{
				if ( isset( $options->$key ) )
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

		if ( null === $callback || is_callable( $callback ) )
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
			?
			$key
			:
			array(
				$key => $value
			);
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

	/**
	 * Wrapper for a self::get on $_SERVER
	 *
	 * @param string $key
	 * @param string $defaultValue
	 * @param bool   $unsetValue
	 *
	 * @return mixed
	 */
	public static function server( $key, $defaultValue = null, $unsetValue = false )
	{
		return self::get( $_SERVER, $key, $defaultValue, $unsetValue );
	}

	/**
	 * Wrapper for a self::get on $_REQUEST
	 *
	 * @param string $key
	 * @param string $defaultValue
	 * @param bool   $unsetValue
	 *
	 * @return mixed
	 */
	public static function request( $key, $defaultValue = null, $unsetValue = false )
	{
		return self::get( $_REQUEST, $key, $defaultValue, $unsetValue );
	}
}