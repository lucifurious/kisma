<?php
/**
 * RouteLike.php
 */
namespace Kisma\Strata\Interfaces;
/**
 * RouteLike
 */
interface RouteLike extends \Kisma\Strata\Interfaces\Events\RouteLike
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param \Kisma\Strata\Interfaces\RequestLike $request
	 *
	 * @return mixed
	 */
	public function processRequest( \Kisma\Strata\Interfaces\RequestLike $request );
}
