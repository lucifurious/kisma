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
 * GlobFlags
 * Ya know, for globbing...
 */
class GlobFlags extends SeedEnum implements \Kisma\Core\Interfaces\GlobFlags
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const GLOB_NODIR = 0x0100;
	/**
	 * @var int
	 */
	const GLOB_PATH = 0x0200;
	/**
	 * @var int
	 */
	const GLOB_NODOTS = 0x0400;
	/**
	 * @var int
	 */
	const GLOB_RECURSE = 0x0800;
	/**
	 * @var int
	 */
	const NoDir = 0x0100;
	/**
	 * @var int
	 */
	const Path = 0x0200;
	/**
	 * @var int
	 */
	const NoDots = 0x0400;
	/**
	 * @var int
	 */
	const Recurse = 0x0800;
}