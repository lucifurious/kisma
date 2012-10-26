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
	 * @param \Kisma\Core\Interfaces\RequestLike $request
	 *
	 * @return mixed
	 */
	public function process( \Kisma\Core\Interfaces\RequestLike &$request );
}
