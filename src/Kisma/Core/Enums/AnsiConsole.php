<?php
/**
 * @file
 * Standard ANSI console attributes
 */
namespace Kisma\Core\Enums;

/**
 * Standard ANSI console attributes
 */
class AnsiConsole extends SeedEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const Reset = 0;
	/**
	 * @var int
	 */
	const Bright = 1;
	/**
	 * @var int
	 */
	const Dim = 2;
	/**
	 * @var int
	 */
	const Underscore = 4;
	/**
	 * @var int
	 */
	const Blink = 5;
	/**
	 * @var int
	 */
	const Reverse = 7;
	/**
	 * @var int
	 */
	const Hidden = 8;
	/**
	 * @var string
	 */
	const Escape = "\033[";

}