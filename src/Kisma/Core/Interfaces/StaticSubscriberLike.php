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
 * Something that subscribes to events
 */
interface StaticSubscriberLike
{
	/**
	 * Adds an event listener that listens on the specified events.
	 *
	 * @param string   $eventName            The event to listen on
	 * @param callable $listener             The listener
	 * @param integer  $priority             The higher this value, the earlier an event
	 *                                       listener will be triggered in the chain (defaults to 0)
	 */
	public static function on( $eventName, $listener, $priority = 0 );

	/**
	 * Turn off/unbind/remove $listener from an event
	 *
	 * @param string   $eventName
	 * @param callable $listener
	 *
	 * @return void
	 */
	public static function off( $eventName, $listener );
}
