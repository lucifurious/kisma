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
 * EventHandling
 * An aspect that provides event handling to a component
 *
 * Basics
 * ======
 *
 * Any component to which this aspect is linked will gain
 * the capability of receiving notification of any event
 * they choose.
 *
 * Some components in Kisma fire events as well. These are
 * defined in each class. The base Component class, for
 * instance, fires and AfterConstructor event when the
 * method completes.
 *
 * You can bind one or more handlers to any event in any
 * object with this aspect. Event handlers are called in
 * the order they are bound. Event propagation can be
 * stopped in two ways.
 *
 * 1. Return "false" from your event handler.
 * 		If an event handler returns "false" event propagation
 * 		halts immediately, with the fireEvent() method returning
 * 		"false" as well to the object firing the event.
 *
 * 		Returning "true" from an event handler continues event
 * 		propagation.
 *
 * 2. Setting the $continuePropagation property to "false".
 * 		The only argument sent to your event handler is the
 * 		\Kisma\Components\Event which fired the event. Call
 * 		Event::setContinuePropagation( false ) to stop further
 * 		propagation. Whatever you return from your handler
 * 		will be returned to the firing object.
 *
 * Event Names
 * ===========
 *
 * Event names, like all other symbolic tags in Kisma, are
 * standardized. Therefore you can use camel-cased names or
 * underscore-delimited names. Your call.
 *
 * Triggering Events
 * =================
 *
 * Kisma utilizes underscore-delimited names throughout the core.
 * I chose this method for its clarity and readability.
 *
 * Example:
 *
 *<pre>
 *	\Kisma\Components\Component::__constructor( $options = array() )
 *	{
 * 		...
 * 		//	Trigger our afterConstructor event
 *		$this->triggerEvent( 'after_constructor' );
 *	}
 *</pre>
 *
 * Event Handlers
 * ==============
 *
 * Event handler signature is:
 *
 *<pre>
 * 	function onEventName( Event $event, $eventData = null );
 *</pre>
 *
 * Where EventName is the name you've given your event, or a pre-defined
 * framework event.
 * 
 */
class EventHandling extends Aspect implements \Kisma\IEvent
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Kisma\Components\Event[] My events indexed by event name
	 */
	protected $_events = array();

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Helper to create a new event.
	 * @param string $eventId
	 * @param mixed|null $eventData
	 * @param string $eventClass
	 * @return \Kisma\Components\Event
	 */
	public function createEvent( $eventId, $eventData = null, $eventClass = 'Kisma\Components\Event' )
	{
		$_eventClass = K::standardizeName( $eventClass );

		$_eventOptions = array(
			'eventId' => $eventId,
			'eventData' => $eventData,
			'source' => $this->_parent,
			'eventClass' => $_eventClass,
		);

		$_event = new $_eventClass( $_eventOptions );

		K::logDebug( 'Event "' . $eventId . '" created for source "' . get_class( $this->_parent ) );

		return $_event;
	}

	/**
	 * Returns the standardized event name or false if we do not have that event.
	 * @param string $eventName
	 * @return string|false
	 */
	public function hasEvent( $eventName )
	{
		$_eventName = K::standardizeName( $eventName );
		return ( isset( $this->_events, $this->_events[$_eventName] ) ? $_eventName : false );
	}

	/**
	 * @param string $eventName
	 * @param callback $callback
	 * @param mixed|null $eventData
	 * @return bool
	 */
	public function bindEvent( $eventName, $callback, $eventData = null )
	{
		//	If we don't have the event, create one...
		if ( false === ( $_eventName = $this->hasEvent( $eventName ) ) )
		{
			//	Create a new event
			$_eventName = K::standardizeName( $eventName );
			$this->_events[$_eventName] = $this->createEvent( $_eventName, $eventData );
		}

		//	Add the handler
		return $this->_events[$_eventName]->addHandler( $callback, $eventData );
	}

	/**
	 * @param string $eventName
	 * @param callback $callback
	 * @return bool
	 */
	public function unbindEvent( $eventName, $callback )
	{
		//	If we don't have the event, create one...
		if ( false !== ( $_eventName = $this->hasEvent( $eventName ) ) )
		{
			return $this->_events[$_eventName]->removeHandler( $callback );
		}

		//	Event doesn't exist.
		return false;
	}

	/**
	 * @param string $eventName
	 * @param mixed|null $eventData
	 * @param callback|null $callback
	 * @return bool Returns true if the $eventName has no handlers.
	 */
	public function triggerEvent( $eventName, $eventData = null, $callback = null )
	{
		//	If we don't have the event, create one...
		if ( false !== ( $_eventName = $this->hasEvent( $eventName ) ) )
		{
			return $this->_events[$_eventName]->fireEvent( $eventData, $callback );
		}

		//	It's all good!
		return true;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param \Kisma\Components\Event[] $events
	 * @return \Kisma\Aspects\EventHandling
	 */
	public function setEvents( $events )
	{
		$this->_events = $events;
		return $this;
	}

	/**
	 * @return \Kisma\Components\Event[]
	 */
	public function getEvents()
	{
		return $this->_events;
	}
}
