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
	 * @var int Via HTTP
	 */
	const Http = 0;
	/**
	 * @var int Via command line
	 */
	const Cli = 1;
	/**
	 * @var int Via internal procedure
	 */
	const Internal = 2;
	/**
	 * @var int HTTP request via internal procedure
	 */
	const InternalHttp = 2;
	/**
	 * @var int CLI request via internal procedure
	 */
	const InternalCli = 3;
}
