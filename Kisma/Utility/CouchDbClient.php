<?php
/**
 * CouchDbClient.php
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
 * @package	   kisma.utility
 * @since		 v1.0.0
 * @filesource
 */
namespace Kisma\Utility
{
	//*************************************************************************
	//* Requirements 
	//*************************************************************************

	/**
	 * CouchDbClient
	 * A generic HTTP class
	 */
	class CouchDbClient extends Http
	{
		//*************************************************************************
		//* Constants
		//*************************************************************************

		/**
		 * @const string
		 */
		const DesignDocumentName = '_design/document';

		//*************************************************************************
		//* Private Members 
		//*************************************************************************

		/**
		 * @var string
		 */
		protected $_hostName = null;
		/**
		 * @var string
		 */
		protected $_databaseName = null;

		//*************************************************************************
		//* Public Methods 
		//*************************************************************************

		/**
		 * @param $databaseName
		 * @param bool $createIfNotFound
		 * @return bool|mixed
		 * @throws \Teledini\Exceptions\StorageException
		 */
		public function setDatabase( $databaseName, $createIfNotFound = true )
		{
			$this->setDatabaseName( $databaseName );

			if ( $createIfNotFound )
			{
				if ( false !== ( $_response = $this->head( '/' ) ) )
				{
					if ( 404 == $_response->status )
					{
						$_databaseName = '/' . trim( $databaseName, '/ ' );
						$this->put( '/', $_databaseName );
						$this->_createDesignDocument();
						return true;
					}

					if ( 200 == $_response->status )
					{
						return true;
					}

					//	Not sure what else would constitute acceptance...
					throw new \Teledini\Exceptions\StorageException( 'Unexpected response from CouchDb: ' . var_export( $_response, true ), $_response->status );
				}

				return true;
			}

			return false;
		}

		/**
		 * Non-Exception generating method to check the existence of a document. If found, it is returned. False is returned otherwise.
		 * @param string $id
		 * @param bool $returnObject
		 * @return false|\Kisma\Storage\CouchDbDocument
		 */
		public function documentExists( $id, $returnObject = false )
		{
			if ( false === $returnObject )
			{
				return ( '200' == $this->head( $id )->status );
			}

			$_document = $this->get( $id );
			return empty( $_document ) ? false : $_document;

		}

		/**
		 * Returns the upper bound of document revisions
		 * @return mixed
		 */
		public function getRevsLimit()
		{
			return $this->get( '_revs_limit' );
		}

		/**
		 * Returns a list of changes made to documents in the database.
		 * @param array $options
		 * @return mixed
		 */
		public function changes( $options = array() )
		{
			$_query = null;
			$_options = array();

			foreach ( $options as $_key => $_value )
			{
				switch ( $_key = strtolower( $_key ) )
				{
					case 'since':
					case 'limit':
					case 'feed':
					case 'heartbeat':
					case 'timeout':
					case 'filter':
					case 'include_docs':
						$_options[] = $_key . '=' . urlencode( $_value );
						break;
				}
			}

			if ( !empty( $_options ) )
			{
				$_query = '?' . trim( implode( '&', $_options ), '&' );
			}

			return $this->get( '_changes' . $_query );
		}

		/**
		 * Builds an url to a view with optional keys and url encoding and GETs it.
		 * @param string $viewName
		 * @param null|string|array $key
		 * @param null|string|array $endKey
		 * @param bool $urlEncode
		 * @return string
		 */
		public function getView( $viewName, $key = null, $endKey = null, $urlEncode = true )
		{
			$_query = 'key=%%startKey%%';
			$_startKey = $this->_makeViewKey( $key );
			$_endKey = $this->_makeViewKey( $endKey );

			//	Start/end?
			if ( null === $_startKey )
			{
				$_query = null;
			}
			else if ( null !== $endKey )
			{
				$_query = 'startkey=%%startKey%%&endkey=%%endKey%%';
			}

			$_query = str_ireplace(
				array(
					'%%startKey%%',
					'%%endKey%%',
				),
				array(
					$_startKey, //( true === $urlEncode ? urlencode( $_startKey ) : $_startKey ),
					$_endKey, //( true === $urlEncode ? urlencode( $_endKey ) : $_endKey ),
				),
				$_query
			);

			return $this->get(
				$viewName . ( false === strpos( $viewName, '?' ) ? '?' . $_query : '&' . $_query )
			);
		}

