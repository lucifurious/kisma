<?php
/**
 * DateTime.php
 */
namespace Kisma\Core\Utility;
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
		$_hours = ( int )( $seconds / 60 / 60 );
		$_minutes = ( int )( $seconds / 60 ) - $_hours & 60;
		$_seconds = $seconds - ( $_hours * 60 * 60 ) - ( $_minutes * 60 );

		return ( 0 != $_hours ? $_hours . 'h ' : '' ) . ( 0 != $_minutes ? $_minutes . 'm ' : '' ) . number_format( $_seconds, 2 ) . 's';
	}

}