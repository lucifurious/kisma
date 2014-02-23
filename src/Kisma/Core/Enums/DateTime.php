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

/**
 * DateTime
 * Various date and time constants
 */
class DateTime extends SeedEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const __default = self::SecondsPerMinute;

	/**
	 * @var int Microseconds per hour
	 */
	const US_PER_HOUR = 3600000;
	/**
	 * @var int Microseconds per minute
	 */
	const US_PER_MINUTE = 60000;
	/**
	 * @var int Microseconds per second
	 */
	const US_PER_SECOND = 1000;
	/**
	 * @var int circa 01/01/1980 (Ahh... my TRS-80... good times)
	 */
	const TheBeginning = 315550800;
	/**
	 * @var int
	 */
	const MicroSecondsPerSecond = 1000000;
	/**
	 * @var int
	 */
	const MilliSecondsPerSecond = 1000;
	/**
	 * @var int
	 */
	const SecondsPerMinute = 60;
	/**
	 * @var int
	 */
	const SecondsPerHour = 3600;
	/**
	 * @var int
	 */
	const SecondsPerEighthDay = 10800;
	/**
	 * @var int
	 */
	const SecondsPerQuarterDay = 21600;
	/**
	 * @var int
	 */
	const SecondsPerHalfDate = 43200;
	/**
	 * @var int
	 */
	const SecondsPerDay = 86400;
	/**
	 * @var int
	 */
	const SecondsPerWeek = 604800;
	/**
	 * @var int circa 01/01/2038 (despite the Mayan calendar or John Titor...)
	 */
	const TheEnd = 2145934800;
}
