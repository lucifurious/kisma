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
		 * Checks if the server is alive
		 *
		 * @return bool|mixed
		 */
		public function serverExists()
		{
			try
			{
				return ( 200 === $this->head( '//' )->status );
			}
			catch ( \Exception $_ex )
			{
				return false;
			}
		}

		/**
		 * @param string $databaseName
		 * @param bool $createIfNotFound
		 * @return bool|mixed
		 * @throws \Teledini\Exceptions\StorageException
		 */
		public function databaseExists( $databaseName, $createIfNotFound = true )
		{
			//	No server? no worky!
			if ( !$this->serverExists() )
			{
				return false;
			}

			//	Get the database
			$_databaseName = '//' . trim( $databaseName, '/ ' );
			$_response = $this->get( $_databaseName );

			if ( 200 !== $_response->status )
			{
				if ( 404 !== $_response->status )
				{
					//	Problems...
					throw new \Kisma\CouchDbException( 'Unexpected status "' . $_response->status . '"', $_response->status, null, $_response );
				}

				//	Create the database...
				$_response = $this->put( $_databaseName );

				if ( isset( $_response->error ) || true !== $_response->body->ok )
				{
					throw new \Kisma\CouchDbException( 'Error creating database', $_response->status, null, $_response );
				}

				$this->_createDesignDocument();
				return $_response;
			}

			return true;
		}

		/**
		 * Non-Exception generating method to check the existence of a document. If found, it is returned. False is returned otherwise.
		 *
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
		 *
		 * @return mixed
		 */
		public function getRevsLimit()
		{
			return $this->get( '_revs_limit' );
		}

		/**
		 * Returns a list of changes made to documents in the database.
		 *
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
		 *
		 * @param string $viewName
		 * @param string|array|null $startKey
		 * @param null|string|array $endKey
		 * @param array $options
		 * @return string
		 */
		public function getView( $viewName, $startKey = null, $endKey = null, $options = array() )
		{
			if ( null !== $startKey && null === $endKey )
			{
				$_keys = array(
					'key' => '"' . $startKey . '"',
				);
			}
			else
			{
				$_keys = array(
					'startKey' => $this->_makeViewKey( $startKey ),
					'endKey' => $this->_makeViewKey( $endKey ),
				);
			}

			//	Build the query
			$_query = null;

			//	Start/end?
			foreach ( $_keys as $_keyName => $_keyValue )
			{
				if ( null !== $_keyValue )
				{
					$_query .= '&' . $_keyName . '=' . $_keyValue;
				}
			}

			//	Add in any options
			foreach ( $options as $_key => $_value )
			{
				$_query .= '&' . $_key . '=' . $_value;
			}

			$_query = trim( $_query, '&' );

			Log::trace( 'Getting view: ' . $viewName . ( false === strpos( $viewName,
				'?' ) ? '?' . $_query : '&' . $_query ) );

			return $this->get( $viewName . ( false === strpos( $viewName, '?' ) ? '?' . $_query : '&' . $_query ) );
		}

		/**
		 * @param array|string $key
		 * @param bool $encode
		 * @return null|string
		 */
		protected function _makeViewKey( $key, $encode = false )
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

			if ( $encode )
			{
				$_key = urlencode( $_key );
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

		/**
		 * Bulk document storage
		 *
		 * @param array $documents
		 * @param bool $allOrNothing
		 * @return bool|mixed
		 */
		public function bulk( $documents = array(), $allOrNothing = false )
		{
			$_payload = new \stdClass();
			$_payload->all_or_nothing = $allOrNothing;
			$_payload->docs = $documents;

			return $this->post( '/_bulk_docs', json_encode( $_payload ) );
		}

		/**
		 * Copy a document
		 *
		 * @param string $fromId
		 * @param string $targetId
		 * @param string $targetRev
		 * @return mixed
		 */
		public function copy( $fromId, $targetId, $targetRev = null )
		{
			return $this->_httpRequest( \Kisma\HttpMethod::Copy, $fromId, array(), array(
					CURLOPT_HTTPHEADER => 'Destination: ' . $targetId . ( $targetRev ? '?rev=' . $targetRev : null ),
				) );
		}

		//*************************************************************************
		//* Private Methods
		//*************************************************************************

		/**
		 * Adds the current database name to the front of the url unless the url starts with two slashes.
		 *
		 * Example:
		 *
		 *  $url = '12345'		becomes				$url = '/[$this->_databaseName]/12345'
		 *  $url = '//database_awesome/12345'		$url = '/database_awesome/12345'
		 *
		 * @param \Kisma\HttpMethod|string $method
		 * @param string $url
		 * @param array $payload
		 * @param array $options
		 * @return bool|\stdClass
		 */
		protected function _httpRequest( $method = \Kisma\HttpMethod::Get, $url, $payload = array(), $options = array() )
		{
			$_response = false;

			//	Add database name if necessary
			if ( false !== ( $_noDatabaseName = ( '//' == substr( $url, 0, 2 ) ) ) )
			{
				$url = '/' . ltrim( $url, '/' );
			}
			else
			{
				$url = '/' . $this->_databaseName . '/' . ltrim( $url, ' /' );
			}

			//	Construct the url
			$_url = 'http://' . $this->_hostName . ( $this->_hostPort ? ':' . $this->_hostPort : null ) . $url;

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
		 *
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

		/**
		 * Constructs and sets all options at once.
		 *
		 * @param array $options
		 *
		 * Options are:
		 *
		 * Name				Default
		 * -------------	   -------------
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
			// Client factory...
			$_client = new self();
			$_client->setUserName( \K::o( $options, 'user_name', null, true ) );
			$_client->setPassword( \K::o( $options, 'password', null, true ) );
			$_client->setHostName( \K::o( $options, 'host_name', 'localhost', true ) );
			$_client->setHostPort( \K::o( $options, 'host_port', 5984, true ) );
			$_client->setDatabaseName( \K::o( $options, 'database_name', null, true ),
				\K::o( $options, 'create_if_not_found', true, true ) );

			return $_client;
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param string $databaseName
		 * @param bool $createIfNotFound
		 * @return \Kisma\Utility\CouchDbClient
		 */
		public function setDatabaseName( $databaseName, $createIfNotFound = true )
		{
			$this->_databaseName = $databaseName;
			$this->databaseExists( $databaseName, $createIfNotFound );
			return $this;
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
			return $this;
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