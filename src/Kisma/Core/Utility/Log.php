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
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The default log line format
	 */
	const DefaultLogFormat = '%%level%% %%date%% %%time%% %%context_tag%%: %%message%%';
	/**
	 * @var string The default log line format
	 */
	const AlternateLogFormat = '%%level%% %%date%% %%time%% %%host_name%% %%context_tag%%%%process_id%%: %%message%%';

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

	//********************************************************************************
	//* Public Methods
	//********************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public static function log( $message, $level = self::Info, $context = array(), $extra = null, $tag = null )
	{
		$_timestamp = time();

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

		$_levelName = self::_getLogLevel( $level );

		$_entry = str_ireplace(
			array(
				'%%level%%',
				'%%date%%',
				'%%time%%',
				'%%host_name%%',
				'%%context_tag%%',
				'%%process_id%%',
				'%%message%%',
			),
			array(
				$_levelName,
				date( 'M d', $_timestamp ),
				date( 'H:i:s', $_timestamp ),
				gethostname(),
				Option::get( $context, 'tag', $tag ),
				'[' . getmypid() . ']',
				self::$_prefix . str_repeat( '  ', $_tempIndent ) . $message,
			),
			self::DefaultLogFormat
		) . PHP_EOL;

		@error_log( $_entry, 3, self::$_defaultLog );
		@flush();

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
		return self::log( $message, self::Error, $context, $extra, self::_getCallingMethod() );
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
		return self::log( $message, self::Warning, $context, $extra, self::_getCallingMethod() );
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
		return self::log( $message, self::Notice, $context, $extra, self::_getCallingMethod() );
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
		return self::log( $message, self::Info, $context, $extra, self::_getCallingMethod() );
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
		return self::log( $message, self::Debug, $context, $extra, self::_getCallingMethod() );
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
			if ( Option::get( $_backTrace[$_i], 'class' ) == $_thisClass )
			{
				continue;
			}

			$_class = Option::get( $_backTrace[$_i], 'class' );
			$_method = Option::get( $_backTrace[$_i], 'method', Option::get( $_backTrace[$_i], 'function' ) );
			$_type = Option::get( $_backTrace[$_i], 'type' );
			break;
		}

		if ( $_i >= 0 )
		{
			return Inflector::tag( str_ireplace( 'Kisma\\Core\\', null, $_class ), true ) . $_type . $_method;
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

	/**
	 * @param string $defaultLog
	 */
	public static function setDefaultLog( $defaultLog )
	{
		self::$_defaultLog = $defaultLog;
	}

	/**
	 * @return string
	 */
	public static function getDefaultLog()
	{
		return self::$_defaultLog;
	}

}

/**
 * Set a name for the default log
 */
Log::setDefaultLog( dirname( \Kisma::getBasePath() ) . '/log/kisma.log' );
