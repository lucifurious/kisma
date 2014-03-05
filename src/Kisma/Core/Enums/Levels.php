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
 * Levels
 *
 * @deprecated in 0.2.27, to be removed in 0.3.0. {@see Kism\Core\Enums\LogLevels}
 */
abstract class Levels extends SeedEnum implements \Kisma\Core\Interfaces\Levels
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const __default = self::Info;

	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var array
	 */
	protected static $_indicators = array(
		self::Emergency => 'X',
		self::Alert     => 'A',
		self::Critical  => 'C',
		self::Error     => 'E',
		self::Warning   => 'W',
		self::Notice    => 'N',
		self::Info      => 'I',
		self::Debug     => 'D',
	);

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Returns the level indicator for the log
	 *
	 * @param int $level The level value
	 *
	 * @return string
	 */
	public static function getIndicator( $level )
	{
		return \Kisma\Core\Utility\Option::get( self::$_indicators, $level, self::__default );
	}
}
