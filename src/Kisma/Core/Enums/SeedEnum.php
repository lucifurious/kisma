<?php
/**
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/lucifurious/kisma/licensing/} for complete information.
 *
 * @copyright          Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 * @link               http://github.com/lucifurious/kisma/ Kisma(tm)
 * @license            http://github.com/lucifurious/kisma/licensing/
 * @author             Jerry Ablan <kisma@pogostick.com>
 * @filesource
 */
namespace Kisma\Core\Enums;
use \Kisma\Core\Utility;

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
		return self::defines( '__default', true );
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

		return $_key ? : \Kisma\Core\Utility\Inflector::tag( $class ? : \get_called_class(), true );
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
	 * Returns a hash of the called class's constants. Caches for speed
	 * (class cache hash, say that ten times fast!).
	 *
	 * @param bool   $flipped If true, the array is flipped before return
	 * @param string $class   Used internally to cache constants
	 *
	 * @return array
	 */
	public static function getDefinedConstants( $flipped = false, $class = null )
	{
		$_key = static::introspect( $class, array(), false );

		return false === $flipped ? static::$_constants[$_key] : array_flip( static::$_constants[$_key] );
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
		return in_array( $value, array_values( self::getDefinedConstants() ) );
	}

	/**
	 * Returns the constant name as a string
	 *
	 * @param string $constant
	 *
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	public static function nameOf( $constant )
	{
		if ( in_array( $constant, ( $_constants = self::getDefinedConstants() ) ) )
		{
			return $_constants[$constant];
		}

		throw new \InvalidArgumentException( 'The constant "' . $constant . '" is not defined.' );
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
		$_constants = self::getDefinedConstants( true );
		$_has = isset( $_constants, $constant );

		if ( false === $_has && false !== $returnValue )
		{
			throw new \InvalidArgumentException( 'The constant "' . $constant . '" is not defined.' );
		}

		return
			false === $returnValue ? $_has : $_constants[$constant];
	}
}
