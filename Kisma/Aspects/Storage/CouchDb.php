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

/**
 * Global namespace declarations
 */
namespace
{
	//*************************************************************************
	//* Requirements
	//*************************************************************************

	/**
	 *  Require the Sag library, but keep it outside of Kisma
	 * @todo Finish up the alias system and replace this nonsense
	 */
	/** @noinspection PhpIncludeInspection */
	require_once \K::glue( DIRECTORY_SEPARATOR, \K::getSetting( \KismaSettings::BasePath ), 'vendors', 'sag', 'src', 'Sag.php' );
}

/**
 * @namespace Kisma\Aspects\Storage
 */
namespace Kisma\Aspects\Storage
{
	//*************************************************************************
	//* Aliases
	//*************************************************************************

	/**
	 * Kisma Aliases
	 */
	use Kisma\Components as Components;
	use Kisma\Aspects as Aspects;
	use \Kisma\Extensions\Davenport\Utility\CouchHelper;

	//*************************************************************************
	//* Requirements
	//*************************************************************************

	/**
	 * CouchDb
	 * An aspect that wraps the Sag library for working with a CouchDb instance
	 *
	 * @property \Kisma\Services\Remote\CouchDbServer $server
	 * @property \Sag $sag
	 * @property string $designDocumentName Defaults to '_design/document'
	 *
	 * Sag Methods
	 * ===========
	 * @method mixed login( $user, $pass, $type = null )
	 * @method \stdClass getSession()
	 * @method \Sag decode( $decode )
	 * @method mixed head( $url )
	 * @method mixed delete( $id, $rev )
	 * @method mixed put( $id, $data )
	 * @method mixed post( $data, $path = null )
	 * @method mixed bulk( $docs, $allOrNothing = false )
	 * @method mixed copy( $sourceId, $destinationId, $destinationRev )
	 * @method \Sag setDatabase( $databaseName, $createIfNotFound = false )
	 * @method mixed getAllDocs( $incDocs = false, $limit = null, $startKey = null, $endKey = null, $keys = null )
	 * @method mixed getAllDatabases()
	 * @method mixed generateIDs()
	 * @method mixed createDatabase( $databaseName )
	 * @method mixed deleteDatabase( $databaseName )
	 * @method mixed replicate( $src, $target, $continuous = false, $createTarget = null, $filter = null, $filterQueryParams = null )
	 * @method mixed compact( $viewName = null )
	 * @method mixed setAttachment( $name, $data, $contentType, $docID, $rev = null )
	 * @method \Sag setOpenTimeout( $seconds )
	 * @method \Sag setRWTimeout( $seconds, $microSeconds = 0 )
	 * @method \Sag setCache( &$cacheImplementer )
	 * @method \SagCache getCache()
	 * @method string currentDatabase()
	 * @method \stdClass getStats()
	 * @method \Sag setStaleDefault( $stale )
	 * @method \Sag setCookie( $key, $value )
	 * @method string getCookie( $key )
	 */
	class CouchDb extends Components\Aspect implements \Kisma\IStorage
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var \Sag
		 */
		protected $_sag = null;
		/**
		 * @var string When opening a database, you have the option of having a design document created for you. If
		 * you do so, it will be called this
		 */
		protected $_designDocumentName = '_design/document';
		/**
		 * @var string The name of the database
		 */
		protected $_databaseName = null;
		/**
		 * @var string
		 */
		protected $_hostName = 'localhost';
		/**
		 * @var int
		 */
		protected $_hostPort = 5984;
		/**
		 * @var string
		 */
		protected $_userName = null;
		/**
		 * @var string
		 */
		protected $_password = null;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * @param array $options
		 */
		public function __construct( $options = array() )
		{
			//	Initialize Sag
			if ( null === ( $this->_sag = \K::o( $options, 'sag', null, true ) ) )
			{
				$this->_sag = CouchHelper::getSagClient( $options );
			}

			//	Call poppa
			parent::__construct( $options );

			//	Create a design document if it's not there...
			$this->_createDesignDocument();
		}

