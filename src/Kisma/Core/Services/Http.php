<?php
/**
 * Http.php
 *
 * @description Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * @copyright   Copyright (c) 2009-2012 Jerry Ablan
 * @license     http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author      Jerry Ablan <get.kisma@gmail.com>
 */
namespace Kisma\Core\Services;

/**
 * Http
 * An HTTP service base class
 *
 * Provides one event handler:
 *
 * onRequestReceived
 * the service is run, respectively.
 *
 * @property string $request The $_REQUEST currently being serviced
 */
abstract class Http extends \Kisma\Core\Service implements \Kisma\Core\Interfaces\Services\HttpEvents, \Kisma\Core\Interfaces\HttpMethod
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var array The $_REQUEST currently being serviced
	 */
	protected $_request = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 * Triggers the Request Received event
	 *
	 * @param array $options Set 'request' to override the inbound $_REQUEST
	 *
	 * @return bool True if it's all good, false if not: Not a web process or the event handler returned false
	 */
	public function initialize( $options = array() )
	{
		if ( 'cli' == PHP_SAPI )
		{
			return false;
		}

		//	Trigger the event
		return $this->trigger(
			self::RequestReceived,
			$this->_request = \Kisma\Utility\Option::get( $options, 'request', $_REQUEST )
		);
	}

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onRequestReceived( $event )
	{
		//	Default implementation
		return true;
	}

	//********************************************************************************
	//* Property Accessors
	//********************************************************************************

	/**
	 * @param array $request
	 *
	 * @return \Kisma\Core\Services\Http
	 */
	public function setRequest( $request )
	{
		$this->_request = $request;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getRequest()
	{
		return $this->_request;
	}

}
