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
 * @category		Kisma_Events
 * @package			kisma.events
 * @namespace		\Kisma\Events
 * @since			v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Components
 */
namespace Kisma\Components;

/**
 * Event
 * The mother of all events!
 */
class Event extends Component implements \Kisma\IEvent
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************
	
	/**
	 * @var string The event ID
	 */
	protected $_eventId = null;
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
	 * @var array The store of event handlers for this event.
	 */
	protected $_handlers = array();
	/**
	 * @var \Kisma\IKisma The source of the event
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
	 * @param mixed $eventData
	 * @param callback $callback
	 * @return bool
	 */
	public function fireEvent( $eventData = null, $callback = null )
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
			/** @var $_callback callback */
			$_callback = $_handler[0];
			$_callbackEventData = $_handler[1];
			$_result = call_user_func_array( $_callback, array( $this, $_callbackEventData ) );

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
			call_user_func_array( $callback, array( $this, $_result ) );
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
			throw new InvalidEventHandlerException( 'Callback must actually be callable. Check out Closures. Try again...' );
		}

		//	Add to our handler store
		$this->_handlers[] = array(
			$callback,
			$callbackData,
		);

		\Kisma\Kisma::logDebug( 'Added event handler for "' . get_class( $this->_source ) . '"."' . $this->_eventId . '"' );

		return $this;
	}

	/**
	 * Remove the specified handler from this event
	 * @param callback $callback
	 * @return bool
	 */
	public function removeHandler( $callback )
	{
		if ( !is_callable( $callback ) )
		{
			throw new InvalidEventHandlerException( 'Callback must actually be callable. Check out Closures. Try again...' );
		}

		foreach ( $this->_handlers as $_index => $_handler )
		{
			if ( $callback == $_handler[0] )
			{
				unset( $this->_handlers[$_index]);
				return true;
			}
		}

		//	Not found :(
		return false;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param $continuePropagation
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
	 * @param $handlers
	 * @param \SplObjectStorage $handlers
	 * @return \Kisma\Components\Event
	 */
	protected function _setHandlers( $handlers )
	{
		$this->_handlers = $handlers;
		return $this;
	}

	/**
	 * @return \SplObjectStorage
	 */
	public function getHandlers()
	{
		return $this->_handlers;
	}

	/**
	 * @param string $eventId
	 * @return \Kisma\Components\Event
	 */
	public function setEventId( $eventId )
	{
		$this->_eventId = $eventId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEventId()
	{
		return $this->_eventId;
	}

	/**
	 * @param $source
	 * @param \Kisma\IKisma $source
	 * @return \Kisma\Components\Event
	 */
	public function setSource( $source )
	{
		$this->_source = $source;
		return $this;
	}

	/**
	 * @return \Kisma\IKisma
	 */
	public function getSource()
	{
		return $this->_source;
	}

}