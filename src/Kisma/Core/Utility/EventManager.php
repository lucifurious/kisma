<?php
/**
 * EventManager.php
 */
namespace Kisma\Core\Utility;
use Kisma\Core\Exceptions\InvalidEventHandlerException;

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
	 * @param \Kisma\Core\Interfaces\Subscriber $object
	 * @param array|null                        $listeners Array of 'event.name' => callback/closure pairs
	 * @param string                            $signature
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
	 * @param \Kisma\Core\Interfaces\Subscriber $object
	 * @param string                            $signature
	 *
	 * @internal param bool $appendToList
	 *
	 * @return array
	 */
	public static function discover( $object, $signature = self::DefaultEventHandlerSignature )
	{
		static $_discovered = array();

		if ( !( $object instanceof \Kisma\Core\Interfaces\Subscriber ) )
		{
			//	Not a subscriber, beat it...
			$_discovered[$object->getId()] = true;

			return false;
		}

		$_listeners = array();

		if ( !isset( $_discovered[$object->getId()] ) )
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
				$_eventName = Inflector::tag( $_matches[1] );

				if ( null !== ( $_eventTag = $_mirror->getConstant( $_eventName ) ) )
				{
					//	We have a winner!
					if ( !isset( $_listeners[$_eventTag] ) )
					{
						$_listeners[$_eventTag] = array();
					}

					//	Create a closure to house the call.
					$_listeners[$_eventTag][] = function ( $event ) use ( $object, $_name )
					{
						return call_user_func( array( $object, $_name ), $event );
					};
				}

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
	 * @param null|\Kisma\Core\Interfaces\Subscriber $publisher
	 * @param string                                 $eventName
	 * @param mixed                                  $eventData
	 *
	 * @throws \Kisma\InvalidEventHandlerException
	 * @return bool|int
	 */
	public static function publish( $publisher, $eventName, $eventData = null )
	{
		//	Ensure this is a valid event
		$_eventTag = Inflector::tag( $eventName, true );

		if ( !isset( self::$_eventMap[$_eventTag] ) || empty( self::$_eventMap[$_eventTag] ) )
		{
			//	No registered listeners, bail
			return false;
		}

		//	Make a new event if one wasn't provided
		$_event =
			( $eventData instanceof \Kisma\Core\Events\SeedEvent )
				?
				$eventData
				:
				new \Kisma\Core\Events\SeedEvent( $publisher, $eventData );

		//	Call each handler in order
		foreach ( self::$_eventMap[$_eventTag] as $_callback )
		{
			//	Stop further handling if the event has been handled...
			if ( $_event->wasKilled() )
			{
				break;
			}

			//	Call the handler
			if ( is_string( $_callback ) || is_callable( $_callback ) )
			{
				//	Call the method
				if ( false === call_user_func( $_callback, $_event ) )
				{
					return false;
				}
			}
			elseif ( is_array( $_callback ) && 1 == count( $_callback ) && $_callback[0] instanceof \Closure )
			{
				//	Call the closure...
				if ( false === $_callback[0]( $_event ) )
				{
					return false;
				}
			}
			else
			{
				//	Oops!
				throw new InvalidEventHandlerException(
					'Event "' .
						( is_object( $_callback[0] )
							?
							get_class( $_callback[0] )
							:
							'<unknownClass>' ) .
						'.' . $_eventTag . ' has an invalid listener subscribed to it.'
				);
			}

			unset( $_callback );
		}

		return true;
	}

	/**
	 * @param object $object
	 *
	 * @return bool|string
	 */
	public static function canPublish( $object )
	{
		//	Publisher with an event manager?
		if ( $object instanceof \Kisma\Core\Interfaces\Events\Publisher )
		{
			//	Return the event manager service or false
			return $object->getServiceClass( 'event_manager' );
		}

		//	Nope!
		return false;
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
