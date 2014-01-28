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
namespace Kisma\Core\Services;

use Kisma\Core\Interfaces\ResponseLike;
use Kisma\Core\SeedBag;

/**
 * SeedResponse
 * A response for a service request
 */
class SeedResponse extends SeedBag implements ResponseLike
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var int The service status
	 */
	protected $_status = null;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Returns true if the service call was successful
	 *
	 * @return bool
	 */
	public function success()
	{
		return $this->_status == static::Success;
	}
}
