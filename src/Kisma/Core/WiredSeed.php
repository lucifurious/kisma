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

use Kisma\Core\Interfaces\Events\Enums\LifeEvents;
use Kisma\Core\Interfaces\PublisherLike;
use Kisma\Core\Interfaces\SubscriberLike;
use Kisma\Core\Utility\EventManager;
use Kisma\Core\Utility\Inflector;
use Kisma\Core\Utility\Option;
use Symfony\Component\HttpFoundation\Request;

/**
 * WiredSeed provides event dispatching via the Symfony Event Dispatcher library.
 *
 * Events
 * ======
 * An event is defined by the presence of a method whose name starts with 'on'.
 * The event name is the method name. When an event is triggered, event handlers attached
 * to the event will be invoked automatically.
 *
 * An event is fired by calling the object's {@link trigger} method. Attached event
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
 * Unless otherwise specified, the object will automatically search for and
 * attach any event handlers that exist in your object.
 *
 * To disable this feature, set $discoverEvents to false before calling the parent constructor.
 */
class WiredSeed extends Seed implements PublisherLike, SubscriberLike
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

	//********************************************************************************
	//* Methods
	//********************************************************************************

	/**
	 * When unserializing an object, this will re-attach any event handlers...
	 */
	public function __wakeup()
	{
		parent::__wakeup();

		//	Discover events and send after_construct event
		EventManager::discoverListeners( $this );

		$this->trigger( LifeEvents::AFTER_CONSTRUCT );
	}

	/**
	 * Choose your destructor!
	 */
	public function __destruct()
	{
		try
		{
			//	Publish after_destruct event
			$this->trigger( LifeEvents::BEFORE_DESTRUCT );
		}
		catch ( \Exception $_ex )
		{
			//	Does nothing, like the goggles.,,
			//	Well, may stop those bogus frame 0 errors too...
		}

		parent::__destruct();
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
	 * @param string $eventName
	 * @param array  $keys
	 *
	 * @return string
	 */
	protected function _normalizeEventName( $eventName, $keys = null )
	{
		static $_cache = array();

		$_tagParts = explode( '.', $_tag = Inflector::neutralize( $eventName ) );

		if ( null !== ( $_name = Option::get( $_cache, $_tag ) ) )
		{
			return $_name;
		}

		$_replacements = array_merge(
			get_class_vars( 'Symfony\\Component\\HttpFoundation\\Request' ),
			Option::clean( $keys )
		);

		foreach ( $_replacements as $_key )
		{
			foreach ( $_tagParts as &$_part )
			{
				if ( preg_match( "/^{(.*)+}$", $_part ) )
				{
					$_part = str_ireplace(
						'{' . $_key . '}',
						Option::get( $_request, $_key ),
						$_part
					);
				}
			}
		}

		return $_cache[$eventName] = implode( '.', $_tagParts );
	}
}