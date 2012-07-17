<?php
/**
 * @file
 *            Down and dirty event system
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 *
 * @copyright Copyright (c) 2009-2012 Jerry Ablan
 * @license   http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author    Jerry Ablan <kisma@pogostick.com>
 *
 * @ingroup   utility
 */
namespace Kisma\Utility;

use \Kisma\Utility\Inflector;
use \Kisma\Components\ObjectEvent;

/**
 * EventManager class
 * Utility class that provides event management
 */
class EventManager
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The default event handler signature
	 */
	const DefaultEventHandlerSignature = '/^_?on(.*)$/';

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var array The event map for the application
	 */
	protected static $_eventMap = array();

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Wires up any event handlers automatically
	 *
	 * @param object     $object
	 * @param array|null $listeners Array of 'event.name' => callback/closure pairs
	 * @param string     $signature
	 *
	 * @return void
	 */
	public static function subscribe( $object, $listeners = null, $signature = self::DefaultEventHandlerSignature )
	{
		//	Allow for passed in listeners
		$_listeners = $listeners ? : self::discover( $object, $signature );

		//	Nothing to do? Bail
		if ( empty( $_listeners ) )
		{
			return;
		}

		//	And wire them up...
		foreach ( $_listeners as $_eventName => $_callback )
		{
			self::$_eventMap[Inflector::tag( $_eventName, true )][] = $_callback;
			unset( $_callback, $_eventName );
		}

		unset( $_listeners );
	}

	/**
	 * Builds a hash of events and handlers that are present in this object based on the event handler signature.
	 * This merely builds the hash, nothing is done with it.
	 *
	 * @param        $object
	 * @param string $signature
	 *
	 * @internal param bool $appendToList
	 *
	 * @return array
	 */
	public static function discover( $object, $signature = self::DefaultEventHandlerSignature )
	{
		static $_discovered = array();

		$_listeners = array();

		if ( !isset( $_discovered[spl_object_hash( $object )] ) )
		{
			$_mirror = new \ReflectionClass( $object );
			$_methods = $_mirror->getMethods( \ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED );

			//	Check each method for the event handler signature
			foreach ( $_methods as $_method )
			{
				$_name = $_method->name;

				//	Event handler?
				if ( 0 == preg_match( $signature, $_name, $_matches ) )
				{
					continue;
				}

				//	Add to the end of the array...
				$_eventTag = Inflector::tag( $_matches[1], true );

				if ( !isset( $_listeners[$_eventTag] ) )
				{
					$_listeners[$_eventTag] = array();
				}

				//	Create a closure to house the call.
				$_listeners[$_eventTag][] = function( $event ) use ( $object, $_method )
				{
					return call_user_func( array( $object, $_method->name ), $event );
				};

				unset( $_eventTag, $_matches, $_method );
			}

			unset( $_methods, $_mirror );

			$_discovered[spl_object_hash( $object )] = true;
		}

		//	Return the current map
		return $_listeners;
	}

	/**
	 * Publishes an event to all subscribers
	 *
	 * @static
	 *
	 * @param object $publisher
	 * @param string $eventName
	 * @param mixed  $eventData
	 *
	 * @throws \Kisma\InvalidEventHandlerException
	 * @return bool|int
	 */
	public static function publish( $publisher, $eventName, &$eventData = null )
	{
		$_eventTag = Inflector::tag( $eventName, true, true );

		if ( !isset( self::$_eventMap[$_eventTag] ) || empty( self::$_eventMap[$_eventTag] ) )
		{
			return false;
		}

		$_event =
			( $eventData instanceof ObjectEvent )
				?
				$eventData
				:
				new ObjectEvent( $publisher, $eventData );

		foreach ( self::$_eventMap[$_eventTag] as $_callback )
		{
			//	Stop further handling if the event has been handled...
			if ( $_event->getStopPropagation() )
			{
				break;
			}

			//	Call the handler
			if ( is_string( $_callback ) || is_callable( $_callback, true ) )
			{
				if ( false === call_user_func( $_callback, $_event ) )
				{
					//	Break the chain
					break;
				}

				unset( $_callback );

				//	If we get here, onward ho!
				continue;
			}

			//	Oops!
			throw new \Kisma\InvalidEventHandlerException(
				'Event "' . ( is_object( $_callback[0] )
					?
					get_class( $_callback[0] )
					:
					'<unknownClass>' ) . '.' . $_eventTag . ' has an invalid listener subscribed to it.'
			);
		}

		return true;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @return array The map of events to listeners
	 */
	public static function getEventMap()
	{
		return self::$_eventMap;
	}

}
