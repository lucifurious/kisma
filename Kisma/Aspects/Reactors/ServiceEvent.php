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
 * @category		Kisma_Components
 * @package			kisma.components
 * @namespace		\Kisma\Components
 * @since			v1.0.0
 * @filesource
 */
namespace Kisma\Aspects\Reactors
{
	/**
	 * ServiceEvent
	 * A base service event class
	 */
	class ServiceEvent extends \Kisma\Components\AspectService implements \Kisma\IDispatcher
	{
		/**
		 * Bind a callback to an event
		 * @param string $eventName
		 * @param callback $callback
		 * @return boolean
		 */
		public function bind( $eventName, $callback )
		{
			// TODO: Implement bind() method.
		}

		/**
		 * Unbind from an event
		 *
		 * @param string $eventName
		 * @param callback $callback
		 * @return boolean
		 */
		public function unbind( $eventName, $callback )
		{
			// TODO: Implement unbind() method.
		}

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
			// TODO: Implement hasEvent() method.
		}

		/**
		 * Triggers an event
		 *
		 * @param string $eventName
		 * @param mixed|null $eventData
		 * @param callback|null $callback
		 * @return bool Returns true if the $eventName has no handlers.
		 */
		public function trigger( $eventName, $eventData = null, $callback = null )
		{
			// TODO: Implement trigger() method.
		}
	}
}