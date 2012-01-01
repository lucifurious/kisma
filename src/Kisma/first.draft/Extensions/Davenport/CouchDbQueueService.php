<?php
/**
 * CouchDbQueueService.php
 * Davenport : A Kisma(tm) Extension (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link		  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license	   http://github.com/Pogostick/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @category	  Kisma_Extensions
 * @package	   kisma.extensions.davenport
 * @since		 v1.0.0
 * @filesource
 */
namespace Kisma\Extensions\Davenport
{
	//*************************************************************************
	//* Usage
	//*************************************************************************

	use \Kisma\CouchDbException;

	/**
	 * CouchDbQueueService
	 * Generic queue handling class
	 *
	 * @property string $queueName
	 * @property string $keyPrefix
	 * @property boolean $encryptKeys
	 * @property string $hashKeys
	 */
	class CouchDbQueueService extends \Kisma\Aspects\Storage\CouchDb
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var string A string which will be prepended along, with a colon separator, to all new _id values
		 */
		protected $_keyPrefix = null;
		/**
		 * @var bool Enable to encrypt _id value before storing.
		 */
		protected $_encryptKeys = false;
		/**
		 * @var bool Enable to hash _id value before storing.
		 */
		protected $_hashKeys = true;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * Checks if a queue is defined or not
		 * @param string $name
		 * @return bool
		 */
		public function queueExists( $name )
		{
			return $this->documentExists( \K::untag( $name ) );
		}

		/**
		 * Creates a new queue with the specified name.
		 * @param string $name
		 * @return Queue
		 */
		public function createQueue( $name )
		{
			$_queueName = \K::untag( $name );

			//	Create and return a new queue
			return new Queue(
				array(
					'queueName' => $_queueName,
					'queueService' => $this,
				)
			);
		}

		/**
		 * Given an $id, based on settings, hash/encrypt/prefix the $id
		 *
		 * @param null|string $id
		 * @param null|string $salt If null, key will NOT be encrypted
		 * @return string
		 */
		public function createKey( $id = null, $salt = null )
		{
			//	Start with the _id
			$_key = $id;

			//	Encrypt first
			if ( null !== $salt && false !== $this->_encryptKeys )
			{
				$_key = $this->_encryptKey( $salt, $_key );
			}

			//	Then hash
			if ( null !== $id && false !== $this->_hashKeys )
			{
				$_key = $this->_hashKey( $_key );
			}

			if ( null !== $this->_keyPrefix )
			{
				$_key = $this->_keyPrefix . ':' . $_key;
			}

			//	Return the new key!
			return $_key;
		}

		//*************************************************************************
		//* Private Methods
		//*************************************************************************

		/**
		 * Creates our design document
		 * @param bool $noSave
		 * @return bool|\Kisma\Components\Document
		 */
		protected function _createDesignDocument( $noSave = false )
		{
			if ( false !== ( $_doc = $this->documentExists( Queue::DesignDocumentName, $noSave ) ) )
			{
				return $_doc;
			}

			$_doc = new \stdClass();
			$_doc->_id = Queue::DesignDocumentName;

			$_doc->views = new \stdClass();

			$_doc->views->pending = new \stdClass();
			$_doc->views->pending->map = 'function( doc ) { if (!doc.lock) emit(doc.create_time,null); }';

			$_doc->views->locked = new \stdClass();
			$_doc->views->locked->map = 'function(doc) { if (doc.lock) emit(doc.lock.lock_time,null); }';

			try
			{
				//	Store it
				return $this->put( $_doc->_id, $_doc );
			}
			catch ( CouchDbException $_ex )
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
		 * Hashes an _id
		 * Override to use different hash or key types.
		 *
		 * @param string $id
		 * @return string
		 */
		protected function _hashKey( $id )
		{
			return \Kisma\Utility\Hash::hash( $id, \Kisma\HashType::SHA1, 40 );
		}

		/**
		 * Encrypts an _id. You may pass a null for $id and this will encrypt the user name and password (in a
		 * special super-double-secret pattern that is not obvious) for storage as an authorization key of sorts. You
		 * can use it just like an MD5 hash but it's a tad more secure I suppose.
		 *
		 * @param string $id
		 * @param string $salt
		 * @return string
		 */
		protected function _encryptKey( $salt, $id = null )
		{
			if ( null === $id )
			{
				$id = '|<|' . $this->_password . '|*|' . $this->_userName . '|>|';
			}

			//	Return encrypted string
			return \Kisma\Utility\Hash::encryptString( $id, $salt );
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param bool $encryptKeys
		 * @return CouchDbQueueService
		 */
		public function setEncryptKeys( $encryptKeys )
		{
			$this->_encryptKeys = $encryptKeys;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function getEncryptKeys()
		{
			return $this->_encryptKeys;
		}

		/**
		 * @param bool $hashKeys
		 * @return CouchDbQueueService
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

		/**
		 * @param string $keyPrefix
		 * @return \Kisma\Extensions\Davenport\CouchDbQueueService
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

	}
}