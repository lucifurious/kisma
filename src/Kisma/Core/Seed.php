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
namespace Kisma\Core;

use Kisma\Core\Events\Enums\LifeEvents;
use Kisma\Core\Events\SeedEvent;
use Kisma\Core\Interfaces\Events\SeedLike;
use Kisma\Core\Interfaces\PublisherLike;
use Kisma\Core\Interfaces\SubscriberLike;
use Kisma\Core\Utility\EventManager;
use Kisma\Core\Utility\Inflector;
use Kisma\Core\Utility\Option;

/**
 * Seed
 * A nugget of goodness that grows into something wonderful
 *
 * Seed provides a simple publish/subscribe service. You're free to use it or not. Never required.
 *
 * Publish/Subscribe
 * =================
 * A simple publish/subscribe service. Yeah, fancy name for event system.
 *
 * An event is defined by the presence of a method whose name starts with 'on'.
 * The event name is the method name. When an event is triggered, event handlers attached
 * to the event will be invoked automatically.
 *
 * An event is triggered by calling the {@link publish} method. Attached event
 * handlers will be invoked automatically in the order they were attached to the event.
 *
 * Event handlers should have the following signature:
 * <pre>
 * public|protected|private function [_]onEventName( $event = null ) { ... }
 * </pre>
 *
 * $event (\Kisma\Core\Events\SeedEvent) will contain details about the event in question.
 *
 * To subscribe to an event, call the {@link EventManager::subscribe} method.
 *
 * You may also use closures for event handlers, ala jQuery
 *
 * This class has a two default events:
 *   - after_construct
 *   - before_destruct
 *
 * Unless otherwise specified, these events will only be emitted
 * if Seed::$enableLifeEvents is TRUE. Good for debugging
 *
 * To disable this feature, set $discoverEvents to false before calling the parent constructor.
 */
class Seed implements SeedLike, PublisherLike, SubscriberLike
{
    //********************************************************************************
    //* Variables
    //********************************************************************************

    /**
     * @var string A unique ID assigned to this object, the last part of which is the creation time
     */
    private $_id;
    /**
     * @var string A "key" quality tag for this object. Defaults to the key-inflected base class name (i.e. "seed")
     */
    protected $_tag;
    /**
     * @var string A display quality name for this object. Defaults to the full class name (i.e. "\Kisma\Core\Seed")
     */
    protected $_name;
    /**
     * @var bool Turn on/off life events
     */
    protected $_enableLifeEvents = false;

    //********************************************************************************
    //* Methods
    //********************************************************************************

    /**
     * Base constructor
     *
     * @param array|object $settings An array of key/value pairs that will be placed into storage
     */
    public function __construct( $settings = array() )
    {
        //	Since $_id is read-only we remove it
        Option::remove( $settings, 'id' );

        //	Now, set the rest
        if ( !empty( $settings ) )
        {
            foreach ( $settings as $_key => $_value )
            {
                Option::set( $this, $_key, $_value );
            }
        }

        //	Wake-up the events
        $this->__wakeup();
    }

    /**
     * When unserializing an object, this will re-attach any event handlers...
     */
    public function __wakeup()
    {
        //	This is my hash. There are many like it, but this one is mine.
        $this->_id = hash( 'sha256', spl_object_hash( $this ) . getmypid() . microtime( true ) );

        //	Auto-set tag and name if empty
        $this->_tag = $this->_tag ? : Inflector::neutralize( get_called_class() );
        $this->_name = $this->_name ? : $this->_tag;

        if ( $this instanceof SubscriberLike )
        {
            EventManager::discoverListeners( $this );
        }

        //	Publish after_construct event
        if ( $this->_enableLifeEvents )
        {
            $this->trigger( LifeEvents::AFTER_CONSTRUCT );
        }
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        //  Take a nap
        return array(
            'id'   => $this->_id,
            'name' => $this->_name,
            'tag'  => $this->_tag,
        );
    }

    /**
     * Choose your destructor!
     */
    public function __destruct()
    {
        try
        {
            //	Publish after_destruct event
            if ( $this->_enableLifeEvents )
            {
                $this->trigger( LifeEvents::BEFORE_DESTRUCT );
            }

            //	And clean up our listeners
            EventManager::removeDiscoveredListeners( $this );
        }
        catch ( \Exception $_ex )
        {
            //	Does nothing, like the goggles.,,
            //	Well, may stop those bogus frame 0 errors too...
        }

        $this->__sleep();
    }

    /**
     * Triggers an object event to all subscribers. Convenient wrapper on EM::publish
     *
     * @param string    $eventName
     * @param SeedEvent $event
     *
     * @return bool|int
     */
    public function trigger( $eventName, $event = null )
    {
        if ( null === $event )
        {
            $event = new SeedEvent( $this );
        }

        return EventManager::trigger( $eventName, $event );
    }

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName            The event to listen on
     * @param callable $listener             The listener
     * @param integer  $priority             The higher this value, the earlier an event
     *                                       listener will be triggered in the chain (defaults to 0)
     */
    public function on( $eventName, $listener, $priority = 0 )
    {
        EventManager::on( $eventName, $listener, $priority );
    }

    /**
     * Turn off/unbind/remove $listener from an event
     *
     * @param string   $eventName
     * @param callable $listener
     *
     * @return void
     */
    public function off( $eventName, $listener )
    {
        EventManager::off( $eventName, $listener );
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param string $name
     *
     * @return Seed
     */
    public function setName( $name )
    {
        $this->_name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $tag
     *
     * @return Seed
     */
    public function setTag( $tag )
    {
        $this->_tag = $tag;

        return $this;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->_tag;
    }

    /**
     * @param boolean $enableLifeEvents
     *
     * @return Seed
     */
    public function setEnableLifeEvents( $enableLifeEvents )
    {
        $this->_enableLifeEvents = $enableLifeEvents;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getEnableLifeEvents()
    {
        return $this->_enableLifeEvents;
    }

}
