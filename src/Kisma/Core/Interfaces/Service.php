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
	 * Initialize the service. Called automatically
	 *
	 * @param mixed $options
	 *
	 * @return bool
	 */
	public function initialize( $options = array() );

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
