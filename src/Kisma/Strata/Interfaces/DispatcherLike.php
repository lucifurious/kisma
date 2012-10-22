<?php
/**
 * DispatcherLike.php
 */
namespace Kisma\Strata\Interfaces;
/**
 * DispatcherLike
 */
interface DispatcherLike extends \Kisma\Strata\Interfaces\Events\DispatcherLike
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Dispatch the inbound request to a route
	 *
	 * @param string       $tag
	 * @param mixed|null   $payload
	 *
	 * @return bool
	 */
	public function dispatchRequest( $tag, $payload = null );
}
