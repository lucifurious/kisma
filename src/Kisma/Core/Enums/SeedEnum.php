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

/**
 * SeedEnum
 * This is the non-SplEnum version
 */
abstract class SeedEnum
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Returns the default value for this enum if called as a function: $_x = SeedEnum()
	 */
	public function __invoke()
	{
		return self::defines( '__default', true );
	}

	/**
	 * Returns a hash of the called class's constants. Caches for speed
	 * (class cache hash, say that ten times fast!).
	 *
	 * @static
	 * @return array
	 */
	public static function getDefinedConstants()
	{
		static $_constants = null;

		if ( null === $_constants )
		{
			$_mirror = new \ReflectionClass( get_called_class() );
			$_constants = $_mirror->getConstants();
		}

		return $_constants;
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
		return
			\Kisma\Core\Utility\Option::get(
				self::getDefinedConstants(),
				$constant,
				function () use ( $constant )
				{
					throw new \InvalidArgumentException( 'The constant "' . $constant . '" is not defined.' );
				}
			);
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
		$_constants = self::getDefinedConstants();
		$_has = in_array( $constant, array_keys( $_constants ) );

		if ( false !== $returnValue && false === $_has )
		{
			throw new \InvalidArgumentException( 'The constant "' . $constant . '" is not defined.' );
		}

		return
			false === $returnValue ? $_has : $_constants[$constant];
	}
}
