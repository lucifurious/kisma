<?php
/**
 * DeploymentZones.php
 */
namespace Kisma\Core\Enums;
/**
 * DeploymentZones
 */
class DeploymentZones extends SeedEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const Local = 0;
	/**
	 * @var int
	 */
	const Development = 1;
	/**
	 * @var int
	 */
	const Staging = 2;
	/**
	 * @var int
	 */
	const Production = 3;
	/**
	 * @var int
	 */
	const Unknown = -1;
}
