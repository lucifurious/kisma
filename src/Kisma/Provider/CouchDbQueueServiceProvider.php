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
	/**
	 * CouchDbQueueServiceProvider
	 * A provider that wraps the CouchDbClient library for working with a CouchDb instance
	 */
	class CouchDbQueueServiceProvider extends \Kisma\Components\SilexServiceProvider
	{
		//*************************************************************************
		//* Constants
		//*************************************************************************

		/**
		 * @var string The option that holds the queue name
		 */
		const Option_QueueName = 'couchdb.queue.options.name';
		/**
		 * @var string Our service options
		 */
		const Options = 'couchdb.queue.options';
		/**
		 * @var string Our queue array
		 */
		const Queues = 'couchdb.queues';
		/**
		 * @var string
		 */
		const PendingViewName = 'pending';
		/**
		 * @var int
		 */
		const DefaultMaxItems = 1;

		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var string
		 */
		protected $_queueName;
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
		/**
		 * @var \Doctrine\CouchDB\CouchDBClient
		 */
		protected $_client = null;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * Registers the service with Silex
		 *
		 * @code
		 *	//	Example usage:
		 *	$app->register( new CouchDbQueueServiceProvider(),
		 *		 array(
		 *			 //	Queue name
		 *			 CouchDbQueueServiceProvider::Option_QueueName => 'job_queue',
		 *		)
		 *	);
		 *
		 *	//	Then, use queue name from above to access each queue service:
		 *	$app['couchdb.queue.<queueName>']->doStuff();
		 *
		 *	//	Example:
		 *	$app['couchdb.queue.job_queue']->enqueue( $queueItem );
		 * @endcode
		 *
		 * @param \Silex\Application $app
		 */
		public function register( \Silex\Application $app )
		{
			if ( null === $this->_queueName )
			{
				$this->_queueName = $app[self::Option_QueueName];
			}

			$_options = isset( $app[self::Options] ) ? $app[self::Options] : $app[CouchDbServiceProvider::Options];
			$_options['dbname'] = $this->_queueName;

			$_allOptions =
				!isset( $app[CouchDbServiceProvider::GroupOptions] ) ? array() :
					$app[CouchDbServiceProvider::GroupOptions];

			$_allOptions[$this->_queueName] = $_options;
			$app[CouchDbServiceProvider::GroupOptions] = $_allOptions;

			$app->register( new CouchDbServiceProvider(), $_options );

			if ( !isset( $app[self::Queues] ) )
			{
				$app[self::Queues] = array();
			}

			$this->_client = $app[CouchDbServiceProvider::Options_GroupPrefix][$this->_queueName]->getCouchDbClient();
			$_queues = $app[self::Queues];
			$_queues[$this->_queueName] = $this;

			$app[self::Queues] = $_queues;

			//	Make sure the queue exists!
			try
			{
				$this->_client->getDatabaseInfo( $this->_queueName );
			}
			catch ( \Doctrine\CouchDB\HTTP\HTTPException $_ex )
			{
				if ( 404 != $_ex->getCode() )
				{
					throw $_ex;
				}

				$this->_client->createDatabase( $this->_queueName );
			}
		}

		/**
		 * Get work items from the queue
		 * The descending param is used to get LIFO (if true) or FIFO (if false) behavior.
		 *
		 * @param int  $maxItems
		 * @param bool $fifo
		 * @param bool $useLocks If true, a lock will be added to the queue item
		 *
		 * @return array|false
		 */
		public function dequeue( $maxItems = self::DefaultMaxItems, $fifo = true, $useLocks = false )
		{
			$_queueItems = array();

			Log::debug( 'Requesting pending view: ' . self::PendingViewName );

			$_query = $this->getClient()->createViewQuery( 'document', self::PendingViewName, null );
			$_query->setLimit( $maxItems );
			$_query->setIncludeDocs( true );
			$_query->setDescending( $fifo );

			$_pendingMessages = $_query->execute();

			Log::debug( 'View results: ' . $_pendingMessages );

			//	Build doc array non-locked
			foreach ( $_pendingMessages as $_message )
			{
				//	Add successful lock to work item list
				$_queueItems[] = new CouchDbQueueItem( array( 'document' => $_message ) );
			}

			//	Return queue item(s)
			return empty( $_queueItems ) ? false : $_queueItems;
		}

		/**
		 * Adds a work item to the queue
		 *
		 * @param string $id
		 * @param mixed  $feedData Any kind of info you want to pass the dequeuer process
		 * @param int	$expireTime How long to keep this guy around. -1 = until deleted@internal param int $timeToLive
		 *
		 * @return mixed The id of the message
		 */
		public function enqueue( $id = null, $feedData = null, $expireTime = -1 )
		{
			//	Create an id
			$_id = $this->createKey();

			$_item = new CouchDbQueueItem( array(
				'id' => $_id, 'feed_data' => $feedData, 'expire_time' => $expireTime,
			) );

			//	See if this key is already in the queue...
			/** @var $_response \Doctrine\CouchDB\HTTP\Response */
			$_response = $this->getClient()->findDocument( $_id );

			if ( 404 == $_response->status )
			{
				$_document = $_item;
			}
			else if ( 200 == $_response->status )
			{
				$_document = new \Kisma\Container\CouchDbQueueItem( $_response->body );

				Log::debug( 'Found prior queue item, _rev: ' . $_document->getRev() );
			}
			else
			{
				throw new \Kisma\StorageException( 'Unexpected CouchDb response.', $_response->status, null, $_response );
			}

			//	Doc exists, read and update...
			$_document->update_time = date( 'c' );

			//	Write it out and update
			$_response = $this->getClient()->putDocument( $_document->getFields(), $_id, $_document->getRev() );

			if ( isset( $_response->body ) && \Kisma\Utility\Scalar::in( $_response->status, 200, 201 ) )
			{
				Log::debug( 'Document queued: ' . print_r( $_response->body, true ) );
				return $_response->body->rev;
			}

			return false;
		}

		/**
		 * Creates a new queue with the specified name.
		 *
		 * @param string $name
		 *
		 * @return Queue
		 */
		public function createQueue( $name )
		{
			//	Create and return a new queue
			return new Queue(
				array(
					'queueName' => $name,
					'queueService' => $this,
				)
			);
		}

		/**
		 * Given an $id, based on settings, hash/encrypt/prefix the $id
		 *
		 * @param null|string $id
		 * @param null|string $salt If null, key will NOT be encrypted
		 *
		 * @return string
		 */
		public function createKey( $id = null, $salt = null )
		{
			//	Start with the _id
			$_key = $id ? : $this->_queueName . microtime( true );

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
		 * @param string $id
		 *
		 * @return mixed
		 */
		public function documentExists( $id )
		{
			/** @var $_response \Doctrine\CouchDB\HTTP\Response */
			$_response = $this->_client->findDocument( $id );

			if ( 404 != $_response->status )
			{
				return $_response->body;
			}

			return false;
		}

		//*************************************************************************
		//* Private Methods
		//*************************************************************************

		/**
		 * Creates our design document
		 *
		 * @param bool   $noSave
		 * @param string $name
		 *
		 * @return bool|\Kisma\Components\Document
		 */
		public function createDesignDocument( $noSave = false, $name = 'document' )
		{
			if ( false !== ( $_doc = $this->documentExists( $name, $noSave ) ) )
			{
				return $_doc;
			}

			$_doc = new \Kisma\Components\DesignDocument();
			$_doc->_id = $name;

			try
			{
				//	Store it
				return $this->_client->createDesignDocument( $name, $_doc );
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
		 * Hashes an _id
		 * Override to use different hash or key types.
		 *
		 * @param string $id
		 *
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
		 *
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
		 *
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
		 *
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
		 *
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

		/**
		 * @param string $queueName
		 *
		 * @return \Kisma\Provider\CouchDbQueueServiceProvider
		 */
		public function setQueueName( $queueName )
		{
			$this->_queueName = $queueName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getQueueName()
		{
			return $this->_queueName;
		}

		/**
		 * @return \Doctrine\CouchDB\CouchDBClient
		 */
		public function getClient()
		{
			return $this->_client;
		}
	}
}
