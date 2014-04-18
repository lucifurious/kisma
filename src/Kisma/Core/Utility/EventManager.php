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

use Kisma\Core\Interfaces\EventDispatcherLike;
use Kisma\Core\Interfaces\Events\PublisherLike;
use Kisma\Core\Interfaces\SubscriberLike;
use Kisma\Core\SeedUtility;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * EventManager class
 * Utility class that provides event management
 */
class EventManager extends SeedUtility implements EventDispatcherLike
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var string The default event handler signature
     * @deprecated Deprecated in  v0.2.19, to be removed in v0.3.0
     * @see        EventDispatcherLike::LISTENER_DISCOVERY_PATTERN
     */
    const DefaultEventHandlerSignature = '/^_?on(.*)$/';

    //*************************************************************************
    //* Members
    //*************************************************************************

    /**
     * @var EventDispatcher Our event system back-end
     */
    protected static $_dispatcher;
    /**
     * @var int A counter of fired events for the run of the app
     */
    protected static $_lastEventId = 0;

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param SubscriberLike|string $object
     * @param string                $pattern
     */
    public static function removeDiscoveredListeners( $object, $pattern = self::LISTENER_DISCOVERY_PATTERN )
    {
        //	Allow for passed in listeners
        $_listeners = static::_discoverObjectListeners( $object, $pattern, true );

        foreach ( $_listeners as $_eventName => $_callables )
        {
            foreach ( $_callables as $_listener )
            {
                try
                {
                    static::off( $_eventName, $_listener );
                }
                catch ( \Exception $_ex )
                {
                    //	Ignore missing listener errors
                }
            }
        }

    }

    /**
     * Wires up any event handlers automatically
     *
     * @param \Kisma\Core\Interfaces\SubscriberLike|string $object    Object or class discovery target
     * @param array|null                                   $listeners Array of 'event.name' => callback/closure pairs
     * @param string                                       $pattern
     *
     * @return void
     */
    public static function discoverListeners( $object, $listeners = null, $pattern = self::LISTENER_DISCOVERY_PATTERN )
    {
        //	Allow for passed in listeners
        $_listeners = $listeners ? : static::_discoverObjectListeners( $object, $pattern );

        //	And wire them up...
        if ( empty( $_listeners ) || !is_array( $_listeners ) )
        {
            return;
        }

        foreach ( $_listeners as $_eventName => $_callables )
        {
            foreach ( $_callables as $_listener )
            {
                static::on( $_eventName, $_listener );
            }
        }
    }

    /**
     * Builds a hash of events and handlers that are present in this object based on the event handler signature.
     * This merely builds the hash, nothing is done with it.
     *
     * @param \Kisma\Core\Interfaces\SubscriberLike|string $object     The object or class to scan
     * @param string                                       $pattern    The method listener pattern to scan for
     * @param bool                                         $rediscover By default, the discoverer will not wire up the same object's events more than once.
     *                                                                 Setting $rediscover to TRUE will force the rediscovery of the listeners, if any.
     *                                                                 The default is false.
     *
     * @return array|bool The listeners discovered. True if already discovered, False on error
     */
    public static function _discoverObjectListeners( $object, $pattern = self::LISTENER_DISCOVERY_PATTERN, $rediscover = false )
    {
        static $_discovered = array();

        $_listeners = array();

        $_objectId = spl_object_hash( is_string( $object ) ? '_class.' . $object : $object );

        if ( false === $rediscover && isset( $_discovered[$_objectId] ) )
        {
            return true;
        }

        try
        {
            $_mirror = new \ReflectionClass( $object );
            $_methods = $_mirror->getMethods( \ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED );

            //	Check each method for the event handler signature
            foreach ( $_methods as $_method )
            {
                //	Event handler?
                if ( 0 == preg_match( $pattern, $_method->name, $_matches ) || empty( $_matches[1] ) )
                {
                    continue;
                }

                //	Neutralize the name
                $_eventName = Inflector::neutralize( $_matches[1] );

                if ( !isset( $_listeners[$_eventName] ) )
                {
                    $_listeners[$_eventName] = array();
                }

                //	Save off a callable
                $_listeners[$_eventName][] = array( $object, $_method->name );

                //	Clean up
                unset( $_matches, $_method );
            }

            unset( $_methods, $_mirror );

            $_discovered[$_objectId] = true;
        }
        catch ( \Exception $_ex )
        {
            return false;
        }

        //	Return the current map
        return $_listeners;
    }

    /**
     * {@InheritDoc}
     */
    public static function on( $eventName, $listener, $priority = 0 )
    {
        static::getDispatcher()->addListener( $eventName, $listener, $priority );
    }

    /**
     * {@InheritDoc}
     */
    public static function off( $eventName, $listener )
    {
        static::getDispatcher()->removeListener( $eventName, $listener );
    }

    /**
     * {@InheritDoc}
     */
    public static function trigger( $eventName, $event = null )
    {
        return static::getDispatcher()->dispatch( $eventName, $event );
    }

    /**
     * {@InheritDoc}
     */
    public static function addSubscriber( EventSubscriberInterface $subscriber )
    {
        static::getDispatcher()->addSubscriber( $subscriber );
    }

    /**
     * {@InheritDoc}
     */
    public static function removeSubscriber( EventSubscriberInterface $subscriber )
    {
        static::getDispatcher()->removeSubscriber( $subscriber );
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
     * @param \Kisma\Core\Events\SeedEvent $event
     *
     * @return string
     */
    public static function generateEventId( $event )
    {
        return hash( 'sha256', $event->getSource()->getId() . getmypid() . microtime( true ) ) . '.' . self::$_lastEventId++;
    }

    /**
     * @return EventDispatcher
     */
    public static function getDispatcher()
    {
        if ( null === static::$_dispatcher )
        {
            static::$_dispatcher = new EventDispatcher();
        }

        return self::$_dispatcher;
    }
}
