<?php
/**
 * Log.php
 * It's better than bad! It's GOOD! All kids love Log!
 *
 * @todo Class neutered at the moment
 */
namespace Kisma\Core\Utility;
/**
 * Log
 * A generic log helper
 */
class Log extends \Kisma\Core\Seed implements \Kisma\Core\Interfaces\SeedUtility, \Kisma\Core\Interfaces\Levels
{
	//********************************************************************************
	//* Private Members
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

	//********************************************************************************
	//* Public Methods
	//********************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public static function log( $message, $level = self::Info, $context = array(), $extra = null )
	{
		//	Get the indent, if any
		$_unindent = ( ( $_newIndent = self::_processMessage( $message ) ) > 0 );

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

		$_entry = self::$_prefix . str_repeat( '  ', $_tempIndent ) . \Kisma\Core\Enums\Levels::getIndicator( $level ) . ' ' . $message;

//		K::log( $_logEntry, $level, array( 'source' => self::_getCallingMethod() ) );

		//	Set indent level...
		self::$_currentIndent += $_newIndent;

		//	Anything over a warning returns false so you can chain
		return ( self::Warning > $level );
	}

	//*************************************************************************
	//* Convenience Methods
	//*************************************************************************

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
		return self::log( $message, self::Error, $context, $extra );
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
		return self::log( $message, self::Warning, $context, $extra );
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
		return self::log( $message, self::Notice, $context, $extra );
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
		return self::log( $message, self::Info, $context, $extra );
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
		return self::log( $message, self::Debug, $context, $extra );
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

	//*************************************************************************
	//* Protected Methods
	//*************************************************************************

	/**
	 * Returns the name of the method that made the call
	 *
	 * @return string
	 */
	protected static function _getCallingMethod()
	{
		$_backTrace = debug_backtrace();
		$_caller = 4;
		$_function = Option::get( $_backTrace[$_caller], 'method', Option::get( $_backTrace[$_caller], 'function' ) );
		$_class = Option::get( $_backTrace[$_caller], 'class' );
		$_callingMethod = ( null !== $_class ? $_class . Option::get( $_backTrace[$_caller], 'type' ) : null ) . $_function;
		return str_replace( array( '::', 'kisma.' ), array( '.', 'k.' ), Inflector::untag( $_callingMethod ) );
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

	//*************************************************************************
	//* Properties
	//*************************************************************************

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
