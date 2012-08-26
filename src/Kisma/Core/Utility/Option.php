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
		if ( is_array( $options ) && isset( $options[$key] ) )
		{
			unset( $options[$key] );
		}

		if ( is_object( $options ) && property_exists( $options, $key ) )
		{
			unset( $options->{$key} );
		}
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
		return ( empty( $array ) || !is_array( $array ) ) ? array() : $array;
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

}