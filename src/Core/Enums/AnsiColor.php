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