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
namespace Kisma\Core\Services\Network;

use Kisma\Core\Interfaces;
use Kisma\Core\Services\SeedService;

/**
 * Http
 * An HTTP service base class
 *
 * Provides one event handler:
 *
 * onRequestReceived
 * Happens when the service is run, respectively.
 */
abstract class Http extends SeedService implements Interfaces\HttpMethod, Interfaces\Events\Http
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function initialize( $consumer, $request = null )
	{
		if ( !parent::initialize( $consumer, $request ) )
		{
			return false;
		}

		if ( null === $this->_request )
		{
			$this->_request = new HttpRequest();
		}

		//	Trigger the event
		return $this->publish(
			self::RequestReceived,
			$this->_request
		);
	}
}
