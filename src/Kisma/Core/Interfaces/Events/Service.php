<?php
/**
 * SeedService.php
 */
namespace Kisma\Core\Interfaces\Events;

/**
 * SeedService
 * Defines the event interface for all services
 */
interface SeedService
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string Fired before a service call is made
	 */
	const BeforeServiceCall = 'kisma.core.service.before_service_call';
	/**
	 * @var string Fired after the service call has completed
	 */
	const AfterServiceCall = 'kisma.core.service.after_service_call';
	/**
	 * @var string Fired when the service call succeeded
	 */
	const Success = 'kisma.core.service.success';
	/**
	 * @var string Fired if there was a failure in the service call
	 */
	const Failure = 'kisma.core.service.failure';

}
