<?php
/**
 * DateTime.php
 */
namespace Kisma\Core\Utility;
use \Kisma\Core\Enums;

/**
 * DateTime
 * Provides methods to manipulate array and object properties
 */
class DateTime
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Coverts a number of seconds into a pretty string (i.e. 0d 0h 0m 0.00s
	 *
	 * @param float $seconds
	 *
	 * @return string
	 */
	public static function prettySeconds( $seconds = 0.0 )
	{
		$_remain = $seconds;
		$_hours = floor( $_remain / Enums\DateTime::SecondsPerHour );
		$_remain -= $_hours * Enums\DateTime::SecondsPerHour;

		$_minutes = floor( $_remain / Enums\DateTime::SecondsPerMinute );
		$_remain -= $_minutes * Enums\DateTime::SecondsPerMinute;

		return $_hours . 'h ' . $_minutes . 'm ' . number_format( $_remain, 2 ) . 's';
	}

}