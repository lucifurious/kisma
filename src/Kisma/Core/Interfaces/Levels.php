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
 * Levels
 * Individual log entry levels
 *
 * @deprecated in 0.2.27, to be removed in 0.3.0. {@see Kisma\Core\Enums\LogLevels}
 */
interface Levels
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const Emergency = 600;
	const EMERGENCY = 600;
	/**
	 * @var int
	 */
	const Alert = 550;
	const ALERT = 550;
	/**
	 * @var int
	 */
	const Critical = 500;
	const CRITICAL = 500;
	/**
	 * @var int
	 */
	const Error = 400;
	const ERROR = 400;
	/**
	 * @var int
	 */
	const Warning = 300;
	const WARNING = 300;
	/**
	 * @var int
	 */
	const Notice = 250;
	const NOTICE = 250;
	/**
	 * @var int
	 */
	const Info = 200;
	const INFO = 200;
	/**
	 * @var int
	 */
	const Debug = 100;
	const DEBUG = 100;
}
