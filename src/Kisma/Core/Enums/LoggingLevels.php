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
 * LoggingLevels
 * A general purpose log level enum
 */
class LoggingLevels extends SeedEnum
{
	//*************************************************************************
	//	Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const EMERGENCY = 600;
	/**
	 * @var int
	 */
	const ALERT = 550;
	/**
	 * @var int
	 */
	const CRITICAL = 500;
	/**
	 * @var int
	 */
	const ERROR = 400;
	/**
	 * @var int
	 */
	const WARNING = 300;
	/**
	 * @var int
	 */
	const NOTICE = 250;
	/**
	 * @var int
	 */
	const INFO = 200;
	/**
	 * @var int
	 */
	const DEBUG = 100;
	/**
	 * @var int Trace information gets routed to debug
	 */
	const TRACE = self::DEBUG;
	/**
	 * @var int Profile information gets routed to debug
	 */
	const PROFILE = self::DEBUG;

	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var array A hash of level names against Monolog levels
	 */
	protected static $_strings = array(
		'debug'     => self::DEBUG,
		'trace'     => self::DEBUG,
		'profile'   => self::DEBUG,
		'info'      => self::INFO,
		'warning'   => self::WARNING,
		'notice'    => self::NOTICE,
		'error'     => self::ERROR,
		'critical'  => self::CRITICAL,
		'alert'     => self::ALERT,
		'emergency' => self::EMERGENCY,
	);

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param string $stringLevel
	 *
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public static function toNumeric( $stringLevel )
	{
		if ( !is_string( $_level = strtolower( $stringLevel ) ) )
		{
			throw new \InvalidArgumentException( 'The level "' . $stringLevel . '" is not a string.' );
		}

		if ( !in_array( $_level, array_keys( static::$_strings ) ) )
		{
			throw new \InvalidArgumentException( 'The level "' . $stringLevel . '" is undefined.' );
		}

		return static::$_strings[$_level];
	}

	/**
	 * @param int $numericLevel
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public static function toString( $numericLevel )
	{
		if ( !is_numeric( $numericLevel ) )
		{
			throw new \InvalidArgumentException( 'The level "' . $numericLevel . '" is not numeric.' );
		}

		if ( !in_array( $numericLevel, array_flip( static::$_strings ) ) )
		{
			throw new \InvalidArgumentException( 'The level "' . $numericLevel . '" is undefined.' );
		}

		return static::nameOf( $numericLevel );
	}
}
