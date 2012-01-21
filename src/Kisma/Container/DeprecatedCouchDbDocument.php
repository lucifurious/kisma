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
namespace Kisma\Container;

//*************************************************************************
//* Aliases
//*************************************************************************

use Kisma\Utility as Utility;
use Doctrine\CouchDB\Response;

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
class CouchDbDocument extends \Kisma\Container\Document implements \Kisma\IDataModel
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const DefaultContentType = 'application/octet-stream';
	/**
	 * @var string
	 */
	const DesignDocumentName = 'document';

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string
	 */
	protected $_keyPrefix = null;
	/**
	 * @var \Doctrine\CouchDB\CouchDBClient
	 */
	protected $_client = null;
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
	 *
	 * @param string			   $id
	 * @param bool				 $createIfNotFound If true, an empty document will be returned if the $id does not exist.
	 * @param \stdClass|array|null $defaultDocument
	 *
	 * @return \stdClass|null
	 */
	public function find( $id, $createIfNotFound = true, $defaultDocument = null )
	{
		if ( null === $this->_client )
		{
			throw new \Kisma\StorageException( 'No database specified. Cannot find.' );
		}

		//	Build our key (_id)
		$_key = $this->_makeKey( $id );

		try
		{
			\Kisma\Kisma::app()->dispatch( \Kisma\Event\ModelEvent::BeforeFind, new \Kisma\Event\ModelEvent( $this ) );

			/** @var $_response \Doctrine\CouchDB\HTTP\Response */
			$_response = $this->_client->findDocument( $_key );

			if ( !Utility\Scalar::in( $_response->status, '200', '201' ) )
			{
				//	Something icky here
				throw new \Kisma\CouchDbException( 'Unexpected CouchDb response.', $_response->status );
			}

			Utility\Log::trace( 'Found document "' . $id . ' with id: ' . $_key );
			\Kisma\Kisma::app()->dispatch( \Kisma\Event\ModelEvent::AfterFind, new \Kisma\Event\ModelEvent( $this ) );

			$this->setFields( $_response->body );
		}
		catch ( \Exception $_ex )
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

			Utility\Log::trace( 'Creating document id "' . $id . '": ' . $_key );

			//	Create a new document and assign the _id
			$this->setFields( $defaultDocument );
			$this->setId( $_key );
			$this->save();
		}

		//	Return the document
		return $this->getFields();
	}

	/**
	 * Saves the current document if $couchDb is set
	 *
	 * @return mixed
	 */
	public function save()
	{
		if ( null === $this->_client )
		{
			throw new \Kisma\StorageException( 'No database specified. Cannot save.' );
		}

		if ( !isset( $this->_fields['_id'] ) )
		{
			if ( false === $this->_autoSetId )
			{
				throw new \Kisma\StorageException( 'Required document field "_id" missing.' );
			}

			$this->_fields['_id'] = $this->_makeKey( $this->_client->getUuids( 1 ) );
		}
		else if ( !isset( $this->_fields['_rev'] ) )
		{
			$this->_fields['_id'] = $this->_makeKey( $this->_fields['_id'] );
		}

		if ( \Kisma\Kisma::app()->dispatch( \Kisma\Event\ModelEvent::BeforeSave, new \Kisma\Event\ModelEvent( $this ) ) )
		{
			$_result = $this->_client->putDocument( $this->_fields, $this->_fields['_id'], $this->getRev() );
			\Kisma\Kisma::app()->dispatch( \Kisma\Event\ModelEvent::AfterSave, new \Kisma\Event\ModelEvent( $this, $_result ) );
			return $_result;
		}

		return false;
	}

	/**
	 * Delete document from DB if $couchDb is set
	 *
	 * @return mixed
	 */
	public function delete()
	{
		if ( null !== $this->_client )
		{
			throw new \Kisma\StorageException( 'No database specified. Cannot delete.' );
		}

		if ( \Kisma\Kisma::app()->dispatch( \Kisma\Event\ModelEvent::BeforeDelete, new \Kisma\Event\ModelEvent( $this ) ) )
		{
			$_result = $this->_client->deleteDocument( $this->getId(), $this->getRev() );

			\Kisma\Kisma::app()->dispatch( \Kisma\Event\ModelEvent::AfterDelete, new \Kisma\Event\ModelEvent( $this, $_result ) );

			return $_result;
		}

		return false;
	}

	/**
	 * Creates a document key with our base pattern. Override to use different hash or key types.
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	public function createKey( $id )
	{
		return $this->_createKey( $id );
	}

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * Event triggered before a document is saved
	 *
	 * @param \Kisma\Components\Event $event
	 *
	 * @return bool
	 * @throws \Teledini\Exceptions\StorageException
	 */
	public function onBeforeSave( $event )
	{
		return true;
	}

	/**
	 * @param \Kisma\Components\Event         $event
	 * @param \Doctrine\CouchDB\HTTP\Response $response
	 *
	 * @return bool
	 */
	public function onAfterSave( $event, $response )
	{
		return true;
	}

	/**
	 * @param \Kisma\Components\Event $event
	 *
	 * @return bool
	 */
	public function onBeforeFind( $event )
	{
		return true;
	}

	/**
	 * @param \Kisma\Components\Event         $event
	 * @param \Doctrine\CouchDB\HTTP\Response $response
	 *
	 * @return bool
	 */
	public function onAfterFind( $event, $response )
	{
		return true;
	}

	/**
	 * Event triggered before a document is deleted. Return false to stop the deletion
	 *
	 * @param \Kisma\Components\Event $event
	 *
	 * @return bool
	 * @throws \Teledini\Exceptions\StorageException
	 */
	public function onBeforeDelete( $event )
	{
		return true;
	}

	/**
	 * @param \Kisma\Components\Event         $event
	 * @param \Doctrine\CouchDB\HTTP\Response $response
	 *
	 * @return bool
	 */
	public function onAfterDelete( $event, $response )
	{
		return true;
	}

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * Creates a document key with our base pattern. Override to use different hash or key types.
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	protected function _createKey( $id )
	{
		return $id;
	}

	/**
	 * @param string $id
	 *
	 * @return null|string
	 */
	protected function _makeKey( $id )
	{
		$_key = null;

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

		return $_key;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param \Doctrine\CouchDB\CouchDBClient $db
	 *
	 * @return $this
	 */
	public function setClient( $db )
	{
		$this->_client = $db;
		return $this;
	}

	/**
	 * @return \Doctrine\CouchDB\CouchDBClient
	 */
	public function getClient()
	{
		return $this->_client;
	}

	/**
	 * @param string $keyPrefix
	 *
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
		return Utility\Option::get( $this->_fields, '_id' );
	}

	/**
	 * @param string $id
	 *
	 * @return \Kisma\Components\Document
	 */
	public function setId( $id )
	{
		\Kisma\Utility\Option::set( $this->_fields, '_id' );
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRev()
	{
		return Utility\Option::get( $this->_fields, '_rev' );
	}

	/**
	 * @param string $rev
	 *
	 * @return \Kisma\Components\Document
	 */
	public function setRev( $rev )
	{
		\Kisma\Utility\Option::set( $this->_fields, '_rev' );
		return $this;
	}

	/**
	 * @param boolean $autoSetId
	 *
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
	 *
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
