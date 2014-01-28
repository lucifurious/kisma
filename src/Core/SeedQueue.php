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
namespace Kisma\Core;

/**
 * SeedQueue
 * An eerily simple FIFO queue
 */
class SeedQueue extends Seed
{
	//**************************************************************************
	//* Private Members
	//**************************************************************************

	/**
	 * @var array The keeper of the cheese
	 */
	protected $_queue = array();

	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * Pushes the thing on to the end of the queue
	 *
	 * @param mixed $queueItem
	 *
	 * @return int The NEW number of requests in the queue
	 */
	public function push( $queueItem )
	{
		return array_push( $this->_queue, $queueItem );
	}

	/**
	 * Pop the thing off of the end of the queue
	 *
	 * @return mixed Returns null if the queue is empty
	 */
	public function pop()
	{
		return array_pop( $this->_queue );
	}
}
