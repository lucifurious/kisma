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

	/**
	 * Kisma Aliases
	 */
	use Kisma\Components as Components;
	use Kisma\Aspects as Aspects;

	//*************************************************************************
	//* Requirements
	//*************************************************************************

	/**
	 * CouchDb
	 * An aspect that wraps the CouchDbClient library for working with a CouchDb instance
	 *
	 * @property \Kisma\Utility\CouchDbClient $db
	 * @property string $designDocumentName Defaults to '_design/document'
	 */
	class CouchDbServiceProvider extends \Kisma\Components\ServiceProvider
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var \Kisma\Utility\CouchDbClient
		 */
		protected $_db = null;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * Registers the service with Silex
		 *
		 * @param \Kisma\Kisma $app
		 */
		public function register( \Kisma\Kisma $app )
		{
			$app['db.couch'] = $app->share( function() use ( $app )
			{
				return new self( $app['db.config'] );
			} );
		}

		/**
		 * @param array $options
		 */
		public function __construct( $options = array() )
		{
			//	Call poppa
			parent::__construct( $options );

			if ( null === $this->_db )
			{
				$this->_db = new \Kisma\Utility\CouchDbClient( $options );
			}
		}

		/**
		 * @param string	 $url
		 * @param array|null $options
		 * @param bool	   $raw
		 *
		 * @return mixed|bool
		 */
		public function get( $url, $options = array(), $raw = false )
		{
			if ( false === ( $_result = $this->_db->get( $url, $options ) ) || !\K::in( $_result->status, 404, 200 ) )
			{
				//	Ruh-roh
				throw new \Kisma\CouchDbException( 'Unexpected CouchDb response: ' . var_export( $_result,
					true ), $_result->status );
			}

			//  Not an array and not an object or raw request? Send back nekkid
			if ( false !== $raw || ( !is_array( $_result ) && !is_object( $_result ) ) )
			{
				return $_result;
			}

			//	Arrays go back as arrays of Document
			if ( is_array( $_result ) && !is_object( $_result ) )
			{
				$_documents = array();

				foreach ( $_result as $_document )
				{
					$_documents[] = new \Kisma\Components\Document( array(
						'document' => $_document,
					) );

					unset( $_document );
				}

				unset( $_result );

				return $_documents;
			}

			//  Single document
			if ( is_object( $_result ) )
			{
				return new \Kisma\Components\Document( array( 'document' => $_result ) );
			}
		}

		//*************************************************************************
		//* Default/Magic Methods
		//*************************************************************************

		/**
		 * Allow calling methods in our CouchDb client directly
		 *
		 * @throws \BadMethodCallException
		 *
		 * @param string $method
		 * @param array  $arguments
		 *
		 * @return mixed
		 */
		public function __call( $method, $arguments )
		{
			//	CouchDbClient pass-through...
			if ( method_exists( $this->_db, $method ) )
			{
				return call_user_func_array( array(
					$this->_db, $method
				), $arguments );
			}

			//	No worky
			throw new \BadMethodCallException( __CLASS__ . '::' . $method . ' is undefined.' );
		}

	}
}
