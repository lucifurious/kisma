<?php
/**
 * @file
 *            An optional base class for other utility classes.
 *            No need to use it if you don't wanna
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 *
 * @copyright Copyright (c) 2009-2012 Jerry Ablan
 * @license   http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author    Jerry Ablan <kisma@pogostick.com>
 *
 * @ingroup   utility
 */
namespace Kisma\Utility;

//*************************************************************************
//* Aliases
//*************************************************************************

require_once dirname( __DIR__ ) . '/KismaPath.php';

use Kisma\Kisma as K;
use Kisma\Utility\Option;

use Monolog;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * BaseUtility
 * A base class for utilities that defines a log mechanism
 */
class BaseUtility
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Monolog\Logger Set to false to disable logging
	 */
	protected static $_logger = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Initializes the logger
	 */
	public static function initialize()
	{
		if ( null === self::$_logger )
		{
			$_handler = new StreamHandler( '/tmp/kisma.utility.log' );
			$_handler->setFormatter( new KismaLogFormatter() );

			self::$_logger = new Logger( PHP_SAPI . '.utility' );
			self::$_logger->pushHandler( $_handler );
		}
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param \Monolog\Logger $logger
	 */
	public static function setLogger( $logger )
	{
		self::$_logger = $logger;
	}

	/**
	 * @return \Monolog\Logger
	 */
	public static function getLogger()
	{
		return self::$_logger;
	}
}
