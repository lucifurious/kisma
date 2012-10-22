<?php
/**
 * @file
 *            Standard ANSI color attributes
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2012 Jerry Ablan
 * @license   http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author    Jerry Ablan <get.kisma@gmail.com>
 */
namespace Kisma\Core\Enums;

/**
 * Standard ANSI color attributes
 */
class AnsiColor extends SeedEnum
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