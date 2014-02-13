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

use Kisma\Core\Events\SeedEvent;

/**
 * Something that looks like a publisher
 */
interface PublisherLike
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The default event manager for an object
	 * @deprecated Deprecated in  v0.2.19, to be removed in v0.3.0
	 * @see        PublisherLike::DEFAULT_EVENT_MANAGER
	 */
	const DefaultEventManager = 'Kisma\\Core\\Utility\\EventManager';
	/**
	 * @var string The default event manager for the publisher
	 */
	const DEFAULT_EVENT_MANAGER = 'Kisma\\Core\\Utility\\EventManager';

	//*************************************************************************
	//	Methods
	//*************************************************************************

	/**
	 * @param string    $eventName
	 * @param SeedEvent $event
	 *
	 * @return SeedEvent
	 */
	public function trigger( $eventName, $event = null );
}
