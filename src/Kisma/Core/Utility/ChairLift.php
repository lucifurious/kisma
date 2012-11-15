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
	public static function createCouchDBClient( $options = array() )
	{
		return \Doctrine\CouchDB\CouchDBClient::create( $options );
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
	 *
	 * @return \Doctrine\ODM\CouchDB\DocumentManager
	 */
	public static function createDocumentManager( $options = array() )
	{
		return \Doctrine\ODM\CouchDB\DocumentManager::create(
			$options,
			Option::get( $options, 'config', new \Doctrine\ODM\CouchDB\Configuration(), true ),
			Option::get( $options, 'manager', new \Doctrine\Common\EventManager(), true )
		);
	}
}