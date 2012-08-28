<?php
/**
 * ServiceEvent.php
 */
namespace Kisma\Core\Events;
/**
 * ServiceEvent
 * An event that is consumed by a service.
 *
 * Events can have multiple service requests lurking inside. This class implements
 * a simple FIFO request queue.
 *
 * Add to the queue like so:
 *
 *     $_doSomethingCool = new ServiceRequest();
 *     $_doSomethingCooler = new ServiceRequest();
 *     $_doSomethingElse = new ServiceRequest();
 *     ...
 *     $_event = new ServiceEvent($this);
 *     $_event->pushRequest( $_doSomethingCool );
 *     $_event->pushRequest( $_doSomethingCooler );
 *     $_event->pushRequest( $_doSomethingElse );
 *
 *     $this->publish( 'my.cool.event', $_event );
 *
 * In your event handler simply pop the requests off the stack:
 *
 *     //    Loop through the requests in queue
 *     while ( null !== ( $_request = $event->popRequest() ) )
 *     {
 *         //    Handle a single request here
 *     }
 *
 */
class ServiceEvent extends SeedEvent
{
	//**************************************************************************
	//* Private Members
	//**************************************************************************

	/**
	 * @var \Kisma\Core\Services\SeedRequest[] One or more requests for this service
	 */
	protected $_requestQueue = array();
	/**
	 * @var array
	 */
	protected $_responses = array();

	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * @param \Kisma\Core\Interfaces\Consumer  $source
	 * @param \Kisma\Core\Services\SeedRequest $request
	 */
	public function __construct( $source, \Kisma\Core\Services\SeedRequest $request )
	{
		parent::__construct( $source, $request );

		$this->pushRequest( $request );
	}

	/**
	 * Pushes the request on to the end of the queue
	 *
	 * @param \Kisma\Core\Services\SeedRequest $request
	 *
	 * @return int The NEW number of requests in the queue
	 */
	public function pushRequest( $request )
	{
		$this->_requestQueue = \Kisma\Core\Utility\Option::clean( $this->_requestQueue );
		$this->_responses = \Kisma\Core\Utility\Option::clean( $this->_responses );

		//	Set up the response entry for this
		$this->_responses[$request->getId()] = null;

		return array_push( $this->_requestQueue, $request );
	}

	/**
	 * Pop the request off of the end of the queue
	 *
	 * @return \Kisma\Core\Services\SeedRequest Returns null if the queue is empty
	 */
	public function popRequest()
	{
		$this->_requestQueue = \Kisma\Core\Utility\Option::clean( $this->_requestQueue );

		return array_pop( $this->_requestQueue );
	}

	/**
	 * Make "$request" look like a property
	 *
	 * @return \Kisma\Core\Services\SeedRequest
	 */
	public function getRequest()
	{
		return $this->popRequest();
	}

	/**
	 * @param string $requestId
	 * @param mixed  $result
	 *
	 * @return \Kisma\Core\Events\ServiceEvent
	 */
	public function setRequestResult( $requestId, $result = true )
	{
		//	Stuff the response, who cares....
		\Kisma\Core\Utility\Option::set( $this->_responses, $requestId, $result );

		return $this;
	}

	/**
	 * @param string $requestId
	 * @param mixed  $defaultResult
	 *
	 * @return mixed
	 */
	public function getRequestResult( $requestId, $defaultResult = null )
	{
		return \Kisma\Core\Utility\Option::get( $this->_responses, $requestId, $defaultResult );
	}

	//**************************************************************************
	//* Properties
	//**************************************************************************

	/**
	 * @return array|\Kisma\Core\Services\SeedRequest[]
	 */
	public function getRequestQueue()
	{
		return $this->_requestQueue;
	}

	/**
	 * @return array
	 */
	public function getResponses()
	{
		return $this->_responses;
	}

}
