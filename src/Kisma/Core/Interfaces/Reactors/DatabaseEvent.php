<?php
/**
 * DatabaseEvent.php
 */
namespace Kisma\Core\Interfaces\Reactors;

/**
 * DatabaseEvent
 * Defines an interface for database service events
 */
interface DatabaseEvent extends \Kisma\Core\Interfaces\Reactor
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const BeforeFind = 'kisma.core.database.before_find';
	/**
	 * @var string
	 */
	const AfterFind = 'kisma.core.database.after_find';
	/**
	 * @var string
	 */
	const BeforeSave = 'kisma.core.database.before_save';
	/**
	 * @var string
	 */
	const AfterSave = 'kisma.core.database.after_save';
	/**
	 * @var string
	 */
	const BeforeDelete = 'kisma.core.database.before_delete';
	/**
	 * @var string
	 */
	const AfterDelete = 'kisma.core.database.after_delete';

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeFind( $event = null );

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onAfterFind( $event = null );

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeFindSave( $event = null );

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onAfterSave( $event = null );

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeDelete( $event = null );

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onAfterDelete( $event = null );

}
