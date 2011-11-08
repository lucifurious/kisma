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
namespace Kisma\Components;
{
	//*************************************************************************
	//* Requirements
	//*************************************************************************

	/**
	 * CouchDbDocument
	 * Building upon Document, this adds a storage component tied to CouchDb
	 * _id and _rev had dedicated getters and setters.
	 *
	 * Also offers some more common storage methods like find, save, and delete.
	 *
	 * @property string $id The document _id
	 * @property string $rev The document _rev
	 */
	class CouchDbDocument extends Document
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
		 * @var string
		 */
		protected $_keyPrefix = null;
		/**
		 * @var \Sag
		 */
		protected $_db = null;
		/**
		 * @var bool If true, keys (after prefix) will be hashed
		 */
		protected $_hashKeys = false;
		/**
		 * @var bool If true, the _id will be set by if found to be null
		 */
		protected $_autoSetId = false;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * Get a document from storage
		 * @param string $id
		 * @param bool $createIfNotFound If true, an empty document will be returned if the $id does not exist.
		 * @return \stdClass|null
		 */
		public function find( $id, $createIfNotFound = true )
		{
			$_key = null;

			if ( null === $this->_db )
			{
				throw new \Kisma\StorageException( 'No database specified. Cannot find.' );
			}

			//	Hash if requested
			if ( false !== $this->_hashKeys )
			{
				$_key = \Kisma\Utility\Hash::hash( $id, \Kisma\HashType::SHA1, 40 );
			}

			//	Add key prefix is missing...
			if ( null !== $this->_keyPrefix && false === strpos( $id, $this->_keyPrefix, 0 ) )
			{
				$_key = $this->_keyPrefix . ':' . $id;
			}

			if ( null === $_key )
			{
				$_key = $id;
			}

			try
			{
				//	Pull a fresh doc
				$this->trigger( 'before_find', $this->_document );

				$_dbDocument = $this->_db->get( urlencode( $_key ) );

				if ( '200' != $_dbDocument->headers->_HTTP->status )
				{
					//	Something icky here
					throw new \Teledini\Exceptions\StorageException( 'Unable to determine if user exists!' );
				}

				$this->setDocument( $_dbDocument->body );
				\Kisma\Utility\Log::trace( 'Found document "' . $id . ' with id: ' . $_key );

				$this->trigger( 'after_find', $this->_document );
			}
			catch ( \SagCouchException $_ex )
			{
				//	Something icky happened...
				if ( 404 != $_ex->getCode() )
				{
					throw $_ex;
				}

				//	Thrown when document not found
				if ( 404 == $_ex->getCode() && false === $createIfNotFound )
				{
					return null;
				}

				\Kisma\Utility\Log::trace( 'Creating document id "' . $id . '": ' . $_key );

				//	Create a new document and assign the _id
				$this->setDocument();
				$this->setId( $_key );
				$this->save();
			}

			//	Return the document
			return $this->getDocument();
		}

		/**
		 * Saves the current document if $couchDb is set
		 * @return mixed
		 */
		public function save()
		{
			if ( null === $this->_db )
			{
				throw new \Kisma\StorageException( 'No database specified. Cannot save.' );
			}

			if ( null !== $this->_document )
			{
				if ( !isset( $this->_document->_id ) )
				{
					if ( false === $this->_autoSetId )
					{
						throw new \Teledini\Exceptions\StorageException( 'Required document field "_id" missing.' );
					}

					$this->_document->_id = $this->_db->generateIDs( 1 )->body->uuids[0];
				}

				//	Hash if requested
				if ( false !== $this->_hashKeys )
				{
					$this->_document->_id = \Kisma\Utility\Hash::hash( $this->_document->_id, \Kisma\HashType::SHA1, 40 );
				}

				//	Add key prefix is missing...
				if ( null !== $this->_keyPrefix && false === strpos( $this->_document->_id, $this->_keyPrefix, 0 ) )
				{
					$this->_document->_id = $this->_keyPrefix . ':' . $this->_document->_id;
				}

				$this->trigger( 'before_save', $this->_document );

				$_result = $this->_db->put( urlencode( $this->_document->_id ), $this->_document );

				$this->trigger( 'after_save', $this->_document );

				return $_result;
			}

			return false;
		}

		/**
		 * Delete document from DB if $couchDb is set
		 * @return mixed
		 */
		public function delete()
		{
			if ( null !== $this->_db )
			{
				throw new \Kisma\StorageException( 'No database specified. Cannot delete.' );
			}

			$this->trigger( 'before_delete', $this->_document );
			$_result = $this->_db->delete( urlencode( $this->getId() ), $this->getRev() );
			$this->trigger( 'after_delete', $this->_document );
			return $_result;
		}

		/**
		 * Add an attachment if $couchDb is set
		 * @param string $name
		 * @param string $data
		 * @param string $contentType
		 * @param string $rev
		 * @return \Kisma\Components\CouchDbDocument
		 * @return mixed
		 */
		public function addAttachment( $name, $data, $contentType = self::DefaultContentType, $rev = null )
		{
			if ( null !== $this->_db )
			{
				throw new \Kisma\StorageException( 'No database specified. Cannot add attachment.' );
			}

			return $this->_db->setAttachment( $name, $data, $contentType, $this->getId(), $rev );
		}

		/**
		 * Creates a document key with our base pattern. Override to use different hash or key types.
		 * @param string $id
		 * @return string
		 */
		public function createKey( $id )
		{
			return $this->_createKey( $id );
		}

		/**
		 * Event triggered before a document is saved
		 * @param \Kisma\Components\Event $event
		 * @return bool
		 * @throws \Teledini\Exceptions\StorageException
		 */
		public function onBeforeSave( $event )
		{
			return true;
		}

		/**
		 * @param \Kisma\Components\Event $event
		 * @return bool
		 */
		public function onBeforeFind( $event )
		{
			return true;
		}

		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * Creates a document key with our base pattern. Override to use different hash or key types.
		 * @param string $id
		 * @return string
		 */
		protected function _createKey( $id )
		{
			return $id;
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param \Sag $db
		 * @return $this
		 */
		public function setDb( $db )
		{
			$this->_db = $db;
			return $this;
		}

		/**
		 * @return \Sag
		 */
		public function getDb()
		{
			return $this->_db;
		}

		/**
		 * @param string $keyPrefix
		 * @return \Kisma\Storage\CouchDbDocument
		 */
		public function setKeyPrefix( $keyPrefix )
		{
			$this->_keyPrefix = $keyPrefix;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getKeyPrefix()
		{
			return $this->_keyPrefix;
		}

		/**
		 * @return string
		 */
		public function getId()
		{
			return $this->_document->_id;
		}

		/**
		 * @param string $id
		 * @return \Kisma\Components\Document
		 */
		public function setId( $id )
		{
			$this->_document->_id = $id;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getRev()
		{
			return $this->_document->_rev;
		}

		/**
		 * @param string $rev
		 * @return \Kisma\Components\Document
		 */
		public function setRev( $rev )
		{
			$this->_document->_rev = $rev;
			return $this;
		}

		/**
		 * @param boolean $autoSetId
		 * @return \Kisma\Components\CouchDbDocument
		 */
		public function setAutoSetId( $autoSetId )
		{
			$this->_autoSetId = $autoSetId;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getAutoSetId()
		{
			return $this->_autoSetId;
		}

		/**
		 * @param boolean $hashKeys
		 * @return \Kisma\Components\CouchDbDocument
		 */
		public function setHashKeys( $hashKeys )
		{
			$this->_hashKeys = $hashKeys;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getHashKeys()
		{
			return $this->_hashKeys;
		}
	}
}