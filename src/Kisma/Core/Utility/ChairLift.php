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
			$_paths = null;

			if ( null === ( $_config = Option::get( $options, 'config' ) ) )
			{
				$_reader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
				$_reader->addNamespace( 'Doctrine\\ODM\\CouchDB\\Mapping\\Annotations' );
				$_reader->addNamespace( '\\Kisma\\Core\\Containers\\Documents' );

				if ( \Kisma\Core\Enums\PhpFrameworks::Yii == \Kisma::get( 'app.framework' ) &&
					null !== ( $_appPath = \Kisma::get( 'app.app_path' ) )
				)
				{
					$_paths = $_appPath . '/protected/models';
				}

				/** @noinspection PhpParamsInspection */
				$_driver = new \Doctrine\ODM\CouchDB\Mapping\Driver\AnnotationDriver( $_reader, $_paths );

				$_config = new \Doctrine\ODM\CouchDB\Configuration();

				$_config->setMetadataCacheImpl(
					new \Doctrine\Common\Cache\ArrayCache
				);

				$_config->setProxyDir( \sys_get_temp_dir() );
				$_config->setAutoGenerateProxyClasses( true );
				$_config->setMetadataDriverImpl( $_driver );
				$_config->setMetadataCacheImpl( new \Doctrine\Common\Cache\ArrayCache() );
 			}

			self::$_dms[$_key] = \Doctrine\ODM\CouchDB\DocumentManager::create(
				static::couchDbClient( $options ),
				$_config,
				Option::get( $options, 'manager' )
			);
		}

		return self::$_dms[$_key];
	}
}