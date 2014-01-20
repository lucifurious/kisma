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
 */
class DebugLevel extends SeedEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int Default value
	 */
	const __default = self::Normal;
	/**
	 * @var int
	 */
	const Normal = 0;
	/**
	 * @var int
	 */
	const Verbose = 1;
	/**
	 * @var int
	 */
	const VeryChatty = 2;
	/**
	 * @var int
	 */
	const WillNotShutUp = 3;
	/**
	 * @var int
	 */
	const Nutty = 4;
	/**
	 * @var int
	 */
	const Satanic = 666;
}
