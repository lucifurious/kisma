<?php
/**
 * SeedUtility.php
 */
namespace Kisma\Core;
/**
 * SeedUtility
 * The base class for utilities
 */
class SeedUtility implements \Kisma\Core\Interfaces\SeedUtility
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var bool If true, events will be logged
	 */
	protected static $_debug = true;

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param boolean $debug
	 */
	public static function setDebug( $debug )
	{
		self::$_debug = $debug;
	}

	/**
	 * @return boolean
	 */
	public static function getDebug()
	{
		return self::$_debug;
	}
}
