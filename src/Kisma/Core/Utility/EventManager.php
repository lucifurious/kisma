<?php
/**
 * EventManager.php
 */
namespace Kisma\Core\Utility;

use Kisma\Core\Exceptions\InvalidEventHandlerException;
use Kisma\Core\Utility\Log;

/**
 * EventManager class
 * Utility class that provides event management
 */
class EventManager extends \Kisma\Core\SeedUtility
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
	/**
	 * @var int A counter of fired events for the run of the app
	 */
	protected static $_lastEventId = 0;

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
		Log::debug( 'START subscribing' );

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
			$_tag = Inflector::tag( $_eventName, true );

			self::on(
				$object,
				$_tag,
				$_callback
			);

			Log::debug( '-- "' . $object->getTag() . '" subscribed to "' . $_tag . '"' );

			unset( $_callback, $_eventName, $_tag );
		}

		unset( $_listeners );

		Log::debug( 'END subscribing' );
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

		$_objectId = $object->getId();

		if ( !( $object instanceof \Kisma\Core\Interfaces\Subscriber ) )
		{
			//	Not a subscriber, beat it...
			$_discovered[$_objectId] = true;

			return false;
		}

		Log::debug( 'START event discovery' );

		$_listeners = array();

		if ( !isset( $_discovered[$_objectId] ) )
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

				if ( null === ( $_eventTag = $_mirror->getConstant( $_eventName ) ) )
				{
					$_eventTag = Inflector::tag( $_matches[1], true );
				}

				self::on(
					$object,
					$_eventTag,
					function ( $event ) use ( $object, $_name )
					{
						return call_user_func( array( $object, $_name ), $event );
					}
				);

				unset( $_eventTag, $_matches, $_method );
			}

			unset( $_methods, $_mirror );

			$_discovered[spl_object_hash( $object )] = true;
		}

		Log::debug( 'END event discovery' );

		//	Return the current map
		return $_listeners;
	}

	/**
	 * @param \Kisma\Core\Interfaces\Subscriber  $object
	 * @param string                             $tag
	 * @param callable|null                      $listener
	 */
	public static function on( $object, $tag, $listener = null )
	{
		if ( null === $listener )
		{
			self::unsubscribe( $object, $tag );

			return;
		}

		$_objectId = $object->getId();

		if ( !isset( self::$_eventMap[$tag] ) )
		{
			self::$_eventMap[$tag] = array();
		}

		if ( !isset( self::$_eventMap[$tag][$_objectId] ) )
		{
			$_listeners[$tag][$_objectId] = array();
		}

		self::$_eventMap[$tag][$_objectId][] = $listener;
	}

	/**
	 * @param \Kisma\Core\Interfaces\Subscriber $object
	 * @param string                            $eventName
	 */
	public static function unsubscribe( $object, $eventName = null )
	{
		$_objectId = $object->getId();
		$eventName = Inflector::tag( $eventName, true );

		foreach ( self::$_eventMap as $_eventTag => $_subscribers )
		{
			\Kisma\Core\Utility\Log::debug( '---- Unsub "' . $_objectId . '" from "' . $_eventTag . '"' );

			foreach ( $_subscribers as $_subscriberId => $_closures )
			{
				if ( $_objectId == $_subscriberId )
				{
					/** @var $_closures \Closure[] */
					foreach ( Option::clean( $_closures ) as $_index => $_closure )
					{
						if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) )
						{
							\Closure::bind( $_closure, null );
						}

						//	Remove and reindex the map
						unset( self::$_eventMap[$_eventTag][$_subscriberId][$_index] );
						self::$_eventMap[$_eventTag][$_subscriberId] = array_values( self::$_eventMap[$_eventTag][$_subscriberId] );
					}
				}
			}

			if ( $_eventTag == $eventName )
			{
				break;
			}
		}

//		foreach ( self::$_eventMap as $_eventTag => $_subscriberId )
//		{
//			if ( $_objectId == $_subscriberId )
//			{
//				unset( self::$_eventMap[$_eventTag][$_objectId] );
//
//				Log::debug(
//					'-- "' . $object->getTag() . '" unsubscribed from "' . $_eventTag . '"',
//					array(
//						'tag'     => $_subscriberId,
//					)
//				);
//			}
//		}
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
	 * @throws \Kisma\Core\Exceptions\InvalidEventHandlerException
	 * @return bool|int
	 */
	public static function publish( $publisher, $eventName, $eventData = null )
	{
		//	Make sure object is cool
		if ( !self::canPublish( $publisher ) )
		{
			//	Not a publisher. Bail
			return false;
		}

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

		$_event->setEventTag( $_eventTag );

		//	Call each handler in order
		if ( isset( self::$_eventMap[$_eventTag] ) && !empty( self::$_eventMap[$_eventTag] ) )
		{
			$_publisherId = $publisher->getId();

			\Kisma\Core\Utility\Log::debug( '---- Publish "' . $_eventTag . '" from "' . $_publisherId . '"' );

			foreach ( self::$_eventMap[$_eventTag] as $_listenerIndex => $_listeners )
			{
				foreach ( $_listeners as $_subscriberId => $_closures )
				{
					/** @var $_closures \Closure[] */
					foreach ( Option::clean( $_closures ) as $_closure )
					{
						//	Stop further handling if the event has been kilt...
						if ( $_event->wasKilled() )
						{
							return true;
						}

						//	Generate an id...
						$_event->setEventId( self::generateEventId( $_event ) );

						//	Call the handler
						if ( is_string( $_closure ) || is_callable( $_closure ) )
						{
							//	Call the method
							$_result = call_user_func( $_closure, $_event );

//							Log::debug(
//								'-- "' . $publisher->getTag() . '" handler for "' . $_event->getEventTag() . '" called',
//								array(
//									'tag'     => $_subscriberId,
//									'result'  => print_r( $_result, true ),
//									'eventId' => $_event->getEventId(),
//								)
//							);
						}
						elseif ( is_array( $_closure ) && 1 == count( $_closure ) && $_closure[0] instanceof \Closure )
						{
							//	Call the closure...
							if ( false === $_closure[0]( $_event ) )
							{
								return false;
							}
						}
						else
						{
							//	Oops!
							throw new InvalidEventHandlerException(
								'Event "' .
									( is_object( $_closure[0] )
										?
										get_class( $_closure[0] )
										:
										'<unknownClass>' ) .
									'.' . $_eventTag . ' has an invalid listener subscribed to it.'
							);
						}

						unset( $_closure );
					}

					unset( $_closures );
				}

				unset( $_listeners );
			}
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
		return ( $object instanceof \Kisma\Core\Interfaces\Events\Publisher );
	}

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return string
	 */
	public static function generateEventId( $event )
	{
		return sha1( $event->getSource()->getId() . getmypid() . microtime( true ) ) . '.' . self::$_lastEventId++;
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