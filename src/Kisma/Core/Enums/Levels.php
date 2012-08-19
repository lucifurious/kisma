<?php
/**
 * Levels.php
 */
namespace Kisma\Core\Enums;
/**
 * Levels
 */
class Levels extends \Kisma\Core\Enums\SeedEnum implements \Kisma\Core\Interfaces\Levels
{
	//*************************************************************************
	//* Class Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const __default = self::Info;

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var array
	 */
	protected static $_indicators = array(
		self::Emergency => 'X',
		self::Alert     => 'A',
		self::Critical  => 'C',
		self::Error     => 'E',
		self::Warning   => 'W',
		self::Notice    => 'N',
		self::Info      => 'I',
		self::Debug     => 'D',
	);

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Returns the level indicator for the log
	 *
	 * @param int $level The level value
	 *
	 * @return string
	 */
	public static function getIndicator( $level )
	{
		return \Kisma\Core\Utility\Option::get( self::$_indicators, $level, self::__default );
	}
}
