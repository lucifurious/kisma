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
	 * Returns the unique identifier for this subscriber
	 *
	 * @return string
	 */
	public function getId();

	/**
	 * Returns a key-worthy tag for your object
	 *
	 * @return string
	 */
	public function getTag();

}
