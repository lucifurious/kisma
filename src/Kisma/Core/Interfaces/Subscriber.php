<?php
/**
 * Subscriber.php
 */
namespace Kisma\Core\Interfaces;
/**
 * Subscriber
 * Defines an object as being able to subscribe to events
 */
interface Subscriber
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Returns the unique identifier for this reactor
	 *
	 * @return string
	 */
	public function getId();

}
