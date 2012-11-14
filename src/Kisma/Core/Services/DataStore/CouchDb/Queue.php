<?php
/**
 * Queue.php
 */
namespace Kisma\Core\Services\DataStore\CouchDb;
use Kisma\Core\Utility\Log;

/**
 * Queue
 * A queuing service using CouchDb
 */
class Queue extends \Kisma\Core\Services\SeedService
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const PendingViewName = 'pending_items';
	/**
	 * @var int
	 */
	const DefaultMaxItems = 1;
	/**
	 * @var string The key prefix for each queue
	 */
	const QueueServiceKeyPrefix = 'queue_service.';

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
	 * @var string The design document structure
	 */
	protected $_designPath = '/';
	/**
	 * @var string The name of the design document with filters
	 */
	protected $_designDocument = 'document';
	/**
	 * @var string The name of this feed provider
	 */
	protected $_providerName = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Interfaces\ConsumerLike $consumer
	 * @param array                               $settings
	 */
	public function __construct( \Kisma\Core\Interfaces\ConsumerLike $consumer, $settings = array() )
	{
		parent::__construct( $consumer, $settings );

		if ( null === $this->_queueName )
		{
			$this->_queueName = \Kisma\Core\Utility\Inflector::tag( get_class( $this ), true );
		}

		//	Add a reference to the queue to the kisma global space
		\Kisma::set( static::QueueServiceKeyPrefix . $this->_queueName, $this );
	}

	/**
	 * Get work items from the queue
	 * The descending param is used to get LIFO (if true) or FIFO (if false) behavior.
	 *
	 * @param string       $ownerId
	 * @param int          $since
	 * @param array|string $queryParams
	 * @param string       $filter
	 *
	 * @return array|false
	 */
	public function dequeue( $ownerId, $since = 0, $queryParams = array(), $filter = '/queue' )
	{
		Log::debug( 'Requesting changes...' );

		$_changes = $this->_getFeedChanges( $ownerId, $since, $queryParams, null, $filter );

		if ( empty( $_changes ) || !isset( $_changes['results'] ) )
		{
			Log::debug( 'No detected changes in feed.' );

			return false;
		}

		//	Return queue item(s)
		return empty( $_changes['results'] ) ? false : $_changes['results'];
	}

	/**
	 * Adds a work item to the queue
	 *
	 * @param string $ownerId    The owner of this queue item
	 * @param string $accountId  User account id
	 * @param mixed  $feedData   Any kind of info you want to pass the dequeuer process
	 * @param int    $expireTime How long to keep this guy around. -1 = until deleted
	 *
	 * @return mixed|false The _rev of the saved message, false if unchanged
	 */
	public function enqueue( $ownerId, $accountId, $feedData, $expireTime = -1 )
	{
		$_queueType = 'raw';

		if ( is_object( $feedData ) && !( $feedData instanceof \stdClass ) )
		{
			$_queueType = \Kisma\Core\Utility\Inflector::tag( get_class( $feedData ), true, true );
		}

		//	New item
		if ( empty( $_response ) )
		{
			$_item = new \Kisma\Core\Containers\QueueItem(
				array(
					'owner_id'      => $ownerId,
					'account_id'    => $accountId,
					'provider_name' => $this->_providerName,
				)
			);

			Log::debug( 'New queue item (' . $_item->get( 'queue_type' ) . '): ' . $ownerId );
		}
		//	Existing item
		else
		{
			$_item = $_response;
			Log::debug( 'Found prior queue item, _rev: ' . $_item->version );
		}

		//	Update
		$_item->merge(
			array(
				'queue_type'  => $_queueType,
				'feed_data'   => $feedData,
				'expire_time' => $expireTime,
				'updated'     => new \DateTime( 'now' ),
			)
		);

		//	Write it out and update
		Log::debug( 'Document queued for ' . $ownerId );

		return $_item->get( '_rev' );
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

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
		return \Kisma\Core\Utility\Hasher::hash( $id, \Kisma\Core\Enums\HashType::SHA1, 40 );
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
			$id = '|<|' . $id . '|>|';
		}

		//	Return encrypted string
		return \Kisma\Core\Utility\Hasher::encryptString( $id, $salt );
	}

	/**
	 * Pulls a changes feed for this queue
	 *
	 * @param string     $ownerId
	 * @param int        $since
	 * @param array      $query
	 * @param string     $params
	 * @param string     $filter
	 *
	 * @return array
	 * @throws \Doctrine\CouchDB\HTTP\HTTPException
	 */
	protected function _getFeedChanges( $ownerId, $since = 0, $query = array(), $params = null, $filter = '/queue' )
	{
		$_client = $this->_dm->getCouchDBClient();

		$_path = '/' . $_client->getDatabase() . '/_changes?';

		if ( empty( $query ) )
		{
			$query = array();
		}

		$query['owner_id'] = $ownerId;
		$query['providerName'] = $this->_providerName;
		$query['since'] = $since;

		$_path .= http_build_query( $query ) . '&filter=' . $_client->getDatabase() . $filter;

		$_response = $_client->getHttpClient()->request( 'GET', $_path, $params );

		if ( $_response->status != 200 )
		{
			throw HTTPException::fromResponse( $_path, $_response );
		}

		return $_response->body;
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
	 * @return \Kisma\Provider\QueueServiceProvider
	 */
	protected function _setQueueName( $queueName )
	{
		$this->_queueName = $queueName;

		return $this;
	}

	/**
	 * @param string $queueName
	 *
	 * @return \Kisma\Provider\CouchDb\QueueServiceProvider
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
	 * @param string $designPath
	 *
	 * @return \Kisma\Provider\CouchDb\QueueServiceProvider
	 */
	public function setDesignPath( $designPath )
	{
		$this->_designPath = $designPath;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDesignPath()
	{
		return $this->_designPath;
	}

	/**
	 * @param \Kisma\Provider\CouchDb\DocumentManager $dm
	 *
	 * @return \Kisma\Provider\CouchDb\QueueServiceProvider
	 */
	public function setDm( $dm )
	{
		$this->_dm = $dm;

		return $this;
	}

	/**
	 * @return \Kisma\Provider\CouchDb\DocumentManager
	 */
	public function getDm()
	{
		return $this->_dm;
	}

	/**
	 * @param string $designDocument
	 *
	 * @return \Kisma\Provider\CouchDb\QueueServiceProvider
	 */
	public function setDesignDocument( $designDocument )
	{
		$this->_designDocument = $designDocument;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDesignDocument()
	{
		return $this->_designDocument;
	}

	/**
	 * @param string $providerName
	 *
	 * @return \Kisma\Provider\CouchDb\QueueServiceProvider
	 */
	public function setProviderName( $providerName )
	{
		$this->_providerName = $providerName;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getProviderName()
	{
		return $this->_providerName;
	}

}
