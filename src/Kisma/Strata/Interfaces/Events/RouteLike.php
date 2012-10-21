<?php
/**
 * RouteLike.php
 */
namespace Kisma\Strata\Interfaces\Events;
/**
 * RouteLike
 * Events thrown by RouteLike things
 */
interface RouteLike extends \Kisma\Core\Interfaces\Seed
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const PreProcess = 'kisma.strata.route_like.pre_process';
	/**
	 * @var string
	 */
	const PostProcess = 'kisma.strata.route_like.post_process';
}
