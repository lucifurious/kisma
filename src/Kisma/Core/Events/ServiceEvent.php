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

	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * Pushes the request on to the end of the queue
	 *
	 * @param \Kisma\Core\Services\SeedRequest $request
	 *
	 * @return int The NEW number of requests in the queue
	 */
	public function pushRequest( $request )
	{
		if ( !is_array( $this->_requestQueue ) || empty( $this->_requestQueue ) )
		{
			$this->_requestQueue = array();
		}

		return array_push( $this->_requestQueue, $request );
	}

	/**
	 * Pop the request off of the end of the queue
	 *
	 * @return \Kisma\Core\Services\SeedRequest Returns null if the queue is empty
	 */
	public function popRequest()
	{
		if ( !is_array( $this->_requestQueue ) || empty( $this->_requestQueue ) )
		{
			$this->_requestQueue = array();
		}

		return array_pop( $this->_requestQueue );
	}

	//**************************************************************************
	//* Properties
	//**************************************************************************

	/**
	 * @param array|\Kisma\Core\Services\SeedRequest[] $requestQueue
	 *
	 * @return \Kisma\Core\Events\ServiceEvent
	 */
	public function setRequestQueue( $requestQueue )
	{
		if ( !is_array( $requestQueue ) || empty( $requestQueue ) )
		{
			$requestQueue = array();
		}

		$this->_requestQueue = $requestQueue;

		return $this;
	}

	/**
	 * @return array|\Kisma\Core\Services\SeedRequest[]
	 */
	public function getRequestQueue()
	{
		return $this->_requestQueue;
	}

}
