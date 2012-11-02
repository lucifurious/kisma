<?php
/**
 * DeploymentZoneNames.php
 */
namespace Kisma\Core\Enums;
/**
 * DeploymentZoneNames
 */
class DeploymentZoneNames extends SeedEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const Production = 'production';
	/**
	 * @var string
	 */
	const Development = 'development';
	/**
	 * @var string
	 */
	const Staging = 'staging';
	/**
	 * @var string
	 */
	const Local = 'local';
	/**
	 * @var string
	 */
	const Unknown = 'unknown';

}
