<?php
/**
 * DeliveryService.php
 */
namespace Kisma\Core\Services;
/**
 * DeliveryService
 * Base for classes that deliver things
 */
abstract class DeliveryService extends SeedService
{
	//*************************************************************************
	//* Methods
	//*************************************************************************
	/**
	 * Process a request for the services of this class
	 * Called after the service is initialized
	 *
	 * @param \Kisma\Core\Interfaces\RequestLike $request
	 *
	 * @return bool
	 */
	public function process( $request )
	{
		return $this->deliver( $request, $this->getConsumer() );
	}

	/**
	 * Process the request (i.e. deliver payload)
	 *
	 * @param \Kisma\Core\Interfaces\RequestLike  $payload
	 * @param \Kisma\Core\Interfaces\ConsumerLike $consumer
	 *
	 * @return \Kisma\Core\Interfaces\ResponseLike
	 */
	abstract public function deliver( $payload, $consumer );
}
