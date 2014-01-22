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

use Kisma\Core\Enums;

/**
 * DateTime
 * Provides methods to manipulate array and object properties
 */
class DateTime
{
	//*************************************************************************
	//* Methods
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