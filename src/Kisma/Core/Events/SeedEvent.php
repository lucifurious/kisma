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
namespace Kisma\Core\Events;

use Kisma\Core\Interfaces\PublisherLike;
use Symfony\Component\EventDispatcher\Event;

/**
 * SeedEvent
 * The base class for Kisma events
 *
 * It encapsulates the parameters associated with an event.
 * The {@link source} property describes who raised the event.
 *
 * If an event handler calls the kill() method, propagation will halt.
 */
class SeedEvent extends Event
{
	//**************************************************************************
	//* Members
	//**************************************************************************

	/**
	 * @var PublisherLike The source of this event
	 */
	protected $_source;
	/**
	 * @var boolean Set to true to stop the bubbling of events at any point
	 */
	protected $_kill = false;
	/**
	 * @var mixed Any event data the sender wants to convey
	 */
	protected $_data;
	/**
	 * @var string
	 */
	protected $_eventTag = null;
	/**
	 * @var string A user-defined event ID
	 */
	protected $_eventId = null;

	//**************************************************************************
	//* Methods
	//**************************************************************************

	/**
	 * Constructor.
	 *
	 * @param PublisherLike $source
	 * @param mixed         $data
	 */
	public function __construct( $source = null, $data = null )
	{
		$this->_source = $source;
		$this->_data = $data;
		$this->_kill = false;
	}

	/**
	 * Kills propagation immediately
	 *
	 * @return SeedEvent
	 */
	public function kill()
	{
		$this->stopPropagation();

		return $this;
	}

	/**
	 * @return bool
	 */
	public function wasKilled()
	{
		return $this->isPropagationStopped();
	}

	/**
	 * @param mixed $data
	 *
	 * @return SeedEvent
	 */
	public function setData( $data )
	{
		$this->_data = $data;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * @return \Kisma\Core\Interfaces\SeedLike
	 */
	public function getSource()
	{
		return $this->_source;
	}

	/**
	 * @param string $eventId
	 *
	 * @return SeedEvent
	 */
	public function setEventId( $eventId )
	{
		$this->_eventId = $eventId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEventId()
	{
		return $this->_eventId;
	}

	/**
	 * @param string $eventTag
	 *
	 * @return SeedEvent
	 */
	public function setEventTag( $eventTag )
	{
		$this->_eventTag = $eventTag;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEventTag()
	{
		return $this->_eventTag;
	}
}
