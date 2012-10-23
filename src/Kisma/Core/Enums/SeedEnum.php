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
	 * @param bool   $flipped If true, the array is flipped before return
	 * @param string $class   Used internally to cache constants
	 *
	 * @return array
	 */
	public static function getDefinedConstants( $flipped = false, $class = null )
	{
		static $_constants = array();

		$class = $class ? : get_called_class();

		if ( !isset( $_constants[$class] ) )
		{
			$_mirror = new \ReflectionClass( $class );
			$_constants[$class] = $_mirror->getConstants();
		}

		return false === $flipped ? $_constants[$class] : array_flip( $_constants[$class] );
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
		$_constants = self::getDefinedConstants( true /*, get_called_class()*/ );

		if ( !\Kisma\Core\Utility\Option::contains( $_constants, $constant ) )
		{
			throw new \InvalidArgumentException( 'The constant "' . $constant . '" is not defined.' );
		}

		return \Kisma\Core\Utility\Option::get( $_constants, $constant );
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
		$_constants = self::getDefinedConstants( true /*, get_called_class()*/ );

		if ( false === ( $_has = Utility\Option::contains( $_constants, $constant ) ) && false !== $returnValue )
		{
			throw new \InvalidArgumentException( 'The constant "' . $constant . '" is not defined.' );
		}

		return
			false === $returnValue ? $_has : $_constants[$constant];
	}
}
