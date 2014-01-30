<?php
namespace Kisma\Core\Enums;

/**
 * Verbosity
 * System-wide logging verbosity levels
 */
class Verbosity extends SeedEnum
{
	//*************************************************************************
	//	Constants
	//*************************************************************************

	/**
	 * @const int Set via "-q|--quiet"
	 */
	const QUIET = 0;
	/**
	 * @const int Default verbosity
	 */
	const NORMAL = 1;
	/**
	 * @const int Set via "-v|--verbose"
	 */
	const VERBOSE = 2;
	/**
	 * @const int Set via "-vv"
	 */
	const VERY_VERBOSE = 3;
	/**
	 * @const int Set via "-vvv"
	 */
	const DEBUG = 4;
}
