<?php
/**
 * KismaEvent.php
 */
namespace Kisma\Core\Interfaces\Reactors;

/**
 * KismaEvent
 * The Kisma application-level events
 */
interface KismaEvent extends \Kisma\Core\Interfaces\Reactor
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const Birth = 'kisma.birth';
	/**
	 * @var string
	 */
	const Death = 'kisma.death';

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onBirth( $event = null );

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onDeath( $event = null );

}
