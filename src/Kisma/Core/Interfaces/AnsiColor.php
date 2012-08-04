<?php
/**
 * @file
 * Standard ANSI color attributes
 */
namespace Kisma\Core\Interfaces;

/**
 * Standard ANSI color attributes
 */
interface AnsiColor
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int Add this to a color to make it the background color (i.e. self::Red + self::Background)
	 */
	const Background = 10;
	/**
	 * @var int
	 */
	const Black = 30;
	/**
	 * @var int
	 */
	const Red = 31;
	/**
	 * @var int
	 */
	const Green = 32;
	/**
	 * @var int
	 */
	const Yellow = 33;
	/**
	 * @var int
	 */
	const Blue = 34;
	/**
	 * @var int
	 */
	const Magenta = 35;
	/**
	 * @var int
	 */
	const Cyan = 36;
	/**
	 * @var int
	 */
	const White = 37;

}