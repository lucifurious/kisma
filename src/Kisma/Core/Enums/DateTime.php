<?php
/**
 * DateTime.php
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license   http://github.com/Pogostick/Kisma/blob/master/LICENSE
 * @author    Jerry Ablan <kisma@pogostick.com>
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
