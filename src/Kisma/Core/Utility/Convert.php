<?php
/**
 * Convert.php
 */
namespace Kisma\Core\Utility;
/**
 * Convert
 */
class Convert extends \Kisma\Core\Seed implements \Kisma\Core\Interfaces\UtilityLike
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
					$_propertyName = ltrim( $_property->name, '_ ' );
					$_getter = 'get' . $_propertyName;

					if ( method_exists( $object, $_getter ) )
					{
						$_propertyValue = $object->{$_getter}();

						if ( !is_scalar( $_propertyValue ) )
						{
							$_propertyValue = self::toObject( $_propertyValue );
						}

						$_obj->{$_propertyName} = $_propertyValue;
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
			return array_map(
				array(
					__CLASS__,
					'toArray'
				),
				$object
			);
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

}
