<?php
/**
 * Storage.php
 */
namespace Kisma\Core\Utility;
/**
 * Storage class
 * Provides storage junk
 */
class Storage
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Object freezer that can handle SimpleXmlElement objects
	 *
	 * @param mixed $object
	 *
	 * @return string
	 */
	public static function freeze( $object )
	{
		try
		{
			if ( $object instanceof \SimpleXMLElement )
			{
				/** @var $object \SimpleXMLElement */
				return $object->asXML();
			}

			if ( is_array( $object ) || is_object( $object ) || $object instanceOf \Serializable )
			{
				return base64_encode( gzcompress( serialize( $object ) ) );
			}
		}
		catch ( \Exception $_ex )
		{
			//	Ignored
		}

		//	Returns the $object if it cannot be frozen
		return $object;
	}

	/**
	 * Object defroster. Warms up those chilly objects.
	 *
	 * @param string $frozenObject
	 *
	 * @return object
	 */
	public static function defrost( $frozenObject )
	{
		try
		{
			//	If the object isn't encoded/compressed, just take it as-is
			if ( false === ( $_object = @gzuncompress( @base64_decode( $frozenObject ) ) ) )
			{
				$_object = $frozenObject;
			}

			//	Is it frozen?
			if ( static::isFrozen( $_object ) )
			{
				return unserialize( $_object );
			}

			//	See if it's XML
			if ( false !== ( $_xml = @simplexml_load_string( $_object ) ) )
			{
				return $_xml;
			}
		}
		catch ( \Exception $_ex )
		{
			//	Nada
		}

		return $frozenObject;
	}

	/**
	 * Tests if a value is frozen
	 *
	 * @param mixed $value
	 *
	 * @return boolean
	 */
	public static function isFrozen( $value )
	{
		$_result = @unserialize( $value );

		return !( false === $_result && $value != serialize( false ) );
	}
}
