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
namespace Kisma\Core\Interfaces;

use Kisma\Core\Events\SeedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Something that looks like an  event dispatcher
 */
interface EventDispatcherLike
{
    //*************************************************************************
    //	Constants
    //*************************************************************************

    /**
     * @var string The pattern to use when discovering listeners
     */
    const LISTENER_DISCOVERY_PATTERN = '/^_?on(.*)$/';

    //*************************************************************************
    //	Methods
    //*************************************************************************

    /**
     * @param string    $eventName
     * @param SeedEvent $event
     *
     * @return void
     */
    public static function trigger( $eventName, $event = null );

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName            The event to listen on
     * @param callable $listener             The listener
     * @param integer  $priority             The higher this value, the earlier an event
     *                                       listener will be triggered in the chain (defaults to 0)
     */
    public static function on( $eventName, $listener, $priority = 0 );

    /**
     * Turn off/unbind/remove $listener from an event
     *
     * @param string   $eventName
     * @param callable $listener
     *
     * @return void
     */
    public static function off( $eventName, $listener );

    /**
     * Searches the methods of an object for event handlers to automatically register.
     * Method signature must match EventDispatcherLike::LISTENER_DISCOVERY_PATTERN
     *
     * @param SubscriberLike|string $object    Object or class discovery target
     * @param array                 $listeners Array of 'event.name' => callback/closure pairs
     * @param string                $pattern   The pattern to use for discovery. Override constant in your subclass or pass in a different string.
     *
     * @return int The number of listeners found. False returned on an error
     */
    public static function discoverListeners( $object, $listeners = null, $pattern = self::LISTENER_DISCOVERY_PATTERN );

    /**
     * @param EventSubscriberInterface $subscriber
     *
     * @return void
     */
    public static function addSubscriber( EventSubscriberInterface $subscriber );

    /**
     * @param EventSubscriberInterface $subscriber
     *
     * @return void
     */
    public static function removeSubscriber( EventSubscriberInterface $subscriber );
}
