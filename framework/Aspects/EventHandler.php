<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license			http://github.com/Pogostick/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @category		Kisma_Aspects
 * @package			kisma.aspects
 * @namespace		\Kisma\Aspects
 * @since			v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Aspects
 */
namespace Kisma\Aspects;

/**
 * EventHandler
 * Provides event capabilities to an object
 */
class EventHandler extends Aspect implements \Kisma\IEvent
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Kisma\Events\Event[] My events indexed by event name
	 */
	protected $_events = array();

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Returns the event or false if we do not have that event.
	 * @param string $eventName
	 * @return bool|\Kisma\Events\Event
	 */
	public function hasEvent( $eventName )
	{
		$_eventName = \Kisma\Kisma::standardizeName( $eventName );

		if ( isset( $this->_events, $this->_events[$_eventName] ) )
		{
			return $this->_events[$_eventName];
		}

		return false;
	}

	/**
	 * @param string $eventName
	 * @param callback $callback
	 */
	public function bindEvent( $eventName, $callback )
	{
		if ( false !== ( $_event = $this->hasEvent( $eventName ) ) )
		{

		}
	}

	/**
	 * @param string $eventName
	 * @param callback $callback
	 */
	public function unbindEvent( $eventName, $callback )
	{
		if ( false !== ( $_event = $this->hasEvent( $eventName ) ) )
		{

		}
	}

	/**
	 * @param string $eventName
	 * @param mixed|null $eventData
	 * @param callback|null $callback
	 */
	public function triggerEvent( $eventName, $eventData = null, $callback = null )
	{
		if ( false !== ( $_event = $this->hasEvent( $eventName ) ) )
		{

		}
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * If we know of an event, fire it
	 * @param string $eventName
	 * @param mixed|null $eventData
	 * @param callback|null $callback
	 * @return bool
	 */
	protected function _fireEvent( $eventName, $eventData = null, $callback = null )
	{
		if ( false !== ( $_event = $this->hasEvent( $eventName ) ) )
		{
			return $_event->fireEvent( $eventData, $callback );
		}

		return false;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param \Kisma\Events\Event[] $events
	 * @return \Kisma\Aspects\EventHandler
	 */
	public function setEvents( $events )
	{
		$this->_events = $events;
		return $this;
	}

	/**
	 * @return \Kisma\Events\Event[]
	 */
	public function getEvents()
	{
		return $this->_events;
	}
}
