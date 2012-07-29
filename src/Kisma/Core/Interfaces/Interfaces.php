<?php
/**
 * Interfaces.php
 */
namespace Kisma\Core\Interfaces;

/**
 * Defines an object as a 3rd-party API consumer
 */
interface ApiConsumer
{
	//	Nada
}

/**
 * Defines some handy time/date constants
 */
interface Time
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int circa 01/01/1980
	 */
	const Beginning = 315550800;
	/**
	 * @var int circa 01/01/2038
	 */
	const End = 2145934800;
}
