<?php
namespace Kisma\Core\Interfaces;

/**
 * EscapeStyle.php
 * Defines the various ways characters can be escaped
 */
interface EscapeStyle
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int No escape (AARRGH!)
	 */
	const NONE = 0;
	/**
	 * @var int Escaped with a double character (i.e. '')
	 */
	const DOUBLED = 1;
	/**
	 * @var int Escaped with a backslash (i.e. \')
	 */
	const SLASHED = 2;
}
