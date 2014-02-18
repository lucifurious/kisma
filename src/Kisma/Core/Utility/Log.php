<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
 *
 * Kisma(tm) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kisma(tm) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kisma(tm).  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Kisma\Core\Utility;

use Kisma\Core\Interfaces\Levels;
use Kisma\Core\Interfaces\UtilityLike;
use Kisma\Core\Seed;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;

/**
 * Log
 * A generic log helper
 */
class Log extends Seed implements UtilityLike, Levels
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The default log line format
	 */
	const DefaultLogFormat = '%%date%% %%time%% %%level%% %%message%% %%extra%%';
	/**
	 * @var string The relative path (from the Kisma base path) for the default log
	 * @deprecated in 0.2.20. To be removed in 0.3.0
	 */
	const DefaultLogFile = '/kisma.log';
	/**
	 * @var string The default log file name if not specified
	 */
	const DEFAULT_LOG_FILE_NAME = 'kisma.log';

	//********************************************************************************
	//* Members
	//********************************************************************************

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
		true  => '<*',
		false => '*>',
	);
	/**
	 * @var string The full path and name of the log file
	 */
	protected static $_defaultLog = null;
	/**
	 * @var string The format of the log entries
	 */
	protected static $_logFormat = self::DefaultLogFormat;
	/**
	 * @var bool If true, pid, uid, and hostname are added to log entry
	 */
	protected static $_includeProcessInfo = false;
	/**
	 * @var bool Set when log file has been validated
	 */
	protected static $_logFileValid = false;
	/**
	 * @var string The path of the log file
	 */
	protected static $_logFilePath = null;
	/**
	 * @var string The name of the log file
	 */
	protected static $_logFileName = null;
	/**
	 * @var Logger
	 */
	protected static $_logger = null;
	/**
	 * @var Logger
	 */
	protected static $_fallbackLogger = null;

	//********************************************************************************
	//* Methods
	//********************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public static function log( $message, $level = Logger::INFO, $context = array(), $extra = null, $tag = null )
	{
		static $_firstRun = true;

		if ( $_firstRun || !static::$_logFileValid )
		{
			static::_checkLogFile();

			if ( !static::$_logFileValid )
			{
				static::setDefaultLog( LOG_SYSLOG );

				if ( is_numeric( static::$_defaultLog ) || empty( static::$_defaultLog ) || !file_exists( static::$_defaultLog ) )
				{
					static::setDefaultLog( static::$_defaultLog ? : LOG_SYSLOG );
				}
			}

			static::$_logFileValid = true;
			$_firstRun = false;
		}

		$_timestamp = time();

		//	Get the indent, if any
		$_unindent = ( ( $_newIndent = static ::_processMessage( $message ) ) > 0 );

		//	Indent...
		$_tempIndent = static::$_currentIndent;

		if ( $_unindent )
		{
			$_tempIndent--;
		}

		if ( $_tempIndent < 0 )
		{
			$_tempIndent = 0;
		}

//		$_entry = static::formatLogEntry(
//			array(
//				'level'     => $level,
//				'message'   => static::$_prefix . str_repeat( '  ', $_tempIndent ) . $message,
//				'timestamp' => $_timestamp,
//				'context'   => $context,
//				'extra'     => $extra,
//			)
//		);

		$_message = static::$_prefix . str_repeat( '  ', $_tempIndent ) . $message;

		if ( static::$_logFileValid )
		{
			static::$_logger->addRecord( $level, $_message, !is_array( $context ) ? array() : $context );
		}
		else
		{
			static::$_fallbackLogger->addRecord( $level, $_message, $context );
		}

		//	Set indent level...
		static::$_currentIndent += $_newIndent;

		//	Anything over a warning returns false so you can chain
		return ( Logger::WARNING > $level );
	}

	/**
	 * Formats the log entry. You can override this method to provide you own formatting.
	 * It will strip out any console escape sequences as well
	 *
	 * @param array $entry Read the code, data in the array
	 * @param bool  $newline
	 *
	 * @return string
	 */
	public static function formatLogEntry( array $entry, $newline = true )
	{
		$_level = Option::get( $entry, 'level' );
		$_levelName = static::_getLogLevel( $_level );
		$_timestamp = Option::get( $entry, 'timestamp' );
		$_message = preg_replace( '/\033\[[\d;]+m/', null, Option::get( $entry, 'message' ) );
		$_context = Option::get( $entry, 'context' );
		$_extra = Option::get( $entry, 'extra' );

		$_blob = new \stdClass();

		if ( static::$_includeProcessInfo )
		{
			$_blob->pid = getmypid();
			$_blob->uid = getmyuid();
			$_blob->hostname = gethostname();
		}

		if ( !empty( $_context ) )
		{
			$_blob->context = $_context;
		}

		if ( !empty( $_extra ) )
		{
			$_context->extra = $_extra;
		}

		$_blob = json_encode( $_blob );

		if ( false === $_blob || '{}' == $_blob )
		{
			$_blob = null;
		}

		$_replacements = array(
			0 => $_levelName,
			1 => date( 'M d', $_timestamp ),
			2 => date( 'H:i:s', $_timestamp ),
			3 => $_message,
			4 => $_blob,
		);

		return str_ireplace(
				   array(
					   '%%level%%',
					   '%%date%%',
					   '%%time%%',
					   '%%message%%',
					   '%%extra%%',
				   ),
				   $_replacements,
				   static::$_logFormat
			   ) . ( $newline ? PHP_EOL : null );
	}

	/**
	 * Creates an 'error' log entry
	 *
	 * @param string $message The message to send to the log
	 * @param array  $context
	 * @param mixed  $extra
	 *
	 * @return bool
	 */
	public static function error( $message, $context = array(), $extra = null )
	{
		return static::log( $message, static::Error, $context, $extra, static::_getCallingMethod() );
	}

	/**
	 * Creates a 'warning' log entry
	 *
	 * @param string $message The message to send to the log
	 * @param array  $context
	 * @param mixed  $extra
	 *
	 * @return bool
	 */
	public static function warning( $message, $context = array(), $extra = null )
	{
		return static::log( $message, static::Warning, $context, $extra, static::_getCallingMethod() );
	}

	/**
	 * Creates a 'notice' log entry
	 *
	 * @param string $message The message to send to the log
	 * @param array  $context
	 * @param mixed  $extra
	 *
	 * @return bool
	 */
	public static function notice( $message, $context = array(), $extra = null )
	{
		return static::log( $message, static::Notice, $context, $extra, static::_getCallingMethod() );
	}

	/**
	 * Creates an 'info' log entry
	 *
	 * @param string $message The message to send to the log
	 * @param array  $context
	 * @param mixed  $extra
	 *
	 * @return bool
	 */
	public static function info( $message, $context = array(), $extra = null )
	{
		return static::log( $message, static::Info, $context, $extra, static::_getCallingMethod() );
	}

	/**
	 * Creates a 'debug' log entry
	 *
	 * @param string $message The message to send to the log
	 * @param array  $context
	 * @param mixed  $extra
	 *
	 * @return bool
	 */
	public static function debug( $message, $context = array(), $extra = null )
	{
		return static::log( $message, static::Debug, $context, $extra, static::_getCallingMethod() );
	}

	/**
	 * Safely decrements the current indent level
	 *
	 * @param int $howMuch
	 */
	public static function decrementIndent( $howMuch = 1 )
	{
		static::$_currentIndent -= $howMuch;

		if ( static::$_currentIndent < 0 )
		{
			static::$_currentIndent = 0;
		}
	}

	/**
	 * Makes the system log path if not there...
	 */
	public static function checkSystemLogPath()
	{
		if ( null === static::$_fallbackLogger )
		{
			static::$_fallbackLogger = new Logger( 'kisma.fallback' );
		}

		static::$_fallbackLogger->pushHandler( new SyslogHandler( static::$_fallbackLogger->getName() ) );
	}

	/**
	 * @param int  $level
	 * @param bool $fullName
	 *
	 * @return string
	 */
	protected static function _getLogLevel( $level = self::Info, $fullName = false )
	{
		static $_logLevels = null;

		if ( empty( $_logLevels ) )
		{
			$_logLevels = \Kisma\Core\Enums\Levels::getDefinedConstants();
		}

		$_levels = ( is_string( $level ) ? $_logLevels : array_flip( $_logLevels ) );

		if ( null === ( $_tag = Option::get( $_levels, $level ) ) )
		{
			$_tag = 'INFO';
		}

		return ( false === $fullName ? substr( strtoupper( $_tag ), 0, 4 ) : $_tag );
	}

	/**
	 * Returns the name of the method that made the call
	 *
	 * @return string
	 */
	protected static function _getCallingMethod()
	{
		$_backTrace = debug_backtrace();

		$_thisClass = get_called_class();
		$_type = $_class = $_method = null;

		for ( $_i = 0, $_size = sizeof( $_backTrace ); $_i < $_size; $_i++ )
		{
			if ( isset( $_backTrace[$_i]['class'] ) )
			{
				$_class = $_backTrace[$_i]['class'];
			}

			if ( $_class == $_thisClass )
			{
				continue;
			}

			if ( isset( $_backTrace[$_i]['method'] ) )
			{
				$_method = $_backTrace[$_i]['method'];
			}
			else if ( isset( $_backTrace[$_i]['function'] ) )
			{
				$_method = $_backTrace[$_i]['function'];
			}
			else
			{
				$_method = 'Unknown';
			}

			$_type = $_backTrace[$_i]['type'];
			break;
		}

		if ( $_i >= 0 )
		{
			return str_ireplace( 'Kisma\\Core\\', 'Core\\', $_class ) . $_type . $_method;
		}

		return 'Unknown';
	}

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

		foreach ( static::$_indentTokens as $_key => $_token )
		{
			if ( $_token == substr( $message, 0, strlen( $_token ) ) )
			{
				$_newIndent = ( false === $_key ? -1 : 1 );
				$message = substr( $message, strlen( static::$_indentTokens[true] ) );
			}
		}

		return $_newIndent;
	}

	/**
	 * Makes sure we have a log file name and path
	 */
	protected static function _checkLogFile()
	{
		if ( null !== static::$_logger )
		{
			return static::$_logFileValid = true;
		}

		if ( empty( static::$_logFilePath ) )
		{
			//	Try and figure out a good place to log...
			static::$_logFilePath = ( \Kisma::get( 'app.log_path', \Kisma::get( 'app.base_path' ) ) ? : getcwd() ) . '/log';

		}

		if ( !is_dir( static::$_logFilePath ) )
		{
			if ( false === @mkdir( static::$_logFilePath, 0777, true ) )
			{
				error_log( 'Unable to create default log directory: ' . static::$_logFilePath );

				return static::$_logFileValid = false;
			}
		}

		if ( empty( static::$_logFileName ) )
		{
			static::$_logFileName = static::DEFAULT_LOG_FILE_NAME;
		}

		static::$_defaultLog = static::$_logFilePath . '/' . trim( static::$_logFileName, '/' );

		static::$_logger = new Logger( 'kisma' );
		static::$_logger->pushHandler( new StreamHandler( static::$_defaultLog ) );

		return static::$_logFileValid = true;
	}

	/**
	 * @static
	 *
	 * @param int $currentIndent
	 *
	 * @return void
	 */
	public static function setCurrentIndent( $currentIndent = 0 )
	{
		static::$_currentIndent = $currentIndent;
	}

	/**
	 * @static
	 * @return int
	 */
	public static function getCurrentIndent()
	{
		return static::$_currentIndent;
	}

	/**
	 * @static
	 *
	 * @param string $prefix
	 *
	 * @return void
	 */
	public static function setPrefix( $prefix = null )
	{
		static::$_prefix = $prefix;
	}

	/**
	 * @static
	 * @return null|string
	 */
	public static function getPrefix()
	{
		return static::$_prefix;
	}

	/**
	 * @param array $indentTokens
	 */
	public static function setIndentTokens( $indentTokens )
	{
		static::$_indentTokens = $indentTokens;
	}

	/**
	 * @return array
	 */
	public static function getIndentTokens()
	{
		return static::$_indentTokens;
	}

	/**
	 * @param string $defaultLog
	 *
	 * @return bool
	 */
	public static function setDefaultLog( $defaultLog )
	{
		if ( null === $defaultLog )
		{
			static::$_defaultLog = null;

			return;
		}

		//	Set up a new log file...
		static::$_logger = null;
		static::$_logFilePath = dirname( $defaultLog );
		static::$_logFileName = basename( $defaultLog );

		static::_checkLogFile();
	}

	/**
	 * @return null|string
	 */
	public static function getDefaultLog()
	{
		return static::$_defaultLog;
	}

	/**
	 * @param string $logFormat
	 */
	public static function setLogFormat( $logFormat )
	{
		static::$_logFormat = $logFormat;
	}

	/**
	 * @return string
	 */
	public static function getLogFormat()
	{
		return static::$_logFormat;
	}

	/**
	 * @param boolean $includeProcessInfo
	 */
	public static function setIncludeProcessInfo( $includeProcessInfo )
	{
		static::$_includeProcessInfo = $includeProcessInfo;
	}

	/**
	 * @return boolean
	 */
	public static function getIncludeProcessInfo()
	{
		return static::$_includeProcessInfo;
	}

	/**
	 * @param string $logFileName
	 */
	public static function setLogFileName( $logFileName )
	{
		static::$_logFileName = $logFileName;
	}

	/**
	 * @return string
	 */
	public static function getLogFileName()
	{
		return static::$_logFileName;
	}

	/**
	 * @param string $logFilePath
	 */
	public static function setLogFilePath( $logFilePath )
	{
		static::$_logFilePath = $logFilePath;
	}

	/**
	 * @return string
	 */
	public static function getLogFilePath()
	{
		return static::$_logFilePath;
	}

	/**
	 * @return boolean
	 */
	public static function getLogFileValid()
	{
		return static::$_logFileValid;
	}

	/**
	 * @return \Monolog\Logger
	 */
	public static function getLogger()
	{
		return static::$_logger;
	}

	/**
	 * @param \Monolog\Logger $logger
	 */
	public static function setLogger( $logger )
	{
		static::$_logger = $logger;
	}

	/**
	 * @param \Monolog\Logger $fallbackLogger
	 */
	public static function setFallbackLogger( $fallbackLogger )
	{
		static::$_fallbackLogger = $fallbackLogger;
	}

	/**
	 * @return \Monolog\Logger
	 */
	public static function getFallbackLogger()
	{
		return static::$_fallbackLogger;
	}

}

Log::checkSystemLogPath();
