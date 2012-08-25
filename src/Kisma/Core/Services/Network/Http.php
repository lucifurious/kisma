<?php
/**
 * Http.php
 */
namespace Kisma\Core\Services\Network;

use Kisma\Core\Interfaces;

/**
 * Http
 * An HTTP service base class
 *
 * Provides one event handler:
 *
 * onRequestReceived
 * Happens when the service is run, respectively.
 */
class Http extends \Kisma\Core\Services\SeedService implements \Kisma\Core\Interfaces\HttpMethod, \Kisma\Core\Interfaces\Events\Http
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var HttpRequest The current request
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

		//	Trigger the event
		return
			$this->publish(
				self::RequestReceived,
				$this->_request = new HttpRequest()
			);
	}

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Events\ServiceEvent $event
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
	 * @param HttpRequest $request
	 *
	 * @return Http
	 */
	public function setRequest( $request )
	{
		$this->_request = $request;

		return $this;
	}

	/**
	 * @return HttpRequest
	 */
	public function getRequest()
	{
		return $this->_request;
	}

}
