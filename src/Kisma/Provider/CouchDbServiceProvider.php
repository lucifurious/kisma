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
namespace Kisma\Provider;

//*************************************************************************
//* Aliases
//*************************************************************************

use Kisma\Components as Components;
use Kisma\AppConfig;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\ODM\CouchDB\Configuration;
use Kisma\Provider\CouchDb\DocumentManager;
use Doctrine\ODM\CouchDB\Mapping\Annotations\Document;
use Doctrine\Common\EventManager;

//*************************************************************************
//* Requirements
//*************************************************************************

/**
 * CouchDbServiceProvider
 * A provider that wraps the CouchDbClient library for working with a CouchDb instance
 */
class CouchDbServiceProvider extends SilexServiceProvider
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
	public function register( \Silex\Application $app )
	{
		/**
		 * Service Initializer
		 */
		$app[CouchDbServiceProvider::ServiceInitializer] = $app->protect( function () use ( $app )
		{
			static $_initialized = false;

			if ( $_initialized )
			{
				return;
			}

			$_initialized = true;

			//	Set the default options
			$app[CouchDbServiceProvider::DefaultOptions] = array(
				'dbname' => null,
				'host' => 'localhost',
				'port' => 5984,
				'user' => null,
				'password' => null,
				'logging' => false,
			);

			//	Register our paths
			$app['autoloader']->registerNamespaces( array(
				'Doctrine\\Common' => $app['vendor_path'] . '/couchdb_odm/lib/vendor/doctrine-common/lib',
				'Doctrine\\ODM' => $app['vendor_path'] . '/couchdb_odm/lib',
				'Doctrine\\ODM\\CouchDB' => $app['vendor_path'] . '/couchdb_odm/lib/CouchDB',
				'Doctrine\\CouchDB' => $app['vendor_path'] . '/couchdb_odm/lib',
			) );

			//	Register namespaces with Doctrine as well
			foreach ( $app['autoloader']->getNamespaces() as $_namespace => $_path )
			{
				$_loader = new \Doctrine\Common\ClassLoader( $_namespace, $_path );
				$_loader->register();
				unset( $_loader );
			}

			\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespaces(
				$app['autoloader']->getNamespaces()
			);

			if ( !isset( $app[CouchDbServiceProvider::GroupOptions] ) )
			{
				$app[CouchDbServiceProvider::GroupOptions] = array(
					'default' => isset( $app[CouchDbServiceProvider::Options] ) ?
						$app[CouchDbServiceProvider::Options] : array()
				);
			}

			$_couchOptions = $app[CouchDbServiceProvider::GroupOptions];

			foreach ( $_couchOptions as $_name => &$_options )
			{
				$_options = array_replace( $app[CouchDbServiceProvider::DefaultOptions], $_options );

				if ( !isset( $app[CouchDbServiceProvider::DefaultGroupOptions] ) )
				{
					$app[CouchDbServiceProvider::DefaultGroupOptions] = $_name;
				}
			}
			$app[CouchDbServiceProvider::GroupOptions] = $_couchOptions;
		} );

		$app[CouchDbServiceProvider::Options_GroupPrefix] = $app->share( function () use ( $app )
		{
			$app[CouchDbServiceProvider::ServiceInitializer]();

			$_dbs = new \Pimple();
			foreach ( $app[CouchDbServiceProvider::GroupOptions] as $_name => $_options )
			{
				if ( $_name === $app[CouchDbServiceProvider::DefaultGroupOptions] )
				{
					// we use shortcuts here in case the default has been overriden
					$_config = $app[CouchDbServiceProvider::Options_Prefix . '.config'];
					$_manager = $app[CouchDbServiceProvider::Options_Prefix . '.event_manager'];
				}
				else
				{
					$_config = $app[CouchDbServiceProvider::Options_GroupPrefix . '.config'][$_name];
					$_manager = $app[CouchDbServiceProvider::Options_GroupPrefix . '.event_manager'][$_name];
				}

				$_dbs[$_name] = DocumentManager::create( $_options, $_config, $_manager );
			}

			return $_dbs;
		} );

		//	Group configuration
		$app[CouchDbServiceProvider::Options_GroupPrefix . '.config'] = $app->share( function () use ( $app )
		{
			$app[CouchDbServiceProvider::GroupOptions . '.initializer']();

			$_documentPath = $app['app.config.document_path'];

			$_configs = new \Pimple();
			foreach ( $app[CouchDbServiceProvider::GroupOptions] as $_name => $_options )
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
		$app[CouchDbServiceProvider::Options_GroupPrefix . '.event_manager'] = $app->share( function () use ( $app )
		{
			$app[CouchDbServiceProvider::ServiceInitializer]();

			$_managers = new \Pimple();
			foreach ( $app[CouchDbServiceProvider::GroupOptions] as $_name => $_options )
			{
				$_managers[$_name] = new EventManager();
			}

			return $_managers;
		} );

		//	Shortcuts for the "first" DB
		$app[CouchDbServiceProvider::Options_Prefix] = $app->share( function() use ( $app )
		{
			$_dbs = $app[CouchDbServiceProvider::Options_GroupPrefix];
			return $_dbs[$app[CouchDbServiceProvider::DefaultGroupOptions]];
		} );

		//	Shortcut to first db's client
		$app['couchdb.client'] = $app->share( function() use( $app )
		{
			return $app[CouchDbServiceProvider::Options_Prefix]->getCouchDBClient();
		} );

		//	Configuration
		$app['couchdb.config'] = $app->share( function() use ( $app )
		{
			$_dbs = $app[CouchDbServiceProvider::Options_GroupPrefix . '.config'];
			return $_dbs[$app[CouchDbServiceProvider::DefaultGroupOptions]];
		} );

		/**
		 * Single event manager
		 */
		$app['couchdb.event_manager'] = $app->share( function() use ( $app )
		{
			$_dbs = $app['couchdbs.event_manager'];
			return $_dbs[$app[CouchDbServiceProvider::DefaultGroupOptions]];
		} );
	}

}