<?php
/**
 * ServiceEvents.php
 *
 * @filesource
 */
namespace Kisma\Core\Interfaces;

/**
 * ServiceEvents
 * Defines an interface a base service class must support
 */
interface ServiceEvents
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

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeServiceCall( $event = null );

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onAfterServiceCall( $event = null );

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onSuccess( $event = null );

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onFailure( $event = null );

}
