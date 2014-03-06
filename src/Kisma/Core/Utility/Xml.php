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

use Kisma\Core\SeedUtility;

/**
 * Xml
 * XML utilities
 */
class Xml extends SeedUtility
{
	/**
	 * Converts an object to an XML string
	 *
	 * @param mixed  $object
	 * @param string $rootName
	 * @param string $nodeName
	 * @param bool   $addHeader If true, the <?xml?> header is prepended to the result
	 *
	 * @throws \InvalidArgumentException
	 * @return null|string
	 */
	public static function fromObject( $object, $rootName = null, $nodeName = null, $addHeader = true )
	{
		if ( !is_object( $object ) )
		{
			throw new \InvalidArgumentException( 'The value of "$object" is not an object.' );
		}

		return static::fromArray( get_object_vars( $object ), $rootName, $nodeName, $addHeader );
	}

	/**
	 * Converts an array to an XML string
	 *
	 * @param mixed  $data
	 * @param string $rootName
	 * @param string $nodeName
	 * @param bool   $addHeader
	 *
	 * @return null|string
	 */
	public static function fromArray( $data, $rootName = null, $nodeName = null, $addHeader = true )
	{
		$_xml = true === $addHeader ? '<?xml version="1.0" encoding="UTF-8" ?>' : null;

		if ( null !== $rootName )
		{
			$_xml .= Markup::openTag( $rootName );
		}

		$_string = null;

		if ( $data instanceof \Traversable )
		{
			foreach ( $data as $_key => $_value )
			{
				if ( is_numeric( $_key ) )
				{
					$_key = $nodeName ? : 'node';
				}

				$_string .= Markup::tag( $_key, array(), static::fromArray( $_value, $nodeName ) );
			}
		}
		else
		{
			$_string = htmlspecialchars( $data, ENT_QUOTES );
		}

		//	Add the converted XML
		$_xml .= $_string;

		if ( null !== $rootName )
		{
			$_xml .= Markup::closeTag( $rootName );
		}

		return $_xml;
	}
}