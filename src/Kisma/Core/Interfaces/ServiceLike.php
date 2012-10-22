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
	 * @param mixed $options
	 *
	 * @return bool
	 */
	public function initializeService( $options = array() );

	/**
	 * Process a request for the services of this class
	 * Called after the service is initialized
	 *
	 * @param \Kisma\Core\Services\SeedRequest $request
	 *
	 * @return bool
	 */
	public function processRequest( $request );

}
