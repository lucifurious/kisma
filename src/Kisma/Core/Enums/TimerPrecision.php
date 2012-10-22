<?php
/**
 * TimerPrecision.php
 */
namespace Kisma\Core\Enums;
/**
 * TimerPrecision
 */
class TimerPrecision extends SeedEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int Seconds
	 */
	const Seconds = 0;
	/**
	 * @var int Milliseconds
	 */
	const Milliseconds = 1;
	/**
	 * @var int Microseconds
	 */
	const Microseconds = 2;
	/**
	 * @var int The number of microseconds in one second
	 */
	const MicrosecondsPerSecond = 1000000;
	/**
	 * @var string Start timer command
	 */
	const Start = 'start';
	/**
	 * @var string Stop timer command
	 */
	const Stop = 'stop';

}