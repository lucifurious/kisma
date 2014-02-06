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
namespace Kisma\Core\Enums;

use Kisma\Core\Utility\Inflector;

/**
 * SeedEnum
 * This is the non-SplEnum version
 */
abstract class SeedEnum
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var array The cache for quick lookups
	 */
	protected static $_constants = null;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Returns the default value for this enum if called as a function: $_x = SeedEnum()
	 */
	public function __invoke()
	{
		return static::defines( '__default', true );
	}

	/**
	 * @param string $class
	 * @param array  $seedConstants Seeds the cache with these optional KVPs
	 * @param bool   $overwrite
	 *
	 * @return string
	 */
	public static function introspect( $class = null, array $seedConstants = array(), $overwrite = true )
	{
		$_key = static::_cacheKey( $class );

		if ( true === $overwrite || !isset( static::$_constants[$_key] ) )
		{
			$_mirror = new \ReflectionClass( $class ? : \get_called_class() );

			static::$_constants[$_key] = array_merge(
				$seedConstants,
				$_mirror->getConstants()
			);

			unset( $_mirror );
		}

		return $_key;
	}

	/**
	 * Gets a guaranteed cache key
	 *
	 * @param string $class
	 *
	 * @return string
	 */
	protected static function _cacheKey( $class = null )
	{
		static $_key = null;

		return $_key ? : Inflector::tag( $class ? : \get_called_class(), true );
	}

	/**
	 * Adds constants to the cache for a particular class. Roll-your-own ENUM
	 *
	 * @param array  $constants
	 * @param string $class
	 *
	 * @return void
	 */
	public static function seedConstants( array $constants, $class = null )
	{
		static::introspect( $class, $constants );
	}

	/**
	 * Returns a hash of the called class's constants ( CONSTANT_NAME => value ). Caches for speed
	 * (class cache hash, say that ten times fast!).
	 *
	 * @param bool   $flipped  If true, the array is flipped before return ( value => CONSTANT_NAME )
	 * @param string $class    Used internally to cache constants
	 * @param bool   $listData If true, the constant names themselves are cleaned up for display purposes.
	 *
	 * @return array
	 */
	public static function getDefinedConstants( $flipped = false, $class = null, $listData = false )
	{
		$_key = static::introspect( $class, array(), false );

		$_constants = false === $flipped ? static::$_constants[$_key] : array_flip( static::$_constants[$_key] );

		if ( false === $listData )
		{
			return $_constants;
		}

		$_temp = array();

		foreach ( static::$_constants[$_key] as $_constant => $_value )
		{
			$_temp[$_value] = Inflector::display( Inflector::neutralize( $_constant ) );
			unset( $_value, $_option );
		}

		return $_temp;
	}

	/**
	 * Returns true or false if this class contains a specific constant value.
	 *
	 * Use for validity checks:
	 *
	 *    if ( false === VeryCoolShit::contains( $evenCoolerShit ) ) {
	 *        throw new \InvalidArgumentException( 'Sorry, your selection of "' . $evenCoolerShit . '" is invalid.' );
	 *    }
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public static function contains( $value )
	{
		return in_array( $value, array_values( static::getDefinedConstants() ) );
	}

	/**
	 * Returns true or false if this class defines a specific constant.
	 * Optionally returns the value of the constant, but throws an
	 * exception if not found.
	 *
	 * Use for validity checks:
	 *
	 *    if ( false === VeryCoolShit::contains( $evenCoolerShit ) ) {
	 *        throw new \InvalidArgumentException( 'Sorry, your selection of "' . $evenCoolerShit . '" is invalid.' );
	 *    }
	 *
	 * @param string $constant
	 * @param bool   $returnValue If true, returns the value of the constant if found, but throws an exception if not
	 *
	 * @throws \InvalidArgumentException
	 * @return bool
	 */
	public static function defines( $constant, $returnValue = false )
	{
		$_constants = static::getDefinedConstants();
		$_has = isset( $_constants[$constant] );

		if ( false === $_has && false !== $returnValue )
		{
			throw new \InvalidArgumentException( 'The constant "' . $constant . '" is not defined.' );
		}

		return false === $returnValue ? $_has : $_constants[$constant];
	}

	/**
	 * Returns the constant name as a string
	 *
	 * @param string $constant
	 * @param bool   $flipped If false, the $constantValue should contain the constant name and the value will be returned
	 * @param bool   $pretty  If true, returned value is prettified (acme.before_event becomes "Acme Before Event")
	 *
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	public static function nameOf( $constant, $flipped = true, $pretty = true )
	{
		if ( in_array( $constant, array_keys( $_constants = static::getDefinedConstants( $flipped ) ) ) )
		{
			return $pretty ? Inflector::display( Inflector::neutralize( $_constants[$constant] ) ) : $_constants[$constant];
		}

		throw new \InvalidArgumentException( 'A constant with the value of "' . $constant . '" does not exist.' );
	}

	/**
	 * @param mixed $constant
	 * @param bool  $flipped
	 *
	 * @return string
	 */
	public static function prettyNameOf( $constant, $flipped = true )
	{
		return static::nameOf( $constant, $flipped, true );
	}
}
