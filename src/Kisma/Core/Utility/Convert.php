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
use Kisma\Core\Seed;

/**
 * Convert
 */
class Convert extends Seed implements UtilityLike
{
	/**
	 * Dynamically generates the object from the declared properties of the given object or array
	 *
	 * @param array|object $object
	 *
	 * @return \stdClass
	 */
	public static function toObject( $object )
	{
		//	If we can't iterate over the thing, we bail
		if ( !is_object( $object ) && !is_array( $object ) && !( $object instanceof \Traversable ) )
		{
			return null;
		}

		if ( is_array( $object ) )
		{
			//	Convert to an object
			$_properties = new \stdClass();

			foreach ( $object as $_key => $_value )
			{
				$_properties->{$_key} = $_value;
			}
		}
		else
		{
			$_me = new \ReflectionObject( $object );
			$_properties = $_me->getProperties();
		}

		//	We'll return this
		$_obj = new \stdClass();

		if ( !empty( $_properties ) )
		{
			if ( is_object( $object ) )
			{
				$_myClass = get_class( $object );
			}
			else
			{
				$_myClass = '_array_';
			}

			foreach ( $_properties as $_property )
			{
				//	Only want properties of $object hierarchy...
				if ( isset( $_property->class ) )
				{
					$_class = new \ReflectionClass( $_property->class );

					if ( !empty( $_class ) && !$_class->isInstance( $object ) && !$_class->isSubclassOf( $_myClass ) )
					{
						unset( $_class );
						continue;
					}

					unset( $_class );
				}

				try
				{
					$_realPropertyName = $_propertyName = ltrim( $_property->name, '_ ' );

					if ( false !== strpos( $_propertyName, '_' ) )
					{
						$_propertyName = Inflector::tag( $_propertyName );
					}

					$_getter = 'get' . $_propertyName;

					if ( method_exists( $object, $_getter ) )
					{
						$_propertyValue = $object->{$_getter}();

						if ( !is_scalar( $_propertyValue ) )
						{
							$_propertyValue = self::toObject( $_propertyValue );
						}

						$_obj->{$_realPropertyName} = $_propertyValue;
					}
				}
				catch ( \Exception $_ex )
				{
					//	Just ignore, not a valid property if we can't read it with a getter
				}
			}
		}

		return $_obj;
	}

	/**
	 * Takes parameters and returns an array of the values.
	 *
	 * @param string|array $data One or more values to read and put into the return array.
	 *
	 * @return array
	 */
	public static function createArray( $data )
	{
		$_result = array();
		$_count = func_num_args();

		for ( $_i = 0; $_i < $_count; $_i++ )
		{
			//	Any other columns to touch?
			if ( null !== ( $_arg = func_get_arg( $_i ) ) )
			{
				if ( !is_array( $_arg ) )
				{
					$_result[] = $_arg;
				}
				else
				{
					foreach ( $_arg as $_value )
					{
						$_result[] = $_value;
					}
				}
			}
		}

		//	Return the fresh array...
		return $_result;
	}

	/**
	 * Down and dirty object to array function
	 *
	 * @static
	 *
	 * @param object $object
	 *
	 * @return array
	 */
	public static function toArray( $object )
	{
		if ( is_object( $object ) )
		{
			return get_object_vars( $object );
		}

		if ( is_array( $object ) )
		{
			return array_map( array(
					__CLASS__,
					'toArray'
				),
				$object );
		}

		// Return array
		return $object;
	}

	/**
	 * Generic super-easy/lazy way to convert lots of different things (like SimpleXmlElement) to an array
	 *
	 * @param object $object
	 *
	 * @return array
	 */
	public static function toSimpleArray( $object )
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
			$_result[preg_replace( "/^\\0(.*)\\0/", "", $_key )] = self::toSimpleArray( $_value );
		}

		return $_result;
	}

	/**
	 * Takes a KVP traversable and converts to a ' key="value" ' string suitable for framing.
	 *
	 * @param array|object $array
	 * @param bool         $uppercaseKeys If TRUE, the "key" portion will be uppercased
	 * @param int          $trueConvert   The value to substitute for boolean true
	 * @param int          $falseConvert  The value to substitute for boolean false
	 *
	 * @return string
	 */
	public static function kvpToString( $array, $uppercaseKeys = false, $trueConvert = 1, $falseConvert = 0 )
	{
		$_result = array();

		foreach ( Option::clean( $array ) as $_key => $_value )
		{
			if ( null !== $_value )
			{
				if ( false === $_value )
				{
					$_value = $falseConvert;
				}
				else if ( true === $_value )
				{
					$_value = $trueConvert;
				}
				else if ( is_array( $_value ) )
				{
					$_value = trim( implode( ' ', $_value ) );
				}

				$_result[] = ( false !== $uppercaseKeys ? strtoupper( $_key ) : strtolower( $_key ) ) . '="' . $_value . '"';
			}
		}

		return trim( implode( ' ', $_result ) );
	}
}