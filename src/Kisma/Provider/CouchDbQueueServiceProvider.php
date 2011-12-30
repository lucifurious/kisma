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
	/**
	 * CouchDbQueueServiceProvider
	 * A provider that wraps the CouchDbClient library for working with a CouchDb instance
	 */
	class CouchDbQueueServiceProvider extends CouchDbQueueServiceProvider
	{
		/**
		 * Registers the service with Silex
		 *
		 * @param \Silex\Application $app
		 */
		public function register( \Silex\Application $app )
		{
			parent::register( $app );

			$app['couchdb.queues'] = $app->share( function() use( $app )
			{
				$_queues = isset( $app['couchdb.queues'] ) ? $app['couchdb.queues'] : array();



				return $_queues;
			} );
		}
	}
}
