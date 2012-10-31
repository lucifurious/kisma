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
	 * {@InheritDoc}
	 */
	public function process( \Kisma\Core\Interfaces\RequestLike $request = null )
	{
		return $this->deliver( $request, $this->getConsumer() );
	}

	/**
	 * Process the request (i.e. deliver payload)
	 *
	 * @param \Kisma\Core\Interfaces\RequestLike  $payload
	 *
	 * @return \Kisma\Core\Interfaces\ResponseLike
	 */
	abstract public function deliver( $payload );
}
