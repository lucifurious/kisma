<?php
/**
 * Service.php
 */
namespace Kisma\Core\Interfaces;
/**
 * Service
 * All Services have this
 */
interface Service extends \Kisma\Core\Interfaces\Events\Service
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Initialize the service. Called after service construction
	 *
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function initializeService( $event );

	/**
	 * Process are request for the services of this class
	 * Called after the service is initialized
	 *
	 * @param \Kisma\Core\Services\SeedRequest $request
	 *
	 * @return bool
	 */
	public function processRequest( $request );

}
