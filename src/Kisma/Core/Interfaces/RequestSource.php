<?php
/**
 * RequestSource.php
 */
namespace Kisma\Core\Interfaces;
/**
 * RequestSource
 * The source of an inbound request
 */
interface RequestSource
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const Http = 0;
	/**
	 * @var int
	 */
	const Cli = 1;
	/**
	 * @var int
	 */
	const InternalHttp = 2;
	/**
	 * @var int
	 */
	const InternalCli = 3;
}
