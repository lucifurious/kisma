<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link		  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license	   http://github.com/Pogostick/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @category	  Kisma_Aspects_Storage_CouchDb
 * @package	   kisma.aspects.storage
 * @namespace	 \Kisma\Aspects\Storage
 * @since		 v1.0.0
 * @filesource
 */
namespace Kisma\Provider\CouchDb;

//*************************************************************************
//* Aliases
//*************************************************************************

use Kisma\Components as Components;
use Kisma\AppConfig;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\ODM\CouchDB\Configuration;
use Doctrine\ODM\CouchDB\DocumentManager;
use Doctrine\ODM\CouchDB\Mapping\Annotations\Document;
use Doctrine\Common\EventManager;
use Silex\Application;

//*************************************************************************
//* Requirements
//*************************************************************************

/**
 * ServiceProvider
 * A provider that wraps the CouchDbClient library for working with a CouchDb instance
 */
class ServiceProvider extends \Kisma\Provider\SilexServiceProvider
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string Our options prefix
	 */
	const Options_Prefix = 'couchdb';
	/**
	 * @var string Our group options prefix
	 */
	const Options_GroupPrefix = 'couchdbs';
	/**
	 * @var string Our group options prefix
	 */
	const GroupOptions = 'couchdbs.options';
	/**
	 * @var string The name of our options element
	 */
	const Options = 'couchdb.options';
	/**
	 * @var string The name of our options element
	 */
	const DefaultOptions = 'couchdb.default_options';
	/**
	 * @var string The name of our group options default
	 */
	const DefaultGroupOptions = 'couchdbs.default';
	/**
	 * @var string The name of our initializer method
	 */
	const ServiceInitializer = 'couchdbs.options.initializer';

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Registers the service with Silex
	 *
	 * @param \Silex\Application $app
	 */
	public function register( Application $app )
	{
		/**
		 * Service Initializer
		 */
		$app[ServiceProvider::ServiceInitializer] = $app->protect( function () use ( $app )
		{
			static $_initialized = false;

			if ( $_initialized )
			{
				return;
			}

			$_initialized = true;

			//	Set the default options
			$app[ServiceProvider::DefaultOptions] = array(
				'dbname' => null,
				'host' => 'localhost',
				'port' => 5984,
				'user' => null,
				'password' => null,
				'logging' => false,
			);

			$_doctrinePath = $app['vendor_path'] . '/couchdb_odm/lib/vendor/doctrine-common/lib';
			require_once $_doctrinePath . '/Doctrine/Common/ClassLoader.php';

			$_loader = new \Doctrine\Common\ClassLoader( 'Doctrine\Common', $_doctrinePath );
			$_loader->register();

			$_loader = new \Doctrine\Common\ClassLoader( 'Doctrine\ORM', $_doctrinePath );
			$_loader->register();

			//	Register our paths
			$app['autoloader']->registerNamespaces( array(
				'Doctrine\\Common' => $_doctrinePath,
				'Doctrine\\ODM' => $app['vendor_path'] . '/couchdb_odm/lib',
				'Doctrine\\ODM\\CouchDB' => $app['vendor_path'] . '/couchdb_odm/lib/CouchDB',
				'Doctrine\\CouchDB' => $app['vendor_path'] . '/couchdb_odm/lib',
			) );

			\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespaces(
				array(
					'Kisma' => __DIR__,
					'Doctrine\\Common' => $_doctrinePath,
					'Doctrine\\ODM' => $app['vendor_path'] . '/couchdb_odm/lib',
					'Doctrine\\ODM\\CouchDB' => $app['vendor_path'] . '/couchdb_odm/lib/CouchDB',
					'Doctrine\\CouchDB' => $app['vendor_path'] . '/couchdb_odm/lib',
				)
			);

			if ( !isset( $app[ServiceProvider::GroupOptions] ) )
			{
				$app[ServiceProvider::GroupOptions] = array(
					'default' => isset( $app[ServiceProvider::Options] ) ?
						$app[ServiceProvider::Options] : array()
				);
			}

			$_couchOptions = $app[ServiceProvider::GroupOptions];

			foreach ( $_couchOptions as $_name => &$_options )
			{
				$_options = array_replace( $app[ServiceProvider::DefaultOptions], $_options );

				if ( !isset( $app[ServiceProvider::DefaultGroupOptions] ) )
				{
					$app[ServiceProvider::DefaultGroupOptions] = $_name;
				}
			}
			$app[ServiceProvider::GroupOptions] = $_couchOptions;
		} );

		$app[ServiceProvider::Options_GroupPrefix] = $app->share( function () use ( $app )
		{
			$app[ServiceProvider::ServiceInitializer]();

			$_dbs = new \Pimple();
			foreach ( $app[ServiceProvider::GroupOptions] as $_name => $_options )
			{
				if ( $_name === $app[ServiceProvider::DefaultGroupOptions] )
				{
					// we use shortcuts here in case the default has been overriden
					$_config = $app[ServiceProvider::Options_Prefix . '.config'];
					$_manager = $app[ServiceProvider::Options_Prefix . '.event_manager'];
				}
				else
				{
					$_config = $app[ServiceProvider::Options_GroupPrefix . '.config'][$_name];
					$_manager = $app[ServiceProvider::Options_GroupPrefix . '.event_manager'][$_name];
				}

				$_dbs[$_name] = DocumentManager::create( $_options, $_config, $_manager );
			}

			return $_dbs;
		} );

		//	Group configuration
		$app[ServiceProvider::Options_GroupPrefix . '.config'] = $app->share( function () use ( $app )
		{
			$app[ServiceProvider::GroupOptions . '.initializer']();

			$_documentPath = $app['app.config.document_path'];

			$_configs = new \Pimple();
			foreach ( $app[ServiceProvider::GroupOptions] as $_name => $_options )
			{
				$_config = new \Doctrine\ODM\CouchDB\Configuration();
				$_metadataDriver =
					$_config->newDefaultAnnotationDriver( isset( $_options['document_path'] ) ?
						$_options['document_path'] : $_documentPath );
				$_config->setMetadataDriverImpl( $_metadataDriver );
				$_configs[$_name] = $_config;
			}

			return $_configs;
		} );

		//	Group events
		$app[ServiceProvider::Options_GroupPrefix . '.event_manager'] = $app->share( function () use ( $app )
		{
			$app[ServiceProvider::ServiceInitializer]();

			$_managers = new \Pimple();
			foreach ( $app[ServiceProvider::GroupOptions] as $_name => $_options )
			{
				$_managers[$_name] = new EventManager();
			}

			return $_managers;
		} );

		//	Shortcuts for the "first" DB
		$app[ServiceProvider::Options_Prefix] = $app->share( function() use ( $app )
		{
			$_dbs = $app[ServiceProvider::Options_GroupPrefix];
			return $_dbs[$app[ServiceProvider::DefaultGroupOptions]];
		} );

		//	Shortcut to first db's client
		$app['couchdb.client'] = $app->share( function() use( $app )
		{
			return $app[ServiceProvider::Options_Prefix]->getCouchDBClient();
		} );

		//	Configuration
		$app['couchdb.config'] = $app->share( function() use ( $app )
		{
			$_dbs = $app[ServiceProvider::Options_GroupPrefix . '.config'];
			return $_dbs[$app[ServiceProvider::DefaultGroupOptions]];
		} );

		/**
		 * Single event manager
		 */
		$app['couchdb.event_manager'] = $app->share( function() use ( $app )
		{
			$_dbs = $app['couchdbs.event_manager'];
			return $_dbs[$app[ServiceProvider::DefaultGroupOptions]];
		} );
	}

}