<?php
/**
 * ServiceLike.php
 */
namespace Kisma\Core\Interfaces\Events;
/**
 * ServiceLike
 * Defines the event interface for all services
 */
interface ServiceLike
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string Fired before service call
	 */
	const BeforePerform = 'kisma.core.service_like.before_perform';
	/**
	 * @var string Fired when the service call succeeded
	 */
	const Success = 'kisma.core.service_like.success';
	/**
	 * @var string Fired if there was a failure in the service call
	 */
	const Failure = 'kisma.core.service_like.failure';
	/**
	 * @var string Fired after service call
	 */
	const AfterPerform = 'kisma.core.service_like.after_perform';
	/**
	 * @var string Fired when processing is complete
	 */
	const Complete = 'kisma.core.service_like.complete';

}
