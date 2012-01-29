<?php
/**
 * @file
 * A generic log helper
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Utilities
 * @package kisma.utility
 * @since 1.0.0
 *
 * @ingroup utilities
 */

namespace Kisma\Utility;

//	Aliases
use Kisma\K;
use Kisma\Components as Components;

/**
 * Log
 * It's better than bad! It's GOOD! All kids love Log!
 */
class Log extends Components\Seed implements \Kisma\IUtility
{
	//********************************************************************************
	//* Private Members
	//********************************************************************************

	/**
	 * @var boolean If true, all applicable log entries will be echoed to STDOUT as well as logged
	 */
	protected static $_echoData = false;
	/**
	 * @var string Prepended to each log entry before writing.
	 */
	protected static $_prefix = null;
	/**
	 * @var integer The current indent level
	 */
	protected static $_currentIndent = 0;
	/**
	 * @var array The strings to watch for at the beginning of a log line to control the indenting
	 */
	protected static $_indentTokens = array(
		true => '>>', false => '<<',
	);
	/**
	 * @var string
	 */
	protected static $_defaultLevelIndicator = ' ';
	/**
	 * @var array
	 */
	protected static $_levelIndicators = array(
		\Kisma\LogLevel::Info => '*',
		\Kisma\LogLevel::Notice => '?',
		\Kisma\LogLevel::Warning => '-',
		\Kisma\LogLevel::Error => '!',
	);

	//********************************************************************************
	//* Public Methods
	//********************************************************************************

	/**
	 * Creates any log entry
	 *
	 * @param string $message The message to send to the log
	 * @param string $logLevel
	 *
	 * @return bool
	 */
	public static function log( $message, $logLevel = \Kisma\LogLevel::Info )
	{
		//	Get the indent, if any
		$_unindent = ( 0 > ( $_newIndent = self::_processMessage( $message ) ) );

		$_indicator = Option::o( self::$_levelIndicators, $logLevel, self::$_defaultLevelIndicator );
		$_logEntry = self::$_prefix . $message;

		if ( self::$_echoData )
		{
			echo K::log( $_logEntry, $logLevel, true );
			flush();
		}

		//	Indent...
		$_tempIndent = self::$_currentIndent;

		if ( $_unindent )
		{
			$_tempIndent--;
		}

		if ( $_tempIndent < 0 )
		{
			$_tempIndent = 0;
		}

		$_logEntry = str_repeat( '  ', $_tempIndent ) . $_indicator . ' ' . $message;

		K::log( $_logEntry, $logLevel, array( 'source' => self::_getCallingMethod() ) );

		//	Set indent level...
		self::$_currentIndent += $_newIndent;

		//	Anything over a warning returns false so you can chain
		return ( \Kisma\LogLevel::Warning > $logLevel );
	}

	/**
	 * Creates an 'info' log entry
	 *
	 * @param string $message The message to send to the log
	 * the calling method will be used.
	 *
	 * @return bool
	 */
	public static function info( $message )
	{
		return self::log( $message, \Kisma\LogLevel::Info );
	}

	/**
	 * Creates an 'error' log entry
	 *
	 * @param string $message The message to send to the log
	 *
	 * @return bool
	 */
	public static function error( $message )
	{
		return self::log( $message, \Kisma\LogLevel::Error );
	}

	/**
	 * Creates a 'warning' log entry
	 *
	 * @param string $message The message to send to the log
	 *
	 * @return bool
	 */
	public static function warning( $message )
	{
		return self::log( $message, \Kisma\LogLevel::Warning );
	}

	/**
	 * Alias of the 'debug' level
	 *
	 * @param string $message The message to send to the log
	 *
	 * @return bool
	 */
	public static function trace( $message )
	{
		return self::debug( $message );
	}

	/**
	 * Creates a 'debug' log entry
	 *
	 * @param string $message The message to send to the log
	 *
	 * @return bool
	 */
	public static function debug( $message )
	{
		return self::log( $message, \Kisma\LogLevel::Debug );
	}

	/**
	 * Safely decrements the current indent level
	 *
	 * @param int $howMuch
	 */
	public static function decrementIndent( $howMuch = 1 )
	{
		self::$_currentIndent -= $howMuch;

		if ( self::$_currentIndent < 0 )
		{
			self::$_currentIndent = 0;
		}
	}

	/**
	 * Returns the name of the method that made the call
	 *
	 * @return string
	 */
	protected static function _getCallingMethod()
	{
		$_backTrace = debug_backtrace();
		$_caller = 4;
		$_function = Option::o( $_backTrace[$_caller], 'method', Option::o( $_backTrace[$_caller], 'function' ) );
		$_class = Option::o( $_backTrace[$_caller], 'class' );

		$_callingMethod = ( null !== $_class ? $_class . Option::o( $_backTrace[$_caller], 'type' ) : null ) . $_function;

		return str_replace( array( '::', 'kisma.' ), array( '.', 'k.' ), Inflector::untag( $_callingMethod ) );
	}

	//*************************************************************************
	//* Protected Methods
	//*************************************************************************

	/**
	 * Processes the indent level for the messages
	 *
	 * @param string $message
	 *
	 * @return integer The indent difference AFTER this message
	 */
	protected static function _processMessage( &$message )
	{
		$_newIndent = 0;

		foreach ( self::$_indentTokens as $_key => $_token )
		{
			if ( $_token == substr( $message, 0, strlen( $_token ) ) )
			{
				$_newIndent = ( false === $_key ? -1 : 1 );
				$message = substr( $message, strlen( self::$_indentTokens[true] ) );
			}
		}

		return $_newIndent;
	}

	/**
	 * @static
	 *
	 * @param $currentIndent
	 */
	public static function setCurrentIndent( $currentIndent = 0 )
	{
		self::$_currentIndent = $currentIndent;
	}

	/**
	 * @static
	 * @return int
	 */
	public static function getCurrentIndent()
	{
		return self::$_currentIndent;
	}

	/**
	 * @param string $defaultLevelIndicator
	 */
	public static function setDefaultLevelIndicator( $defaultLevelIndicator = '.' )
	{
		self::$_defaultLevelIndicator = $defaultLevelIndicator;
	}

	/**
	 * @return string
	 */
	public static function getDefaultLevelIndicator()
	{
		return self::$_defaultLevelIndicator;
	}

	/**
	 * @static
	 *
	 * @param $echoData
	 */
	public static function setEchoData( $echoData = false )
	{
		self::$_echoData = $echoData;
	}

	/**
	 * @static
	 * @return bool
	 */
	public static function getEchoData()
	{
		return self::$_echoData;
	}

	/**
	 * @param array $logLevelIndicators
	 */
	public static function setLevelIndicators( $logLevelIndicators )
	{
		self::$_levelIndicators = $logLevelIndicators;
	}

	/**
	 * @return array
	 */
	public static function getLevelIndicators()
	{
		return self::$_levelIndicators;
	}

	/**
	 * @static
	 *
	 * @param $prefix
	 */
	public static function setPrefix( $prefix = null )
	{
		self::$_prefix = $prefix;
	}

	/**
	 * @static
	 * @return null|string
	 */
	public static function getPrefix()
	{
		return self::$_prefix;
	}

	/**
	 * @param array $indentTokens
	 */
	public static function setIndentTokens( $indentTokens )
	{
		self::$_indentTokens = $indentTokens;
	}

	/**
	 * @return array
	 */
	public static function getIndentTokens()
	{
		return self::$_indentTokens;
	}
}
