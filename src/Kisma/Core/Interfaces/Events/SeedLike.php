<?php
/**
 * SeedLike.php
 */
namespace Kisma\Core\Interfaces\Events;

/**
 * SeedLike
 * Defines the event interface for all seeds
 */
interface SeedLike
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const AfterConstruct = 'kisma.core.seed_like.after_construct';
	/**
	 * @var string
	 */
	const BeforeDestruct = 'kisma.core.seed_like.before_destruct';

}
