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
class EventHandling extends \Kisma\Components\Aspect
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
	 * Returns a standardized event name if this component has the requested
	 * event, otherwise false
	 *
	 * @param string $eventName The name of the event
	 * @param bool $returnEvent If true, the event object will be returned instead of the name
	 * @return false|string|\Kisma\Components\Event
	 */
	public function hasEvent( $eventName, $returnEvent = false )
	{
		return K::hasComponent( $this->_events, $eventName, $returnEvent );
	}

	/**
	 * Helper to create a new event.
	 * @param string $eventName
	 * @param mixed|null $eventData
	 * @param string $eventClass
	 * @return \Kisma\Components\Event
	 */
	public function create( $eventName, $eventData = null, $eventClass = 'kisma.components.Event' )
	{
		if ( false !== ( $_event = $this->hasEvent( $eventName, true ) ) )
		{
			return $_event;
		}

		$_eventKey = K::kismaTag( $eventName, true );

		$_eventOptions = array(
			'eventId' => $eventName,
			'eventData' => $eventData,
			'source' => $this->_linker,
			'eventKey' => $_eventKey,
		);

		$_event = K::createComponent( $eventClass, $_eventOptions );

		if ( K::getDebugLevel( \Kisma\DebugLevel::Nutty ) )
		{
			K::logDebug( 'Event "' . $_eventKey . '" created for source "' . get_class( $this->_linker ) );
		}

		return $this->_events[$_eventKey] = $_event;
	}

	/**
	 * @param string $eventName
	 * @param callback $callback
	 * @param mixed|null $eventData
	 * @param string $eventClass
	 * @return bool
	 */
	public function bind( $eventName, $callback, $eventData = null, $eventClass = 'kisma.components.Event' )
	{
		//	Get or Create a new event
		$_event = $this->create( $eventName, $eventData, $eventClass );

		//	Add the handler
		return $_event->addHandler( $callback, $eventData );
	}

	/**
	 * @param string $eventName
	 * @param callback $callback
	 * @return bool
	 */
	public function unbind( $eventName, $callback )
	{
		//	If we don't have the event, fail & bail
		if ( false === ( $_event = $this->hasEvent( $eventName, true ) ) )
		{
			return false;
		}

		return $_event->removeHandler( $callback );
	}

	/**
	 * Automatically binds any events in the event map
	 *
	 * @param array $options
	 * @return bool
	 */
	public function autoBind( $options = array() )
	{
		//	Check each method for the event handler signature
		foreach ( $this->_eventMap as $_eventName => $_callback )
		{
			//	Bind it like a binder!
			$this->bind( $_eventName, $_callback );
		}
	}

	/**
	 * @param string $eventName
	 * @param mixed|null $eventData
	 * @param callback|null $callback
	 * @return bool Returns true if the $eventName has no handlers.
	 */
	public function trigger( $eventName, $eventData = null, $callback = null )
	{
		//	If we don't have the event, create one...
		if ( false !== ( $_event = $this->hasEvent( $eventName, true ) ) )
		{
			return $_event->fire( $eventData, $callback );
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


	/**
	 * @param string $eventHandlerSignature
	 * @return \Kisma\Aspects\EventHandling
	 */
	public function setEventHandlerSignature( $eventHandlerSignature = 'on' )
	{
		$this->_eventHandlerSignature = $eventHandlerSignature;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEventHandlerSignature()
	{
		return $this->_eventHandlerSignature;
	}

}
