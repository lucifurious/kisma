<?php
/**
 * CouchDbServer.php
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright     Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link          http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license       http://github.com/Pogostick/kisma/licensing/
 * @author        Jerry Ablan <kisma@pogostick.com>
 * @category      Kisma_Services_Remote
 * @package       kisma.services.remote
 * @since         v1.0.0
 * @filesource
 */
namespace Kisma\Services\Remote
{
	//*************************************************************************
	//* Requirements 
	//*************************************************************************

	use \Kisma\Utility as Utility;

	/**
	 * CouchDbServer
	 * Merely a value store for a CouchDb instance. Has a few utility methods.
	 */
	class CouchDbServer extends \Kisma\Services\Remote\Http
	{
		//*************************************************************************
		//* Private Members 
		//*************************************************************************

		/**
		 * @var string The name of the database
		 */
		protected $_databaseName = null;
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
		 * Given an $id, based on settings, hash/encrypt/prefix the $id
		 *
		 * @param null|string $id
		 * @param null|string $salt
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

		/**
		 * Hashes an _id
		 * Override to use different hash or key types.
		 *
		 * @param string $id
		 * @return string
		 */
		protected function _hashKey( $id )
		{
			Utility\Hash::hash( $id, \Kisma\HashType::SHA1, 40 );
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
			return Utility\Hash::encryptString( $id, $salt );
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param string $databaseName
		 * @return CouchDbServer
		 */
		public function setDatabaseName( $databaseName )
		{
			$this->_databaseName = $databaseName;
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
		 * @param bool $encryptKeys
		 * @return CouchDbServer
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
		 * @param string $keyPrefix
		 * @return CouchDbServer
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
		 * @param boolean $hashKeys
		 * @return $this
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