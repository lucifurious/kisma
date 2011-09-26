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
use Kisma\Kisma as K;

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
	 * @var \SplObjectStorage The store of event handlers for this event.
	 */
	protected $_handlers = array();
	/**
	 * @var string The event ID
	 */
	protected $_eventId = null;
	/**
	 * @var boolean While true, propagation of events will continue. Set to false
	 * in an event handler then return true to successfully halt propagation.
	 */
	protected $_continuePropagation = true;
	/**
	 * @var mixed Data pertinent to the event
	 */
	protected $_eventData = null;
	/**
	 * @var \Kisma\IKisma The source of the event
	 */
	protected $_source = null;
	
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
		parent::__construct( $options );
		$this->_handlers = new \SplObjectStorage();
	}

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
		$this->_eventData = $eventData;

		//	Loop through the handlers for this event, passing data
		/** @var $_handler callback */
		foreach ( $this->_handlers as $_index => $_handler )
		{
			//	Call each handler
			/** @var $_callback callback */
			/** @noinspection PhpUndefinedMethodInspection */
			$_callback = $_handler->getInfo();
			$_result = $_callback( $this );

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
			$callback(
				$this,
				$_result
			);
		}

		//	We made it through, so return true
		return $_result;
	}

	/**************************************************************************
	 ** Helpers
	 **************************************************************************/

	/**
	 * Helper to create a new event.
	 * @static
	 * @param string $eventId
	 * @param Component $source
	 * @param mixed|null $eventData
	 * @return \Kisma\Events\Event
	 */
	public static function create( $eventId, $source, $eventData = null )
	{
		$_class = get_called_class();

		$_eventOptions = array(
			'eventId' => $eventId,
			'eventData' => $eventData,
			'source' => $source,
		);

		K::logDebug( 'Event "' . $eventId . '" created for source "' . K::standardizeName( get_class( $source ) ) );

		return new $_class( $_eventOptions );
	}

	/**************************************************************************
	 ** Handler Logic
	 **************************************************************************/

	/**
	 * @param callback $callback
	 * @return \Kisma\Events\Event
	 */
	public function addHandler( $callback )
	{
		if ( !is_callable( $callback ) )
		{
			throw new InvalidEventHandlerException( 'Callback must actually be callable. Check out Closures. Try again...' );
		}

		//	Add to our handler store
		$_result = $this->_handlers->attach( $this, $callback );

		K::logDebug( 'Added event handler for "' . get_class( $this->_source ) . '"."' . $this->_eventId . '"' );

		return $this;
	}

	/**
	 * Remove the specified handler from this event
	 * @param callback $callback
	 * @return bool
	 */
	public function removeHandler( $callback )
	{
		$this->_handlers->detach( $callback );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param $continuePropagation
	 * @param boolean $continuePropagation
	 * @return \Kisma\Events\Event
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
	 * @return \Kisma\Events\Event
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
	 * @return \Kisma\Events\Event
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
	 * @return \Kisma\Events\Event
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
	 * @return \Kisma\Events\Event
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