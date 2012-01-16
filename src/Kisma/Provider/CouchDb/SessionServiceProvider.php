<?php
/**
 * @file
 * Provides ...
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Framework
 * @package kisma
 * @since 1.0.0
 *
 * @ingroup framework
 */

namespace Kisma\Provider\CouchDb;

//*************************************************************************
//* Aliases 
//*************************************************************************

use Kisma\Provider\CouchDb\Document\Session;

/**
 * SessionServiceProvider
 */
class SessionServiceProvider extends \Kisma\Components\SilexServiceProvider
{
	//*************************************************************************
	//* Class Constants
	//*************************************************************************

	/**
	 * @const string The name of our session table
	 */
	const DocumentName = 'Kisma\\Provider\\CouchDb\\Document\\Session';
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
	/**
	 * @var \Doctrine\ODM\CouchDB\DocumentManager
	 */
	protected static $_dm = null;

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
		register_shutdown_function( 'session_write_close' );
	}

	/**
	 * Opens the session, creating the design document if necessary. You do not
	 * need to call this function directly, because PHP will do it for you.
	 *
	 * @param string $sessionPath The save path (not used).
	 * @param string $sessionName The session name, which will be used for the
	 * database name.
	 *
	 * @return bool Whether or not the operation was successful.
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
	 * Retrieves the session by its ID (document's _id).
	 *
	 * @param string $id The session ID.
	 *
	 * @return string The serialized session data (PHP takes care of unserialization for us).
	 */
	public static function read( $id )
	{
		try
		{
			$_response = self::$_dm->find( self::DocumentName, $id );

			if ( 404 == $_response->status )
			{
				throw new \Kisma\StorageException( 'Session ID "' . $id . '" not found!' );
			}

			return $_response->body->data;
		}
		catch ( \Exception $_ex )
		{
			return '';
		}
	}

	/**
	 * Updates the session data, creating it if necessary. This will also advance
	 * the session's create_time timestamp to time(), pushing out when it will
	 * expire and be garbage collected.
	 *
	 * @param string $id The session ID.
	 * @param string $data The serialized data to store.
	 *
	 * @return bool Whether or not the operation was successful.
	 */
	public static function write( $id, $data )
	{
		$_response = self::$_dm->find( self::DocumentName, $id );

		if ( 404 == $_response->status )
		{
			$_session = new Kisma\Provider\CouchDb\Document\Session();
		}
		else
		{
			$_session = new Kisma\Provider\CouchDb\Document\Session( $_response->body );
		}

		$_session->data = $data;

		self::$_dm->persist( $_session );
		self::$_dm->flush();

		return true;
	}

	/**
	 * Destroys the session, deleting it from CouchDB.
	 *
	 * @param string $id The session ID.
	 *
	 * @return bool Whether or not the operation was successful.
	 */
	public static function destroy( $id )
	{
		try
		{
			$_doc = self::$_db->get( $id );

			if ( isset( $_doc->body ) )
			{
				self::$_db->delete( $_doc->_id, $_doc->_rev );
			}
		}
		catch ( \Exception $_ex )
		{
			return false;
		}

		return true;
	}

	/**
	 * Runs garbage collection against the sessions, deleting all those that are
	 * older than the number of seconds passed to this function. Uses CouchDB's
	 * Bulk Document API instead of deleting each one individually.
	 *
	 * @param int $maxLife The maximum life of a session in seconds.
	 *
	 * @return bool Whether or not the operation was successful.
	 */
	public static function gc( $maxLife )
	{
		$_expired = array();
		$_time = microtime( true );

		try
		{
			$_rows = self::$_db->getView( 'by_create_time', null, $_time,
				array( 'include_docs' => 'true' ) )->body->rows;

			foreach ( $_rows as $_row )
			{
				if ( $_row->doc->create_time + $maxLife < $_time )
				{
					$_row->doc->_deleted = true;
					$_expired[] = $_row->doc;
				}
			}

			if ( sizeof( $_expired ) > 0 )
			{
				self::$_db->bulk( $_expired );
			}
		}
		catch ( \Exception $_ex )
		{
			return false;
		}

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
	 * @param string $sessionTableName
	 */
	public static function setSessionTableName( $sessionTableName )
	{
		self::$_sessionTableName = $sessionTableName;
	}

	/**
	 * @return string
	 */
	public static function getSessionTableName()
	{
		return self::$_sessionTableName;
	}

	/**
	 * @static
	 * @return bool|string
	 */
	protected static function _checkExistingSession()
	{
		$_sessionId = \K::o( $_COOKIE, self::CookiePrefix . 'session_id', null, false, true );

		return $_sessionId ? : false;
	}

	/**
	 * Get a persistent session value
	 *
	 * @static
	 *
	 * @param	  $key
	 * @param null $defaultValue
	 * @param bool $unsetValue
	 *
	 * @return mixed
	 */
	public static function get( $key, $defaultValue = null, $unsetValue = false, $noTag = false )
	{
		$_value = \K::o( $_SESSION, $key, $defaultValue, $unsetValue, $noTag );
		return is_string( $_value ) && false !== @unserialize( $_value ) ? unserialize( $_value ) : $_value;
	}

	/**
	 * Sets a persistent session value
	 *
	 * @static
	 *
	 * @param	  $key
	 * @param null $value
	 *
	 * @return mixed
	 */
	public static function set( $key, $value = null )
	{
		return \K::so( $_SESSION, $key, is_scalar( $value ) ? $value : serialize( $value ) );
	}

	/**
	 * Registers services on the given app.
	 *
	 * @param Application $app An Application instance
	 */
	public
	function register( Application $app )
	{
		// TODO: Implement register() method.
	}
}

session_set_save_handler( array(
	'\\' . __NAMESPACE__ . '\\SessionServiceProvider',
	'open'
), array(
	'\\' . __NAMESPACE__ . '\\SessionServiceProvider',
	'close'
), array(
	'\\' . __NAMESPACE__ . '\\SessionServiceProvider',
	'read'
), array(
	'\\' . __NAMESPACE__ . '\\SessionServiceProvider',
	'write'
), array(
	'\\' . __NAMESPACE__ . '\\SessionServiceProvider',
	'destroy'
), array(
	'\\' . __NAMESPACE__ . '\\SessionServiceProvider',
	'gc'
) );
