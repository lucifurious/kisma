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
class ChairLift
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
			\Kisma::getAutoLoader()->add(
				'Documents',
				\Kisma::get( 'app.base_path' ) . '/Kisma/Core/Containers/Documents'
			);

			\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespaces(
				array(
					'\\Kisma\\Core\\Containers\\Documents' => \Kisma::get( 'app.base_path' ) . '/Kisma/Core/Containers/Documents',
				)
			);

			self::$_dms[$_key] = \Doctrine\ODM\CouchDB\DocumentManager::create(
				static::couchDbClient( $options ),
				Option::get( $options, 'config' ),
				Option::get( $options, 'manager' )
			);
		}

		return self::$_dms[$_key];
	}
}