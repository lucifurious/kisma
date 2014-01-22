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
namespace Kisma\Core\Interfaces;

/**
 * RequestSource
 * The source of an inbound request
 */
interface RequestSource
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int Via HTTP
	 */
	const Http = 0;
	/**
	 * @var int Via command line
	 */
	const Cli = 1;
	/**
	 * @var int Via internal procedure
	 */
	const Internal = 2;
	/**
	 * @var int HTTP request via internal procedure
	 */
	const InternalHttp = 2;
	/**
	 * @var int CLI request via internal procedure
	 */
	const InternalCli = 3;
}
