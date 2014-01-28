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

use Doctrine\CouchDB\CouchDBClient;
use Doctrine\CouchDB\HTTP\HTTPException;
use Doctrine\ODM\CouchDB\DocumentManager;
use Kisma\Core\Interfaces\HttpResponse;
use Kisma\Core\Interfaces\UtilityLike;

/**
 * ChairLift
 * Lifts you up off the couch! It's a "couch" helper, get it?
 */
class ChairLift implements HttpResponse, UtilityLike
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var DocumentManager[]
	 */
	protected static $_dms = null;
	/**
	 * @var CouchDBClient[]
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
	 * @return CouchDBClient
	 */
	public static function couchDbClient( $options = array() )
	{
		$_key = Option::get( $options, 'host', 'localhost' ) . ':' . Option::get( $options, 'port', 5984 );

		if ( !isset( static::$_clients[$_key] ) )
		{
			static::$_clients[$_key] = CouchDBClient::create( $options );
		}

		return static::$_clients[$_key];
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
	 * @return DocumentManager
	 */
	public static function documentManager( $options = array() )
	{
		$_key = Option::get( $options, 'host', 'localhost' ) . ':' . Option::get( $options, 'port', 5984 );

		if ( !isset( static::$_dms[$_key] ) )
		{
			static::$_dms[$_key] = DocumentManager::create(
				Option::get( $options, 'client', static::couchDbClient( $options ) ),
				Option::get( $options, 'config' ),
				Option::get( $options, 'manager' )
			);
		}

		return static::$_dms[$_key];
	}

	/**
	 * @param CouchDBClient $client
	 * @param string        $database
	 * @param bool          $createIfNotFound
	 *
	 * @return bool Returns TRUE ONLY when the database existed before the call.
	 * @throws HTTPException
	 */
	public static function databaseExists( $client, $database = null, $createIfNotFound = true )
	{
		try
		{
			return $client->getDatabaseInfo( $database ? : $client->getDatabase() );
		}
		catch ( HTTPException $_ex )
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