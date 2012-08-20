<?php
/**
 * Seed.php
 */
namespace Kisma\Core\Interfaces\Events;

/**
 * Seed
 * Defines the event interface for all seeds
 */
interface Seed
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const AfterConstruct = 'kisma.core.seed.after_construct';
	/**
	 * @var string
	 */
	const BeforeDestruct = 'kisma.core.seed.before_destruct';

}
