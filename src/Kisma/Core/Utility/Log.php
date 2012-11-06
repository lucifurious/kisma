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
class Log extends \Kisma\Core\Seed implements \Kisma\Core\Interfaces\UtilityLike, \Kisma\Core\Interfaces\Levels
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The default log line format
	 */
	const DefaultLogFormat = '%%date%% %%time%% %%level%% %%message%% %%extra%%';

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
	/**
	 * @var string
	 */
	protected static $_defaultLog = null;
	/**
	 * @var string The format of the log entries
	 */
	protected static $_logFormat = self::DefaultLogFormat;

	//********************************************************************************
	//* Public Methods
	//********************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public static function log( $message, $level = self::Info, $context = array(), $extra = null, $tag = null )
	{
		//	If we're not debugging, don't log debug statements
		if ( static::Debug == $level )
		{
			$_debug = \Kisma::getDebug();

			if ( is_callable( $_debug ) )
			{
				$_debug = call_user_func( $_debug, $message, $level, $context, $extra, $tag );
			}

			if ( false === $_debug )
			{
				return true;
			}
		}

		static::_checkLogFile();

		$_timestamp = time();

		//	Get the indent, if any
		$_unindent = ( ( $_newIndent = static::_processMessage( $message ) ) > 0 );

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

		$_levelName = static::_getLogLevel( $level );

		$_entry = static::formatLogEntry(
			array(
				'level'     => $_levelName,
				'message'   => static::$_prefix . str_repeat( '  ', $_tempIndent ) . $message,
				'timestamp' => $_timestamp,
				'context'   => $context,
				'extra'     => $extra,
			)
		);

		error_log( $_entry, 3, static::$_defaultLog );

		//	Set indent level...
		static::$_currentIndent += $_newIndent;

		//	Anything over a warning returns false so you can chain
		return ( static::Warning > $level );
	}

	/**
	 * Formats the log entry. You can override this method to provide you own formatting.
	 *
	 * @param array $entry Read the code, data in the array
	 * @param bool  $newline
	 *
	 * @return string
	 */
	public static function formatLogEntry( array $entry, $newline = true )
	{
		$_level = Option::get( $entry, 'level' );
		$_timestamp = Option::get( $entry, 'timestamp' );
		$_message = Option::get( $entry, 'message' );
		$_context = Option::get( $entry, 'context' );
		$_extra = Option::get( $entry, 'extra' );

		$_blob = new \stdClass();
		$_blob->pid = getmypid();
		$_blob->uid = getmyuid();
		$_blob->hostname = gethostname();

		if ( !empty( $_context ) || !empty( $_extra ) )
		{
			if ( null !== $_context )
			{
				$_blob->context = $_context;
			}

			if ( null !== $_extra )
			{
				$_context->extra = $_extra;
			}
		}

		$_replacements =
			array(
				0 => $_level,
				1 => date( 'M d', $_timestamp ),
				2 => date( 'H:i:s', $_timestamp ),
				3 => $_message,
				4 => json_encode( $_blob ),
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

	//*************************************************************************
	//* Protected Methods
	//*************************************************************************

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
			$_logLevels = array();
			\Kisma\Core\Enums\SeedEnum::seedConstants(
				array(

				)
			)
			$_mirror = new \ReflectionClass( get_called_class() );
			$_constants = $_mirror->getConstants();

			foreach ( $_constants as $_name => $_value )
			{
				$_logLevels[$_name] = $_value;
			}
		}

		$_levels = is_string( $level ) ? $_logLevels : array_flip( $_logLevels );

		if ( null === ( $_tag = Option::get( $_levels, $level ) ) )
		{
			$_tag = 'Info';
		}

		if ( false === $fullName )
		{
			$_tag = substr( strtoupper( $_tag ), 0, 4 );
		}

		unset( $_levels );

		return $_tag;
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
	 * @param $prefix
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
	 */
	public static function setDefaultLog( $defaultLog )
	{
		static::$_defaultLog = $defaultLog;
	}

	/**
	 * @return string
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
	 * Makes sure we have a log file name and path
	 */
	protected static function _checkLogFile()
	{
		//	Set a name for the default log
		if ( null === static::$_defaultLog )
		{
			Log::setDefaultLog( dirname( \Kisma::getBasePath() ) . '/log/kisma.log' );
		}
	}

}