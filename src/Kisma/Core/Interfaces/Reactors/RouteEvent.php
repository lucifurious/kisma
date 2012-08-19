<?php
/**
 * RouteEvent.php
 */
namespace Kisma\Core\Interfaces\Reactors;

/**
 * Events for request routers
 */
interface RouteEvent extends \Kisma\Core\Interfaces\Reactor
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const BeforeRouting = 'kisma.route.before_routing';
	/**
	 * @var string
	 */
	const AfterRoutine = 'kisma.route.after_routing';

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

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
