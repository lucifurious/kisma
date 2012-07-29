<?php
/**
 * @file
 * Standard hash seeds
 */
namespace Kisma\Core\Interfaces;

/**
 * Standard hash seeds
 */
interface HashSeed
{
	//*************************************************************************
	//* Constants
	//*************************************************************************
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const All = 0;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const AlphaLower = 1;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const AlphaUpper = 2;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const Alpha = 3;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const AlphaNumeric = 4;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const AlphaLowerNumeric = 5;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const Numeric = 6;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const AlphaLowerNumericIdiotProof = 7;
}