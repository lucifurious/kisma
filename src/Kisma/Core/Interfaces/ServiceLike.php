<?php
/**
 * ServiceLike.php
 */
namespace Kisma\Core\Interfaces;
/**
 * ServiceLike
 * Acts like a service
 */
interface ServiceLike extends \Kisma\Core\Interfaces\Events\ServiceLike
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Perform the service requested
	 *
	 * @return mixed
	 */
	public function perform();
}