		/**
		 * @param array|string $key
		 * @return null|string
		 */
		protected function _makeViewKey( $key )
		{
			$_key = null;

			if ( null !== $key )
			{
				//	Make a string from the complex array key
				if ( is_array( $key ) )
				{
					$_key = null;

					foreach ( $key as $_value )
					{
						if ( '{}' == $_value )
						{
							$_key .= ',{}';
						}
						else
						{
							$_key .= '"' . $_value . '"';
						}
					}

					$_key = '[' . trim( $_key, ' ,' ) . ']';
				}
				else
				{
					$_key = '"' . $_key . '"';
				}
			}

			return $_key;
		}

		/**
		 * Get an attachment by id and name
		 *
		 * @param string $id
		 * @param string $fileName
		 * @return mixed
		 */
		public function getAttachment( $id, $fileName )
		{
			return $this->get( '/' . $id . '/' . urlencode( $fileName ) );
		}

		//*************************************************************************
		//* Private Methods
		//*************************************************************************

		/**
		 * @param \Kisma\HttpMethod|string $method
		 * @param string $url
		 * @param array $payload
		 * @param array $options
		 * @return bool|\stdClass
		 */
		protected function _httpRequest( $method = \Kisma\HttpMethod::Get, $url, $payload = array(), $options = array() )
		{
			$_response = false;
			if ( false !== ( $_noDatabaseName = ( substr( $url, 0, 2 ) == '//' ) ) )
			{
				$url = '/' . ltrim( $url, '/' );
			}

			$_url = 'http://' .
				$this->_hostName . ( $this->_hostPort ? ':' . $this->_hostPort : null ) .
				( false === $_noDatabaseName ? '/' . $this->_databaseName : null ) . '/' .
				trim( $url, '/ ' );

			if ( false !== ( $_result = parent::_httpRequest( $method, $_url, $payload, $options ) ) )
			{
				$_response = new \stdClass();
				$_response->info = $this->_info;
				$_response->error = $this->_error;
				$_response->status = $this->_info['http_code'];
				$_response->body = json_decode( $_result );
			}

			return $_response;
		}

		/**
		 * @return bool
		 */
		protected function _hasDesignDocument()
		{
			return $this->documentExists( self::DesignDocumentName );
		}

		/**
		 * Creates our design document
		 * @return bool
		 */
		protected function _createDesignDocument()
		{
			if ( $this->_hasDesignDocument() )
			{
				return true;
			}

			//	Build the design document
			$_doc = new \stdClass();
			$_doc->_id = self::DesignDocumentName;

			$_doc->views = new \stdClass();
			$_doc->views->by_date = new \stdClass();
			$_doc->views->by_date->map = 'function( doc ) { emit(doc.create_time, doc); }';

			try
			{
				//	Store it
				$this->put( $_doc->_id, $_doc );
			}
			catch ( \Exception $_ex )
			{
				if ( 404 == $_ex->getCode() )
				{
					//	No database, rethrow
					throw $_ex;
				}

				/**
				 * Conflict-o-rama!
				 */
				if ( 409 == $_ex->getCode() )
				{
					//	I guess we don't care...
					return true;
				}
			}

			return false;
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * Constructs and sets all options at once.
		 *
		 * @param array $options
		 *
		 * Options are:
		 *
		 * Name                Default
		 * -------------       -------------
		 * host_name		   localhost
		 * host_port		   5984
		 * user_name		   null
		 * password			   null
		 * database_name	   null
		 *
		 * @return \Kisma\Utility\CouchDbClient
		 */
		public static function create( &$options = array() )
		{
			$_client = new self();
			$_client->setUserName( \K::o( $options, 'user_name', null, true ) );
			$_client->setPassword( \K::o( $options, 'password', null, true ) );
			$_client->setHostName( \K::o( $options, 'host_name', 'localhost', true ) );
			$_client->setHostPort( \K::o( $options, 'host_port', 5984, true ) );
			$_client->setDatabase(
				\K::o( $options, 'database_name', null, true ),
				\K::o( $options, 'create_if_not_found', true, true )
			);

			return $_client;
		}

		/**
		 * @param string $databaseName
		 */
		public function setDatabaseName( $databaseName )
		{
			$this->_databaseName = $databaseName;
		}

		/**
		 * @return string
		 */
		public function getDatabaseName()
		{
			return $this->_databaseName;
		}

		/**
		 * @param string $hostName
		 */
		public function setHostName( $hostName )
		{
			$this->_hostName = $hostName;
		}

		/**
		 * @return string
		 */
		public function getHostName()
		{
			return $this->_hostName;
		}

	}
}