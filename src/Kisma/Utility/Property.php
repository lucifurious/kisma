<?php
/**
 * @file
 * Provides property manipulation utilities
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Utilities
 * @package kisma.utility
 * @since 1.0.0
 *
 * @ingroup utilities
 */

namespace Kisma\Utility;

/**
 * Property
 * Provides property manipulation routines
 */
class Property implements \Kisma\IUtility
{
	//*************************************************************************
	//* Public Methods 
	//*************************************************************************

	/**
	 * Generic super-easy/lazy way to convert lots of different things (like SimpleXmlElement) to an array
	 *
	 * @param object $object
	 *
	 * @return array|false
	 */
	public static function toArray( $object )
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
			$_result[preg_replace( "/^\\0(.*)\\0/", "", $_key )] = self::toArray( $_value );
		}

		return $_result;
	}

	/**
	 * @static
	 *
	 * @param \Kisma\Components\SubComponent $object
	 * @param string						 $propertyName
	 * @param \Kisma\AccessorMode|int		$access
	 * @param mixed|null					 $valueOrDefault The "Set" value or default value for "Get"
	 *
	 * @return mixed
	 */
	public static function checkProperty( &$object, $propertyName, $access = AccessorMode::Get, $valueOrDefault = null )
	{
		try
		{
			//	Try object first. Squelch undefineds...
			return self::property( $object, $propertyName, $access, $valueOrDefault );
		}
			//	Ignore undefined properties. Another aspect may have it
		catch ( \Kisma\UndefinedPropertyException $_ex )
		{
			//	Ignored
		}
		catch ( PropertyException $_ex )
		{
			//	Rethrow other property exceptions
			throw $_ex;
		}

		//	No clue what they're talking about
		throw new \Kisma\UndefinedPropertyException( 'The property "' . $propertyName . '" is undefined.', \Kisma\AccessorMode::Undefined );
	}

	/**
	 * An all-purpose property accessor.
	 *
	 * @static
	 *
	 * @param Components\SubComponent|array|object $object
	 * @param string                               $propertyName
	 * @param int                                  $access
	 * @param mixed|null                           $valueOrDefault
	 *
	 * @return bool
	 */
	public static function property( &$object, $propertyName, $access = \Kisma\AccessorMode::Get, $valueOrDefault = null )
	{
		$_propertyName = $propertyName;
		$_getter = 'get' . $_propertyName;
		$_setter = 'set' . $_propertyName;

		switch ( $access )
		{
			case \Kisma\AccessorMode::Has:
				//	Is it accessible
				if ( method_exists( $object, $_getter ) || method_exists( $object, $_setter ) )
				{
					return true;
				}
				break;

			case \Kisma\AccessorMode::Get:
				//	Does a setter exist?
				if ( method_exists( $object, $_getter ) )
				{
					return $object->{$_getter}();
				}

				//	Is it write only?
				if ( method_exists( $object, $_setter ) )
				{
					self::propertyError( $_propertyName, \Kisma\AccessorMode::WriteOnly );
				}
				break;

			case \Kisma\AccessorMode::Set:
				//	Does a setter exist?
				if ( method_exists( $object, $_setter ) )
				{
					return $object->{$_setter}( $valueOrDefault );
				}

				//	Is it read only?
				if ( !method_exists( $object, $_setter ) && method_exists( $object, $_getter ) )
				{
					self::propertyError( $_propertyName, \Kisma\AccessorMode::ReadOnly );
				}
				break;
		}

		//	Everything falls through to undefined
		self::propertyError( $propertyName, \Kisma\AccessorMode::Undefined );
	}

	/**
	 * A generic property error handler to go with our generic property system.
	 *
	 * @param string $name
	 * @param int	$type
	 *
	 * @throws UndefinedPropertyException|ReadOnlyPropertyException|WriteOnlyPropertyException
	 */
	public static function propertyError( $name, $type = \Kisma\AccessorMode::Undefined )
	{
		switch ( $type )
		{
			case \Kisma\AccessorMode::ReadOnly:
				$_class = '\Kisma\ReadOnlyPropertyException';
				$_reason = 'read-only';
				break;

			case \Kisma\AccessorMode::WriteOnly:
				$_class = '\Kisma\WriteOnlyPropertyException';
				$_reason = 'write-only';
				break;

			default:
				$_class = '\Kisma\UndefinedPropertyException';
				$_reason = 'undefined';
				break;
		}

		throw new $_class( 'Property "' . get_called_class() . '"."' . $name . '" is ' . $_reason . '.', $type );
	}

}
