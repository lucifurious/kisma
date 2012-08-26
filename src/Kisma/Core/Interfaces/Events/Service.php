<?php
/**
 * Service.php
 */
namespace Kisma\Core\Interfaces\Events;

/**
 * Service
 * Defines the event interface for all services
 */
interface Service
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string Published by dispatchers
	 */
	const Dispatch = 'kisma.core.service.dispatch';
	/**
	 * @var string Fired when the service call succeeded
	 */
	const Success = 'kisma.core.service.success';
	/**
	 * @var string Fired if there was a failure in the service call
	 */
	const Failure = 'kisma.core.service.failure';

}
