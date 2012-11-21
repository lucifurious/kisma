<?php
/**
 * AccessLevels.php
 */
namespace Kisma\Core\Interfaces;
/**
 * AccessLevels
 * Generic access levels, matching with Yii
 */
interface AccessLevels
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const Any = '*';
	/**
	 * @var string
	 */
	const Guest = '?';
	/**
	 * @var string
	 */
	const Authenticated = '@';

}
