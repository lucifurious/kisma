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

use Kisma\Core\Utility\Inflector;
use Kisma\Core\Utility\Option;
use Symfony\Component\EventDispatcher\Event;

/**
 * SeedEvent
 * The base class for Kisma events
 *
 * It encapsulates the parameters associated with an event.
 * The {@link source} property describes who raised the event.
 *
 * If an event handler calls the stopPropagation() method, propagation will halt.
 */
class SeedEvent extends Event
{
	//**************************************************************************
	//* Members
	//**************************************************************************

	/**
	 * @var boolean Set to true to stop the bubbling of events at any point
	 * @deprecated in 0.2.31, to be removed in 0.3.0 {@see Event::stopPropagation}
	 */
	protected $_kill = false;
	/**
	 * @var mixed Any event data the sender wants to convey
	 */
	protected $_data;
	/**
	 * @var string A user-defined event ID
	 */
	private $_eventId = null;

	//**************************************************************************
	//* Methods
	//**************************************************************************

	/**
	 * Constructor.
	 *
	 * @param mixed $data
	 */
	public function __construct( $data = null )
	{
		$this->_eventId = hash( 'sha256', spl_object_hash( $this ) . get_class( $this ) );
		$this->_data = $data;
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
	 * @return string
	 */
	public function getEventId()
	{
		return $this->_eventId;
	}

	/**
	 * @param array $excludes The list of elements to exclude
	 *
	 * @return array
	 */
	public function toArray( $excludes = null )
	{
		$_me = array(
			'stop_propagation' => $this->isPropagationStopped(),
		);

		$_excludes = Option::clean( $excludes );

		foreach ( get_object_vars( $this ) as $_key => $_value )
		{
			if ( method_exists( $this, 'get' . ( $_cleanKey = ltrim( $_key, '_' ) ) ) && !in_array( $_cleanKey, $_excludes ) )
			{
				$_me[Inflector::neutralize( $_cleanKey )] = $_value;
			}
		}

		return $_me;
	}

	/**
	 * @param array $data
	 *
	 * @return $this
	 */
	public function fromArray( $data = array() )
	{
		foreach ( $data as $_key => $_value )
		{
			//  Event ID cannot be changed
			if ( 'event_id' != $_key )
			{
				if ( method_exists( $this, 'set' . ( $_key = Inflector::deneutralize( $_key ) ) ) )
				{
					$this->{'set' . $_key}( $_value );
				}
			}
		}

		//  Special propagation stopper
		if ( isset( $data['stop_propagation'] ) && false !== $data['stop_propagation'] )
		{
			$this->stopPropagation();
		}

		return $this;
	}

	/**
	 * Kills propagation immediately
	 *
	 * @return SeedEvent
	 * @deprecated in 0.2.41, to be removed in 0.3.0 {@see Event::stopPropagation}
	 */
	public function kill()
	{
		$this->stopPropagation();

		return $this;
	}

	/**
	 * @return bool
	 * @deprecated in 0.2.31, to be removed in 0.3.0 {@see Event::isPropagationStopped}
	 */
	public function wasKilled()
	{
		return $this->isPropagationStopped();
	}

}
