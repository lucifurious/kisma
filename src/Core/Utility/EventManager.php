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
namespace Kisma\Core\Utility;

use Kisma\Core\Events\SeedEvent;
use Kisma\Core\Exceptions\InvalidEventHandlerException;
use Kisma\Core\Interfaces\Events\PublisherLike;
use Kisma\Core\Interfaces\SubscriberLike;
use Kisma\Core\SeedUtility;

/**
 * EventManager class
 * Utility class that provides event management
 */
class EventManager extends SeedUtility
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The default event handler signature
	 */
	const DefaultEventHandlerSignature = '/^_?on(.*)$/';

	//*************************************************************************
	//* Members
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
	//* Methods
	//*************************************************************************

	/**
	 * Wires up any event handlers automatically
	 *
	 * @param SubscriberLike $object
	 * @param array|null     $listeners Array of 'event.name' => callback/closure pairs
	 * @param string         $signature
	 *
	 * @return void
	 */
	public static function subscribe( SubscriberLike $object, $listeners = null, $signature = self::DefaultEventHandlerSignature )
	{
		Log::debug( 'START subscribing' );

		//	Allow for passed in listeners
		$_listeners = $listeners ? : static::discover( $object, $signature );

		//	Nothing to do? Bail
		if ( empty( $_listeners ) )
		{
			return;
		}

		//	And wire them up...
		foreach ( $_listeners as $_eventName => $_callback )
		{
			static::on(
				$object,
				$_tag = Inflector::neutralize( $_eventName ),
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
	 * @param SubscriberLike $object
	 * @param string         $signature
	 *
	 * @internal param bool $appendToList
	 *
	 * @return array
	 */
	public static function discover( $object, $signature = self::DefaultEventHandlerSignature )
	{
		static $_discovered = array();

		$_objectId = $object->getId();

		if ( !( $object instanceof SubscriberLike ) )
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
				if ( 0 == preg_match( $signature, $_name, $_matches ) || empty( $_matches[1] ) )
				{
					continue;
				}

				//	Add to the end of the array...
				$_eventName = Inflector::neutralize( $_matches[1] );
				$_eventTag = $_mirror->getConstant( $_eventName ) ? : $_eventName;

				$_listeners[$_eventTag] = function ( $event ) use ( $object, $_name )
				{
					return call_user_func( array( $object, $_name ), $event );
				};

				unset( $_eventTag, $_matches, $_method );
			}

			unset( $_methods, $_mirror );

			$_discovered[$_objectId] = true;
		}

		Log::debug( 'END event discovery' );

		//	Return the current map
		return $_listeners;
	}

	/**
	 * @param SubscriberLike $object
	 * @param string         $tag
	 * @param callable|null  $listener
	 */
	public static function on( $object, $tag, $listener = null )
	{
		if ( null === $listener )
		{
			static::unsubscribe( $object, $tag );

			return;
		}

		$_tag = Inflector::neutralize( $tag );
		$_objectId = $object->getId();

		if ( !isset( static::$_eventMap[$_tag] ) )
		{
			static::$_eventMap[$_tag] = array();
		}

		if ( !isset( static::$_eventMap[$_tag][$_objectId] ) )
		{
			$_listeners[$_tag][$_objectId] = array();
		}

		static::$_eventMap[$_tag][$_objectId][] = $listener;
	}

	/**
	 * @param SubscriberLike $object
	 * @param string         $eventName
	 */
	public static function unsubscribe( $object, $eventName = null )
	{
		$_objectId = $object->getId();
		$_tag = Inflector::tag( $eventName, true );

		foreach ( static::$_eventMap as $_eventTag => $_subscribers )
		{
			Log::debug( '---- Unsub "' . $_objectId . '" from "' . $_eventTag . '"' );

			foreach ( $_subscribers as $_subscriberId => $_closures )
			{
				if ( $_objectId == $_subscriberId )
				{
					foreach ( Option::clean( $_closures ) as $_index => $_closure )
					{
						if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) )
						{
							/** @noinspection PhpUndefinedMethodInspection */
							$_closure->bindTo( null );
						}

						//	Remove and reindex the map
						unset( static::$_eventMap[$_eventTag][$_subscriberId][$_index], $_closure );
						static::$_eventMap[$_eventTag][$_subscriberId] = array_values( static::$_eventMap[$_eventTag][$_subscriberId] );
					}
				}
			}

			if ( $_eventTag == $_tag )
			{
				break;
			}
		}

		foreach ( static::$_eventMap as $_eventTag => $_subscriberId )
		{
			if ( $_objectId == $_subscriberId )
			{
				unset( static::$_eventMap[$_eventTag][$_objectId] );

				Log::debug(
					'-- "' . $object->getTag() . '" unsubscribed from "' . $_eventTag . '"',
					array(
						'tag' => $_subscriberId,
					)
				);
			}
		}
	}

	/**
	 * Publishes an event to all subscribers
	 *
	 * @static
	 *
	 * @param null|SubscriberLike|PublisherLike $publisher
	 * @param string                            $eventName
	 * @param mixed                             $eventData
	 *
	 * @throws \Kisma\Core\Exceptions\InvalidEventHandlerException
	 * @return bool|int
	 */
	public static function publish( $publisher, $eventName, $eventData = null )
	{
		//	Make sure object is cool
		if ( !static::canPublish( $publisher ) )
		{
			//	Not a publisher. Bail
			return false;
		}

		//	Ensure this is a valid event
		$_eventTag = Inflector::neutralize( $eventName );

		if ( null === ( $_subscribers = Option::get( static::$_eventMap, $_eventTag ) ) || empty( $_subscribers ) )
		{
			//	No registered listeners, bail
			return false;
		}

		//	Make a new event if one wasn't provided
		$_event = ( $eventData instanceof SeedEvent ) ? $eventData : new SeedEvent( $publisher, $eventData );
		$_event->setEventId( static::generateEventId( $_event ) );
		$_event->setEventTag( $_eventTag );

		//	Call each handler in order
		$_publisherId = $publisher->getId();

		Log::debug( '---- Publish "' . $_eventTag . '" from "' . $_publisherId . '"' );

		foreach ( $_subscribers as $_subscriberId => $_listeners )
		{
			//	Stop further handling if the event has been kilt...
			if ( empty( $_listeners ) || $_event->wasKilled() )
			{
				return true;
			}

			//	Call the handler
			foreach ( $_listeners as $_listener )
			{
				if ( is_callable( $_listener ) )
				{
					//	Call the method
					$_listener( $_event );
				}
				else
				{
					//	Oops!
					throw new InvalidEventHandlerException( 'Invalid event handler "' . $_subscriberId . '::' . $_eventTag . '"' );
				}
			}

			unset( $_listeners );
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
		return ( $object instanceof PublisherLike );
	}

	/**
	 * @param object $object
	 *
	 * @return bool|string
	 */
	public static function isSubscriber( $object )
	{
		//	A subscriber?
		return ( $object instanceof SubscriberLike );
	}

	/**
	 * @param SeedEvent $event
	 *
	 * @return string
	 */
	public static function generateEventId( $event )
	{
		return hash( 'sha256', $event->getSource()->getId() . getmypid() . microtime( true ) ) . '.' . static::$_lastEventId++;
	}

	/**
	 * @return array The map of events to listeners
	 */
	public static function getEventMap()
	{
		return static::$_eventMap;
	}
}
