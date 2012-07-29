<?php
/**
 * Lumberjack.php
 */
namespace Kisma\Core\Interfaces;

/**
 * Lumberjack
 * When a class implements this interface, it becomes a logger.
 */
interface Lumberjack
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int The default is info
	 */
	const __default = self::Info;

	/**
	 * @var int alert
	 */
	const Alert = 550;
	/**
	 * @var int critical error
	 */
	const Critical = 500;
	/**
	 * @var int error
	 */
	const Error = 400;
	/**
	 * @var int warning
	 */
	const Warning = 300;
	/**
	 * @var int
	 */
	const Info = 200;
	/**
	 * @var int debug
	 */
	const Debug = 100;
	/**
	 * @var int trace (same as debug)
	 */
	const Trace = 100;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @abstract
	 *
	 * @param string|\Exception $message
	 * @param int|string        $level
	 * @param string            $source
	 * @param mixed             $data
	 *
	 * @return mixed
	 */
	public function log( $message, $level, $source, $data = null );
}
