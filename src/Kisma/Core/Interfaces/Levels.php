<?php
/**
 * Levels.php
 */
namespace Kisma\Core\Interfaces;
/**
 * Levels
 * Individual log entry levels
 */
interface Levels
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const Emergency = 600;
	/**
	 * @var int
	 */
	const Alert = 550;
	/**
	 * @var int
	 */
	const Critical = 500;
	/**
	 * @var int
	 */
	const Error = 400;
	/**
	 * @var int
	 */
	const Warning = 300;
	/**
	 * @var int
	 */
	const Notice = 250;
	/**
	 * @var int
	 */
	const Info = 200;
	/**
	 * @var int
	 */
	const Debug = 100;

//	//*************************************************************************
//	//* Public Methods
//	//*************************************************************************
//
//	/**
//	 * @abstract
//	 *
//	 * @param string|\Exception $message
//	 * @param int               $level
//	 * @param array             $context
//	 * @param mixed             $extra
//	 *
//	 * @return bool If warning level or greater, false is returned. Otherwise true.
//	 */
//	public function log( $message, $level, $context = array(), $extra = null );
}
