<?php
/**
 * @file
 * Provides asset management
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Framework
 * @package kisma
 * @since 1.0.0
 *
 * @ingroup framework
 */
namespace Kisma\Provider;

//*************************************************************************
//* Aliases
//*************************************************************************

use Kisma\Kisma;
use Silex\Application;

use Assetic;
use Assetic\Extension\Twig\AsseticExtension as TwigAsseticExtension;

//*************************************************************************
//* Requirements
//*************************************************************************

/**
 * AssetManagerServiceProvider
 */
class AssetManagerServiceProvider extends SilexServiceProvider
{
	/**
	 * @param \Kisma\Kisma|\Silex\Application $app
	 */
	public function register( Application $app )
	{
		$this->setOptions(
			\Kisma\K::app(
				'assetic.options',
				array(
					'debug' => false,
				)
			)
		);

		//	Register the service
		$app['assetic'] = $app->share( function () use ( $app )
		{
			//	Initialize lazy asset manager
			if ( isset( $this->formulae ) && !is_array( $this->formulae ) && !empty( $this->formulae ) )
			{
				$app['assetic.lazy_asset_manager'];
			}

			return $app['assetic.factory'];
		} );

		/**
		 * Factory
		 *
		 * @return Assetic\Factory\AssetFactory
		 */
		$app['assetic.factory'] = $app->share( function() use ( $app )
		{
			$_options = $app['assetic.options'];

			$_factory = new \Assetic\Factory\AssetFactory( $app['assetic.path_to_web'], $_options['debug'] );
			$_factory->setAssetManager( $app['assetic.asset_manager'] );
			$_factory->setFilterManager( $app['assetic.filter_manager'] );

			return $_factory;
		} );

		/**
		 * Outputs all lazy asset manager and asset managers assets
		 */
		$app->after( function() use ( $app )
		{
			$app['assetic.asset_writer']->writeManagerAssets( $app['assetic.lazy_asset_manager'] );
			$app['assetic.asset_writer']->writeManagerAssets( $app['assetic.asset_manager'] );
		} );

		/**
		 * Asset writer, writes to the 'assetic.path_to_web' folder
		 *
		 * @return Assetic\AssetWriter
		 */
		$app['assetic.asset_writer'] = $app->share( function () use ( $app )
		{
			return new AssetWriter( $app['assetic.path_to_web'] );
		} );

		/**
		 * Asset manager, can be accessed via $app['assetic.asset_manager']
		 * and can be configured via $app['assetic.assets'], just provide a
		 * protected callback $app->protect(function($am) { }) and add
		 * your assets inside the function to asset manager ($am->set())
		 */

		$app['assetic.asset_manager'] = $app->share( function () use ( $app )
		{
			/** @var $_assets callback */
			$_assets = Kisma::app( 'assetic.assets', function()
			{
			} );
			$_manager = new Assetic\AssetManagerServiceProvider();

			call_user_func_array( $_assets, array( $_manager, $app['assetic.filter_manager'] ) );

			return $_manager;
		} );

		/**
		 * Filter manager, can be accessed via $app['assetic.filter_manager']
		 * and can be configured via $app['assetic.filters'], just provide a
		 * protected callback $app->protect(function($fm) { }) and add
		 * your filters inside the function to filter manager ($fm->set())
		 */
		$app['assetic.filter_manager'] = $app->share( function () use ( $app )
		{
			$_filters = Kisma::app( 'assetic.filters', function()
			{
			} );
			$_manager = new FilterManager();

			call_user_func_array( $_filters, array( $_manager ) );

			return $_manager;
		} );

		/**
		 * Lazy asset manager for loading assets from $app['assetic.formulae']
		 * (will be later maybe removed)
		 */
		$app['assetic.lazy_asset_manager'] = $app->share( function () use ( $app )
		{
			$_formulae = Kisma::app( 'assetic.formulae', array() );
			$_options = $app['assetic.options'];
			$_assMan = new LazyAssetManagerServiceProvider( $app['assetic.factory'] );

			if ( empty( $_formulae ) )
			{
				return $_assMan;
			}

			foreach ( $_formulae as $_name => $_formula )
			{
				$_assMan->setFormula( $_name, $_formula );
			}

			if ( null !== $_options['formulae_cache_dir'] && true !== $_options['debug'] )
			{
				foreach ( $_assMan->getNames() as $_name )
				{
					$_assMan->set(
						$_name,
						new AssetCache(
							$_assMan->get( $_name ),
							new FilesystemCache( $_options['formulae_cache_dir'] )
						)
					);
				}
			}

			return $_assMan;
		} );

		$app->before( function () use ( $app )
		{
			// twig support
			if ( isset( $app['twig'] ) )
			{
				$app['twig']->addExtension( new TwigAsseticExtension( $app['assetic.factory'] ) );
			}
		} );

		// autoloading the assetic library
		if ( isset( $app['assetic.class_path'] ) )
		{
			$app['autoloader']->registerNamespace( 'Assetic', $app['assetic.class_path'] );
		}
	}
}
