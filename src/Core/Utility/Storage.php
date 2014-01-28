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
 * Storage class
 * Provides storage junk
 */
class Storage
{
	//*************************************************************************
	//* Methods
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
