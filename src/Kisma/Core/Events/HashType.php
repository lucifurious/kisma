<?php
/**
 * @file
 * Standard hash types
 */
namespace Kisma\Core\Interfaces;

/**
 * Standard hash types
 */
interface HashType
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @const int Supported hash algorithms
	 */
	const MD5 = 1;
	/**
	 * @const int Supported hash algorithms
	 */
	const SHA1 = 2;
	/**
	 * @const int Supported hash algorithms
	 */
	const CRC32 = 18;

}