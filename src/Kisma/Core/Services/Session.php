<?php
/**
 * Session.php
 */
namespace Kisma\Core\Services;
/**
 * Session
 */
class Session extends \Kisma\Core\SeedBag
{
	//*************************************************************************
	//* Class Constants
	//*************************************************************************

	/**
	 * @const string The prefix for our cookies
	 */
	const CookiePrefix = 'kisma_';
	/**
	 * @const int Session lifetime (30 days)
	 */
	const SessionCookieLifetime = 2592000;
	/**
	 * @const string
	 */
	const SessionCookiePath = '/';
	/**
	 * @const int
	 */
	const SessionCookieGcTimeout = 600;

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string Our session name
	 */
	protected static $_sessionName;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Initializes the database
	 *
	 * @static
	 *
	 * @param bool $startSession
	 */
	public static function initialize( $startSession = true )
	{
		\ini_set( 'session.gc_maxlifetime', self::SessionCookieGcTimeout + self::SessionCookieLifetime );
		\session_set_cookie_params( self::SessionCookieLifetime, self::SessionCookiePath );
		\session_start();

		//	Ensure we can write by registering a shutdown function
		\register_shutdown_function( 'session_write_close' );
	}

	/**
	 * Opens the session
	 * You do not need to call this function directly, PHP will do it for you.
	 *
	 * @param string $sessionPath The save path (not used).
	 * @param string $sessionName The session name
	 *
	 * @return void
	 */
	public static function open( $sessionPath, $sessionName )
	{
		self::$_sessionName = trim( strtolower( $sessionName ) );
	}

	/**
	 * Closes the session
	 *
	 * @return bool Always returns true, because there's nothing for us to clean up.
	 */
	public static function close()
	{
		return true;
	}

	/**
	 * Retrieves the session by ID
	 *
	 * @param string $id The session ID.
	 *
	 * @throws \Kisma\Core\Exceptions\NotImplementedException
	 * @return string The serialized session data
	 */
	public static function read( $id )
	{
		throw new \Kisma\Core\Exceptions\NotImplementedException();
	}

	/**
	 * Updates the session data, creating it if necessary. This will also advance
	 * the session's create_time timestamp to time(), pushing out when it will
	 * expire and be garbage collected.
	 *
	 * @param string $id   The session ID.
	 * @param string $data The serialized data to store.
	 *
	 * @throws \Kisma\Core\Exceptions\NotImplementedException
	 * @return bool
	 */
	public static function write( $id, $data )
	{
		throw new \Kisma\Core\Exceptions\NotImplementedException();
	}

	/**
	 * Destroys the session
	 *
	 * @param string $id The session ID.
	 *
	 * @throws \Kisma\Core\Exceptions\NotImplementedException
	 * @return bool
	 */
	public static function destroy( $id )
	{
		throw new \Kisma\Core\Exceptions\NotImplementedException();
	}

	/**
	 * Runs garbage collection against the sessions, deleting all those that are
	 * older than the number of seconds passed to this function.
	 *
	 * The default implementation does nothing, like the goggles.
	 *
	 * @param int $maxLife The maximum life of a session in seconds.
	 *
	 * @return bool
	 */
	public static function gc( $maxLife )
	{
		return true;
	}

	/**
	 * @param string $sessionName
	 */
	public static function setSessionName( $sessionName )
	{
		self::$_sessionName = $sessionName;
	}

	/**
	 * @return string
	 */
	public static function getSessionName()
	{
		return self::$_sessionName;
	}

	/**
	 * @return bool|string
	 */
	protected static function _checkExistingSession()
	{
		return \Kisma\Core\Utility\FilterInput::cookie( self::CookiePrefix . 'session_id', false );
	}
}

/**
 * Set up the handlers for each thing
 */
\session_set_save_handler(
	array(
		__CLASS__,
		'open'
	),
	array(
		__CLASS__,
		'close'
	),
	array(
		__CLASS__,
		'read'
	),
	array(
		__CLASS__,
		'write'
	),
	array(
		__CLASS__,
		'destroy'
	),
	array(
		__CLASS__,
		'gc'
	)
);