		/**
		 * Non-Exception generating method to check the existence of a document. If found, it is returned. False is returned otherwise.
		 * @param string $id
		 * @param bool $returnObject
		 * @return false|\Kisma\Storage\CouchDbDocument
		 */
		public function documentExists( $id, $returnObject = false )
		{
			try
			{
				if ( false === $returnObject )
				{
					return ( '200' == $this->head( urlencode( $id ) )->headers->_HTTP->status );
				}

				$_document = $this->get( urlencode( $id ), $returnObject );
				return empty( $_document ) ? false : $_document;
			}
			catch ( \SagCouchException $_ex )
			{
				if ( 404 == $_ex->getCode() )
				{
					//	Not found
					return false;
				}

				//	Pssst... pass it on
				throw $_ex;
			}
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

		//*************************************************************************
		//* Default/Magic Methods
		//*************************************************************************

		/**
		 * Allow calling Aspect methods from the object
		 *
		 * @throws \BadMethodCallException
		 * @param string $method
		 * @param array  $arguments
		 * @return mixed
		 */
		public function __call( $method, $arguments )
		{
			//	Sag pass-through...
			if ( method_exists( $this->_sag, $method ) )
			{
				return call_user_func_array( array(
					$this->_sag,
					$method
				), $arguments );
			}

			//	No worky
			throw new \BadMethodCallException( __CLASS__ . '::' . $method . ' is undefined.' );
		}

		/**
		 * Wrapper for \Sag::get() to return a \Kisma\Components\Document
		 * @param string $url
		 * @param bool $returnObject
		 * @return \Kisma\Components\Document|\stdClass|false|\Kisma\Components\Document[]|\stdClass[]
		 */
		public function get( $url, $returnObject = false )
		{
			try
			{
				$_result = $this->_sag->get( $url );

				if ( !\K::in( $_result->headers->_HTTP->status, '200', '201' ) )
				{
					throw new \Exception( 'Couldn\'t find doc.' );
				}
			}
			catch ( \SagCouchException $_ex )
			{
				if ( 404 != $_ex->getCode() )
				{
					//	Not not found is not cool
					throw $_ex;
				}

				//	Not found
				return false;
			}

			//	Arrays go back as arrays of Document
			if ( is_array( $_result ) && !is_object( $_result ) )
			{
				$_documents = array();

				foreach ( $_result as $_document )
				{
					if ( false === $returnObject )
					{
						$_documents[] = new \Kisma\Components\Document(
							array(
								'document' => $_document,
							)
						);
					}
					else
					{
						$_documents[] = $_document;
					}

					unset( $_document );
				}

				unset( $_result );

				return $_documents;
			}

			//	Objects go back as Document
			if ( is_object( $_result ) && false === $returnObject )
			{
				$_document = new \Kisma\Components\Document();
				return $_document->setDocument( $_result );
			}

			//	No clue...
			return $_result;
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
		 * Initializes Sag
		 * @param array $options
		 */
		protected function _initializeClient( &$options = array() )
		{
			//	Nada
		}

		/**
		 * @return bool
		 * @throws \SagCouchException
		 */
		protected function _hasDesignDocument()
		{
			try
			{
				//	See if it's there...
				return ( '200' == $this->_sag->head( urlencode( $this->_designDocumentName ) )->headers->_HTTP->status );
			}
			catch ( \SagCouchException $_ex )
			{
				if ( 404 != $_ex->getCode() )
				{
					//	Not not found
					throw $_ex;
				}
			}

			return false;
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
			$_doc->_id = $this->_designDocumentName;

			$_doc->views = new \stdClass();

			$_doc->views->by_date = new \stdClass();
			$_doc->views->by_date->map = 'function( doc ) { emit(doc.create_time.getTime(), doc); }';

			try
			{
				//	Store it
				$this->_sag->put( urlencode( $_doc->_id ), $_doc );
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
		 * @param string $designDocumentName
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		public function setDesignDocumentName( $designDocumentName )
		{
			$this->_designDocumentName = $designDocumentName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDesignDocumentName()
		{
			return $this->_designDocumentName;
		}

		/**
		 * @param \Sag $sag
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		public function setSag( $sag )
		{
			$this->_sag = $sag;
			return $this;
		}

		/**
		 * @return \Sag
		 */
		public function getSag()
		{
			return $this->_sag;
		}

		/**
		 * @param string $databaseName
		 * @param bool $createIfNotFound
		 * @return CouchDb
		 */
		public function setDatabase( $databaseName, $createIfNotFound = false )
		{
			return $this->setDatabaseName( $databaseName, $createIfNotFound );
		}

		/**
		 * @param string $databaseName
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		public function setDatabaseName( $databaseName = null, $createIfNotFound = false )
		{
			if ( null !== $this->_sag && null !== ( $this->_databaseName = $databaseName ) )
			{
				$this->_sag->setDatabase( $this->_databaseName, $createIfNotFound );
				$this->_createDesignDocument();
			}

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
		 * @return \Kisma\Aspects\Storage\CouchDb
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

		/**
		 * @param int $hostPort
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		public function setHostPort( $hostPort )
		{
			$this->_hostPort = $hostPort;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getHostPort()
		{
			return $this->_hostPort;
		}

		/**
		 * @param string $password
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		public function setPassword( $password )
		{
			$this->_password = $password;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getPassword()
		{
			return $this->_password;
		}

		/**
		 * @param string $userName
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		public function setUserName( $userName )
		{
			$this->_userName = $userName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getUserName()
		{
			return $this->_userName;
		}

	}
}
