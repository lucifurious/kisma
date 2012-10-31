<?php
/**
 * ServiceLike.php
 */
namespace Kisma\Core\Interfaces;
/**
 * ServiceLike
 * Acts like a service
 */
interface ServiceLike extends \Kisma\Core\Interfaces\Events\ServiceLike
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Initialize the service. Called automatically
	 *
	 * @param \Kisma\Core\Interfaces\ConsumerLike $consumer
	 * @param \Kisma\Core\Interfaces\RequestLike  $request
	 *
	 * @return bool
	 */
	public function initialize( \Kisma\Core\Interfaces\ConsumerLike $consumer, $request = null );

	/**
	 * Process a request for the services of this class
	 * Called after the service is initialized
	 *
	 * @param \Kisma\Core\Interfaces\RequestLike $request
	 *
	 * @return \Kisma\Core\Interfaces\ResponseLike
	 */
	public function process( \Kisma\Core\Interfaces\RequestLike $request = null );

}
