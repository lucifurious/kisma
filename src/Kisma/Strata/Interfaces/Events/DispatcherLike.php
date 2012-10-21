<?php
/**
 * DispatcherLike.php
 */
namespace Kisma\Strata\Interfaces\Events;
/**
 * DispatcherLike
 * Events thrown by DispatcherLike things
 */
interface DispatcherLike extends \Kisma\Core\Interfaces\Seed
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const PreProcess = 'kisma.strata.dispatcher_like.pre_process';
	/**
	 * @var string
	 */
	const PostProcess = 'kisma.strata.dispatcher_like.post_process';
}
