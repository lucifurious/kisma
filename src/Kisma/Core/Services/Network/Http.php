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
abstract class Http extends \Kisma\Core\Services\SeedService implements \Kisma\Core\Interfaces\HttpMethod, \Kisma\Core\Interfaces\Events\Http
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function initialize( $consumer = null, $request = null )
	{
		if ( !parent::initialize( $consumer, $request ) )
		{
			return false;
		}

		if ( null === $this->_request )
		{
			$this->_request = new HttpRequest();
		}

		//	Trigger the event
		return
			$this->publish(
				self::RequestReceived,
				$this->_request
			);
	}

}
