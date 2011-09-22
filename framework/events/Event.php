<?php
/**
 * Kisma(tm) : PHP Microframework (http://github.com/lucifurious/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/lucifurious/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/lucifurious/kisma/ Kisma(tm)
 * @license			http://github.com/lucifurious/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @package			kisma
 * @namespace		\Kisma\Components
 * @since			v1.0.0
 * @filesource
 */
namespace Kisma\Events;

/**
 * Event
 * The base event class
 */
class Event extends \Kisma\Components\Component implements \Kisma\IEvent
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
	 * @var \Kisma\IKisma} The source of the event
	 */
	protected $_source = null;

	//*************************************************************************
	//* Default Methods
	//*************************************************************************

	/**
	 * Constructor.
	 * @param \Kisma\IKisma $source The source of the event
	 * @param mixed $data Optional event data
	 */
	public function __construct( $source = null, $data = null )
	{
		parent::__construct(
			array
			(
				'source' => $source,
				'data' => $data,
			)
		);
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
}