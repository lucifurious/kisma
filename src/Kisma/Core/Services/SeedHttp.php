<?php
/**
 * Http.php
 */
namespace Kisma\Core\Services;

use Kisma\Core\Interfaces;

/**
 * Http
 * An HTTP service base class
 *
 * Provides one event handler:
 *
 * onRequestReceived
 * the service is run, respectively.
 *
 * @property \Kisma\Core\Services\Request $request The request currently being serviced
 */
class SeedHttp extends \Kisma\Core\Service implements Interfaces\Events\Http, \Kisma\Core\Interfaces\HttpMethod
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Kisma\Core\Services\Request The current request
	 */
	protected $_request = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function initialize( $options = array() )
	{
		if ( !parent::initialize( $options ) )
		{
			return false;
		}

		$this->_request = new \Kisma\Core\Services\Request();

		//	Trigger the event
		return
			$this->publish(
				self::RequestReceived,
				$this->_request
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
	 * @param Request $request
	 *
	 * @return \Kisma\Core\Services\SeedHttp
	 */
	public function setRequest( $request )
	{
		$this->_request = $request;
		return $this;
	}

	/**
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->_request;
	}

}
