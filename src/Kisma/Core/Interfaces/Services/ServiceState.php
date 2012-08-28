<?php
/**
 * ServiceState.php
 */
namespace Kisma\Core\Interfaces\Services;
/**
 * ServiceState
 * Defines the various states in which a service can exist
 */
interface ServiceState
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var bool The beginning state
	 */
	const Uninitialized = false;
	/**
	 * @var bool After initialization
	 */
	const Initialized = true;
	/**
	 * @var int
	 */
	const Running = 1;
	/**
	 * @var int
	 */
	const Completed = 2;

}
