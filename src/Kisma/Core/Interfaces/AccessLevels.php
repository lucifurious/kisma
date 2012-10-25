<?php
/**
 * AccessLevels.php
 */
namespace Kisma\Core\Interfaces;
/**
 * AccessLevels
 * Generic access levels
 */
interface AccessLevels
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const None = -1;
	/**
	 * @var int
	 */
	const Guest = 0;
	/**
	 * @var int
	 */
	const UnconfirmedUser = 1;
	/**
	 * @var int
	 */
	const ConfirmedUser = 2;
	/**
	 * @var int
	 */
	const AuthorizedUser = 3;
	/**
	 * @var intNo access
	 */
	const Admin = 4;

}
