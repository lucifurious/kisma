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
	
}