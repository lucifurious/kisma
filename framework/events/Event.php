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
 * @namespace Kisma\Events Kisma events
 */
namespace Kisma\Events;
use Kisma\Components\Component;

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
	 * @var mixed Data pertinent to the event
	 */
	protected $_data = null;
	/**
	 * @var bool Indicates that the event has been processed
	 */
	protected $_handled = false;
	/**
	 * @var \Kisma\IKisma The source of the event
	 */
	protected $_source = null;
	/**
	 * @var array The list of event handlers for this event.
	 */
	protected $_handlers = array();

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Trigger the event
	 * @return bool
	 */
	public function trigger()
	{
		//	Loop through the handlers for this event, passing data
		foreach ( $this->_handlers as $_handler )
		{
			//	Call each handler
			$_result = call_user_func( $_handler, $this );

			//	If an event returns false, stop propagation unsuccessfully
			if ( false === $_result )
			{
				return false;
			}

			//	Stop propagation successfully
			if ( true === $this->_handled )
			{
				break;
			}
		}

		//	We made it through, so return true
		return true;
	}

	/**
	 * @param callback $callback
	 * @return \Kisma\Events\Event
	 */
	public function addHandler( $callback )
	{
		if ( !is_callable( $callback ) )
		{
			throw new InvalidEventHandler( 'The event handler specified is not callable.' );
		}

		$this->_handlers[] = $callback;
		return $this;
	}

	/**
	 * Remove the specified handler from this event
	 * @param callback $callback
	 * @return bool
	 */
	public function removeHandler( $callback )
	{
		foreach ( $this->_handlers as $_index => $_handler )
		{
			if ( $callback == $_handler )
			{
				unset( $this->_handlers[$_index] );
				return true;
			}
		}

		return false;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param mixed $data
	 * @return \Kisma\Components\Event $this
	 */
	public function setData( $data )
	{
		$this->_data = $data;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * @param boolean $handled
	 * @return \Kisma\Components\Event $this
	 */
	public function setHandled( $handled )
	{
		$this->_handled = $handled;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getHandled()
	{
		return $this->_handled;
	}

	/**
	 * @param \Kisma\IKisma $source
	 * @return \Kisma\Components\Event $this
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

	/**
	 * @param array $handlers
	 * @return $this
	 */
	public function setHandlers( $handlers = array() )
	{
		$this->_handlers = $handlers;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getHandlers()
	{
		return $this->_handlers;
	}

}