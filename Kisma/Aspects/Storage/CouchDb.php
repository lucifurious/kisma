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
	require_once \K::glue(
		DIRECTORY_SEPARATOR,
		\K::getSetting( \KismaSettings::BasePath ),
		'vendors',
		'sag',
		'src',
		'Sag.php'
	);
}

/**
 * @namespace Kisma\Aspects\Storage
 */
namespace Kisma\Aspects\Storage
{
	use \string;

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
		 * @var \Kisma\Services\Remote\CouchDbServer An optional server aspect that provides additional functionality
		 */
		protected $_server = null;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * @param \Kisma\Components\Component $linker
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		public function link( \Kisma\Components\Component $linker )
		{
			parent::link( $linker );

			//	Create our server
			if ( false === ( $this->_server = $linker->getAspect( \KismaSettings::CouchDbServer ) ) )
			{
				$this->_server = $linker->linkAspect( \KismaSettings::CouchDbServer, $this->getOptions() );
			}
			else
			{

			}

			//	Instantiate Sag
			$this->_sag = new \Sag( $this->_server->getHostName(), $this->_server->getHostPort() );

			if ( null !== $this->_server->getUserName() && null !== $this->_server->getPassword() )
			{
				$this->_sag->login( $this->_server->getUserName(), $this->_server->getPassword() );
			}

			if ( null !== $this->_server->getDatabaseName() )
			{
				$this->_sag->setDatabase(
					$this->_server->getDatabaseName(),
					$this->getOption( \KismaOptions::CreateIfNotFound, false )
				);
			}

			return $this;
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
				return call_user_func_array(
					array(
						$this->_sag,
						$method
					),
					$arguments
				);
			}

			//	No worky
			throw new \BadMethodCallException( __CLASS__ . '::' . $method . ' is undefined.' );
		}

		/**
		 * Wrapper for \Sag::get() to return a \Kisma\Components\Document
		 * @param string $url
		 * @return \Kisma\Components\Document
		 */
		public function get( $url )
		{
			$_result = $this->_sag->get( $url )->body;

			//	Arrays go back as arrays of Document
			if ( is_array( $_result ) && !is_object( $_result ) )
			{
				$_documents = array();

				foreach ( $_result as $_document )
				{
					$_documents[] = new \Kisma\Components\Document(
						array(
							'document' => $_document,
						)
					);

					unset( $_document );
				}

				unset( $_result );

				return $_documents;
			}

			//	Objects go back as Document
			if ( is_object( $_result ) )
			{
				$_document = new \Kisma\Components\Document();
				return $_document->setDocument( $_result );
			}

			//	No clue...
			return $_result;
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
		//* Event Handlers
		//*************************************************************************

//		/**
//		 * Catch the aspect linked event and set the database name.
//		 *
//		 * We have to set it now because construction
//		 * calls your setters for each property. Aspects are linked last. Therefore any pass-through method calls
//		 * to your aspect will fail.
//		 *
//		 * @param \Kisma\Components\Event $event
//		 * @return bool
//		 */
//		public function onAspectLinked( $event )
//		{
//			/** @var $_aspect \Kisma\Components\Aspect */
//			$_aspect = $event->getEventData();
//
//			//	Create our server
//			if ( null !== $_aspect && $_aspect instanceof ${\KismaSettings::CouchDbClass} == $_aspect->getAspectName() )
//			{
//				if ( false === ( $this->_server = $this->_linker->getAspect( \KismaSettings::CouchDbServer ) ) )
//				{
//					$this->_server = $this->_linker->linkAspect( \KismaSettings::CouchDbServer, $this->getOptions() );
//				}
//
//				//	Instantiate Sag
//				$this->_sag = new \Sag( $this->_server->getHostName(), $this->_server->getHostPort() );
//
//				if ( null !== $this->_server->getUserName() && null !== $this->_server->getPassword() )
//				{
//					$this->_sag->login( $this->_server->getUserName(), $this->_server->getPassword() );
//				}
//
//				if ( null !== $this->_server->getDatabaseName() )
//				{
//					$this->_sag->setDatabase(
//						$this->_server->getDatabaseName(),
//						$this->getOption( \KismaOptions::CreateIfNotFound, false )
//					);
//				}
//			}
//
//			return true;
//		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param \Kisma\Services\Remote\CouchDbServer $server
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		protected function _setServer( $server )
		{
			$this->_server = $server;
			return $this;
		}

		/**
		 * @return \Kisma\Services\Remote\CouchDbServer
		 */
		public function getServer()
		{
			return $this->_server;
		}

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
		protected function _setSag( $sag )
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

	}
}