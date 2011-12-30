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
namespace Kisma\Provider
{
	//*************************************************************************
	//* Aliases
	//*************************************************************************

	use Silex\ServiceProviderInterface;
	use Doctrine\CouchDB\CouchDBClient;
	use Doctrine\ODM\CouchDB\Configuration;
	use Doctrine\ODM\CouchDB\DocumentManager;
	use Doctrine\Common\EventManager;

	//*************************************************************************
	//* Requirements
	//*************************************************************************

	/**
	 * CouchDbServiceProvider
	 * A provider that wraps the CouchDbClient library for working with a CouchDb instance
	 */
	class CouchDbServiceProvider implements ServiceProviderInterface
	{
		/**
		 * Registers the service with Silex
		 *
		 * @param \Silex\Application $app
		 */
		public function register( \Silex\Application $app )
		{
			//	Set the default options
			$app['couchdb.default_options'] = array(
				'dbname' => null,
				'host' => 'localhost',
				'port' => 5984,
				'user' => null,
				'password' => null,
				'logging' => false,
			);

			//	Register our paths
			$app['couchdb.common.class_path'] = $app['base_path'] . '/vendor/doctrine-common/lib';

			$app['autoloader']->registerNamespaces(
				array(
					'Doctrine\\CouchDB' => $app['base_path'] . '/vendor/couchdb_odm/lib',
					'Doctrine\\ODM' => $app['base_path'] . '/vendor/couchdb_odm/lib',
				)
			);

			$app['couchdbs.options.initializer'] = $app->protect( function () use ( $app )
			{
				static $_initialized = false;

				if ( $_initialized )
				{
					return;
				}

				$_initialized = true;

				if ( !isset( $app['couchdbs.options'] ) )
				{
					$app['couchdbs.options'] = array( 'default' => isset( $app['couchdb.options'] ) ? $app['couchdb.options'] : array() );
				}

				$_couchOptions = $app['couchdbs.options'];

				foreach ( $_couchOptions as $_name => &$_options )
				{
					$_options = array_replace( $app['couchdb.default_options'], $_options );

					if ( !isset( $app['couchdbs.default'] ) )
					{
						$app['couchdbs.default'] = $_name;
					}
				}
				$app['couchdbs.options'] = $_couchOptions;
			} );

			$app['couchdbs'] = $app->share( function () use ( $app )
			{
				$app['couchdbs.options.initializer']();

				$_dbs = new \Pimple();
				foreach ( $app['couchdbs.options'] as $_name => $_options )
				{
					if ( $_name === $app['couchdbs.default'] )
					{
						// we use shortcuts here in case the default has been overriden
						$_config = $app['couchdb.config'];
						$_manager = $app['couchdb.event_manager'];
					}
					else
					{
						$_config = $app['couchdbs.config'][$_name];
						$_manager = $app['couchdbs.event_manager'][$_name];
					}

					$_dbs[$_name] = DocumentManager::create( $_options, $_config, $_manager );
				}

				return $_dbs;
			} );

			$app['couchdbs.config'] = $app->share( function () use ( $app )
			{
				$app['couchdbs.options.initializer']();

				$_configs = new \Pimple();
				foreach ( $app['couchdbs.options'] as $_name => $_options )
				{
					$_configs[$_name] = new Configuration();
				}

				return $_configs;
			} );

			$app['couchdbs.event_manager'] = $app->share( function () use ( $app )
			{
				$app['couchdbs.options.initializer']();

				$_managers = new \Pimple();
				foreach ( $app['couchdbs.options'] as $_name => $_options )
				{
					$_managers[$_name] = new EventManager();
				}

				return $_managers;
			} );

			// shortcuts for the "first" DB
			$app['couchdb'] = $app->share( function() use ( $app )
			{
				$_dbs = $app['couchdbs'];

				return $_dbs[$app['couchdbs.default']];
			} );

			//	Shortcut to client
			$app['couchdb.client'] = $app->share( function() use( $app )
			{
				return $app['couchdb']->getCouchDBClient();
			} );

			//	Shortcut to dbname
			$app['couchdb.client'] = $app->share( function() use( $app )
			{
				return $app['couchdb']->getCouchDBClient();
			} );

			$app['couchdb.config'] = $app->share( function() use ( $app )
			{
				$_dbs = $app['couchdbs.config'];

				return $_dbs[$app['couchdbs.default']];
			} );

			$app['couchdb.event_manager'] = $app->share( function() use ( $app )
			{
				$_dbs = $app['couchdbs.event_manager'];

				return $_dbs[$app['couchdbs.default']];
			} );

			if ( isset( $app['couchdb.common.class_path'] ) )
			{
				$app['autoloader']->registerNamespace( 'Doctrine\\Common', $app['couchdb.common.class_path'] );
			}
		}
	}
}
