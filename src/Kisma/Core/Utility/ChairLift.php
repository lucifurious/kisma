<?php
/**
 * ChairLift.php
 */
namespace Kisma\Core\Utility;
use Kisma\Core\Utility\Option;

/**
 * ChairLift
 * Lifts you up off the couch! It's a "couch" helper, get it?
 */
class ChairLift implements \Kisma\Core\Interfaces\HttpResponse
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var \Doctrine\ODM\CouchDB\DocumentManager[]
	 */
	protected static $_dms = null;
	/**
	 * @var \Doctrine\CouchDB\CouchDBClient[]
	 */
	protected static $_clients = array();

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Construct and returns a CouchDBClient
	 *
	 * @param array $options
	 *
	 * Options are:
	 *
	 * @option string         $dbname The name of the database
	 * @option string         $type The connection type, "socket" or "stream"
	 * @option string         $host
	 * @option int            $port
	 * @option string         $user
	 * @option string         $password
	 * @option string         $ip
	 * @option bool           $logging
	 *
	 * @return \Doctrine\CouchDB\CouchDBClient
	 */
	public static function couchDbClient( $options = array() )
	{
		$_key = Option::get( $options, 'host', 'localhost' ) . ':' . Option::get( $options, 'port', 5984 );

		if ( !isset( self::$_clients[$_key] ) )
		{
			self::$_clients[$_key] = \Doctrine\CouchDB\CouchDBClient::create( $options );
		}

		return self::$_clients[$_key];
	}

	/**
	 * Constructs and sets all options at once.
	 *
	 * @param array $options
	 *
	 * Options are:
	 *
	 * @option string             $dbname The name of the database
	 * @option string             $type The connection type, "socket" or "stream"
	 * @option string             $host
	 * @option int                $port
	 * @option string             $user
	 * @option string             $password
	 * @option string             $ip
	 * @option bool               $logging
	 * @option Configuration      $config
	 * @option EventManager       $manager
	 * @option array               $namespaces Array of additional document namespaces
	 *
	 * @return \Doctrine\ODM\CouchDB\DocumentManager
	 */
	public static function documentManager( $options = array() )
	{
		$_key = Option::get( $options, 'host', 'localhost' ) . ':' . Option::get( $options, 'port', 5984 );

		if ( !isset( self::$_dms[$_key] ) )
		{
			self::$_dms[$_key] = \Doctrine\ODM\CouchDB\DocumentManager::create(
				Option::get( $options, 'client', static::couchDbClient( $options ) ),
				Option::get( $options, 'config' ),
				Option::get( $options, 'manager' )
			);
		}

		return self::$_dms[$_key];
	}

	/**
	 * @param \Doctrine\CouchDB\CouchDBClient $client
	 * @param string                          $database
	 * @param bool                            $createIfNotFound
	 *
	 * @return bool Returns TRUE ONLY when the database existed before the call.
	 * @throws \Doctrine\CouchDB\HTTP\HTTPException
	 */
	public static function databaseExists( $client, $database = null, $createIfNotFound = true )
	{
		try
		{
			return $client->getDatabaseInfo( $database ? : $client->getDatabase() );
		}
		catch ( \Doctrine\CouchDB\HTTP\HTTPException $_ex )
		{
			if ( static::NotFound != $_ex->getCode() )
			{
				throw $_ex;
			}

			if ( true === $createIfNotFound )
			{
				$client->createDatabase( $database ? : $client->getDatabase() );
			}
		}

		return false;
	}
}