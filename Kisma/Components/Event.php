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
 * @since			v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Components
 */
namespace Kisma\Components
{
	//*************************************************************************
	//* Imports
	//*************************************************************************

	use \Kisma\Utility as Utility;

	/**
	 * Event
	 * The mother of all events!
	 */
	class Event extends SubComponent implements \Kisma\IEvent
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var string The event name
		 */
		protected $_eventName = null;
		/**
		 * @var string Any extra event data
		 */
		protected $_eventData = null;
		/**
		 * @var boolean While true, propagation of events will continue. Set to false
		 * in an event handler then return true to successfully halt propagation.
		 */
		protected $_continuePropagation = true;
		/**
		 * @var \Kisma\Components\EventHandler[] The store of event handlers for this event.
		 */
		protected $_handlers = array();
		/**
		 * @var \Kisma\Components\Component The source of the event
		 */
		protected $_source = null;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * Walks the event handler chain calling the optional callback upon completion.
		 * Callback signature is:
		 * <pre>
		 * callback_function( Event $event, boolean $result )
		 * </pre>
		 *
		 * @param mixed $eventData
		 * @param \Kisma\Components\EventHandler $callback
		 * @return bool
		 */
		public function fire( $eventData = null, $callback = null )
		{
			$_result = true;

			//	Allow no data, just a callback.
			if ( null == $callback && null !== $eventData && is_callable( $eventData ) )
			{
				$callback = $eventData;
				$eventData = null;
			}

			//	Reset the event
			$this->_continuePropagation = true;

			//	Loop through the handlers for this event, passing data
			foreach ( $this->_handlers as $_handler )
			{
				//	Call each handler
				$_result = $_handler->handle( $this );

				//	If an event returns false, or wants to stop, break...
				if ( false === $this->_continuePropagation || false === $_result )
				{
					break;
				}
			}

			//	Ping the callback with the event data
			if ( null !== $callback )
			{
				//	Sending some info to the callback...
				$_result = call_user_func_array(
					$callback,
					array(
						$this,
					)
				);
			}

			//	We made it through, so return true
			return $_result;
		}

		/**************************************************************************
		 ** Handler Logic
		 **************************************************************************/

		/**
		 * @param callback $callback
		 * @param mixed|null $callbackData
		 * @return \Kisma\Components\Event
		 */
		public function addHandler( $callback, $callbackData = null )
		{
			if ( !is_callable( $callback ) )
			{
				throw new \Kisma\InvalidEventHandlerException( 'Callback must actually be callable. Check out Closures. Try again...' );
			}

			$_handler = new EventHandler(
				array(
					'callback' => $callback,
					'callbackData' => $callbackData,
				)
			);

			$this->_handlers[$_handler->getHandlerId()] = $_handler;
			Utility\Log::trace( 'ComponentEvent "' . $this->_eventName . '" bound with handler ID: ' . $_handler->getHandlerId() );
			
			return $this;
		}

		/**
		 * Remove the specified handler from this event
		 * @param callback $callback
		 * @return bool
		 */
		public function removeHandler( $callback )
		{
			//	Add to our handler store
			$_handlerId = spl_object_hash( (object)$callback );

			if ( isset( $this->_handlers, $this->_handlers[$_handlerId] ) )
			{
				unset( $this->_handlers[$_handlerId] );
				if ( empty( $this->_handlers ) )
				{
					$this->_handlers = array();
				}

				return true;
			}

			//	Not found :(
			return false;
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param boolean $continuePropagation
		 * @return \Kisma\Components\Event
		 */
		public function setContinuePropagation( $continuePropagation )
		{
			$this->_continuePropagation = $continuePropagation;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getContinuePropagation()
		{
			return $this->_continuePropagation;
		}

		/**
		 * @param $eventData
		 * @param mixed $eventData
		 * @return \Kisma\Components\Event
		 */
		public function setEventData( $eventData )
		{
			$this->_eventData = $eventData;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getEventData()
		{
			return $this->_eventData;
		}

		/**
		 * @param callback[] $handlers
		 * @return \Kisma\Components\Event
		 */
		protected function _setHandlers( $handlers )
		{
			$this->_handlers = $handlers;
			return $this;
		}

		/**
		 * @return callback[]
		 */
		public function getHandlers()
		{
			return $this->_handlers;
		}

		/**
		 * @param string $eventName
		 * @return \Kisma\Components\Event
		 */
		public function setEventName( $eventName )
		{
			$this->_eventName = $eventName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getEventName()
		{
			return $this->_eventName;
		}

		/**
		 * @param $source
		 * @param \Kisma\Components\Component $source
		 * @return \Kisma\Components\Event
		 */
		public function setSource( $source )
		{
			$this->_source = $source;
			return $this;
		}

		/**
		 * @return \Kisma\Components\Component
		 */
		public function getSource()
		{
			return $this->_source;
		}

	}
}