<?php
/**
 * ControlEvents.php
 */
namespace Kisma\Core\Interfaces;

/**
 * Events for request controllers
 */
interface ControlEvents
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const RequestReceived = 'kisma.service.control.request_received';
	/**
	 * @var string
	 */
	const BeforeRequestDispatch = 'kisma.service.control.before_request_dispatch';
	/**
	 * @var string
	 */
	const AfterRequestDispatch = 'kisma.service.control.after_request_dispatch';

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onRequestReceived( $event = null );

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeRequestDispatch( $event = null );

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onAfterRequestDispatch( $event = null );

}
