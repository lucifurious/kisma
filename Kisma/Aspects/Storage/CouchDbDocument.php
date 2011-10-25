<?php
/**
 * CouchDbDocument.php
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link	  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license   http://github.com/Pogostick/kisma/licensing/
 * @author	Jerry Ablan <kisma@pogostick.com>
 * @category  Kisma_Components
 * @package   kisma.components
 * @namespace \Kisma\Components
 * @since	 v1.0.0
 * @filesource
 */
namespace Kisma\Storage;
{
	//*************************************************************************
	//* Requirements 
	//*************************************************************************

	use \Kisma\Utility as Utility;

	/**
	 * Document
	 * A magical bare-bones CouchDB document wrapper. Read and write document properties just like using
	 * a \stdClass. _id and _rev had dedicated getters and setters.
	 *
	 * In addition, IF you create a getter or setter for a property, it will always be called. This allows
	 * you to enforce property restrictions in a single place.
	 *
	 * @property array|\stdClass $document
	 *
	 * @property string $id The document _id
	 * @property string $rev The document _rev
	 */
	class CouchDbDocument extends \Kisma\Components\Document
	{
		//*************************************************************************
		//* Constants
		//*************************************************************************

		/**
		 * @var string
		 */
		const DefaultContentType = 'application/octet-stream';

		//*************************************************************************
		//* Private Members 
		//*************************************************************************

		/**
		 * @var \Kisma\Services\Remote\CouchDbServer
		 */
		protected $_dbServer = null;
		/**
		 * @var \Kisma\Aspects\Storage\CouchDb
		 */
		protected $_db = null;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * @param array $options
		 * @return \Kisma\Storage\CouchDbDocument
		 */
		public function __construct( $options = array() )
		{
			parent::__construct( $options );

			//	Add CouchDbServer first (cuz it can feed next aspect)
			if ( !$this->hasAspect( \KismaSettings::CouchDbServer ) )
			{
				$this->_dbServer = $this->linkAspect( \KismaSettings::CouchDbServer, $options );
			}

			//	Add CouchDb service
			if ( !$this->hasAspect( \KismaSettings::CouchDbClass ) )
			{
				$this->_db = $this->linkAspect( \KismaSettings::CouchDbClass, $options );
			}
		}

		/**
		 * Saves the current document if $couchDb is set
		 * @return mixed
		 */
		public function save()
		{
			if ( null !== $this->_db )
			{
				return false;
			}

			$this->setDocument();
			$this->setId( $this->getDb()->generateIDs( 1 )->uuids[0] );

			return $this->put( $this->getId(), $this->getDocument() );
		}

		/**
		 * Delete document from DB if $couchDb is set
		 * @return mixed
		 */
		public function delete()
		{
			return $this->_db->delete( $this->getId(), $this->getRev() );
		}

		/**
		 * Add an attachment if $couchDb is set
		 * @param string $name
		 * @param string $data
		 * @param string $contentType
		 * @param string $rev
		 * @return \Kisma\Components\CouchDbDocument
		 */
		public function addAttachment( $name, $data, $contentType = self::DefaultContentType, $rev = null )
		{
			return $this->_db->setAttachment( $name, $data, $contentType, $this->getId(), $rev );
		}

		/**
		 * Loads our internal document from an external one
		 * @param array|\stdClass $document
		 */
		public function loadDocument( $document )
		{
			foreach ( $document as $_property => $_value )
			{
				$this->__set( $_property, $_value );
			}
		}

		//*************************************************************************
		//* Event Handlers
		//*************************************************************************

		/**
		 * Catch the aspect linked event and set the database name.
		 *
		 * We have to set it now because construction
		 * calls your setters for each property. Aspects are linked last. Therefore any pass-through method calls
		 * to your aspect will fail.
		 *
		 * @param \Kisma\Components\Event $event
		 * @return bool
		 */
		public function onAspectLinked( $event )
		{
			/** @var $_aspect \Kisma\Components\Aspect */
			$_aspect = $event->getEventData();

			if ( null !== $_aspect && \KismaSettings::CouchDbClass == $_aspect->getAspectName() )
			{
				//	Call the CouchDb aspect's setDatabase() method
				$this->setDatabaseName( $this->_dbServer->getDatabaseName() );
			}

			return true;
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param \Kisma\Aspects\Storage\CouchDb $db
		 * @return $this
		 */
		public function setDb( $db )
		{
			$this->_db = $db;
			return $this;
		}

		/**
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		public function getDb()
		{
			return $this->_db;
		}

		/**
		 * @param \Kisma\Services\Remote\CouchDbServer $dbServer
		 * @return $this
		 */
		public function setDbServer( $dbServer )
		{
			$this->_dbServer = $dbServer;
			return $this;
		}

		/**
		 * @return \Kisma\Services\Remote\CouchDbServer
		 */
		public function getDbServer()
		{
			return $this->_dbServer;
		}

	}
}