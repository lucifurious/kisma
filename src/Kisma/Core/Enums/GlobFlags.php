<?php
/**
 * GlobFlags.php
 */
namespace Kisma\Core\Enums;
/**
 * GlobFlags
 * Ya know, for globbing...
 */
class GlobFlags extends SeedEnum implements \Kisma\Core\Interfaces\GlobFlags
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const GLOB_NODIR = 0x0100;
	/**
	 * @var int
	 */
	const GLOB_PATH = 0x0200;
	/**
	 * @var int
	 */
	const GLOB_NODOTS = 0x0400;
	/**
	 * @var int
	 */
	const GLOB_RECURSE = 0x0800;
	/**
	 * @var int
	 */
	const NoDir = 0x0100;
	/**
	 * @var int
	 */
	const Path = 0x0200;
	/**
	 * @var int
	 */
	const NoDots = 0x0400;
	/**
	 * @var int
	 */
	const Recurse = 0x0800;

}