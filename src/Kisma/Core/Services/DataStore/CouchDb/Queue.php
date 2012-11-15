<?php
/**
 * Queue.php
 */
namespace Kisma\Core\Services\DataStore\CouchDb;
use Kisma\Core\Utility\Log;
use Kisma\Core\Utility\Inflector;

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
	const QueueServiceKeyPrefix = 'chairlift.';

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Doctrine\ODM\CouchDB\DocumentManager
	 */
	protected $_dm;
	/**
	 * @var string
	 */
	protected $_queue;
	/**
	 * @var string A string which will be prepended along, with a colon separator, to all new _id values
	 */
	protected $_prefix = null;

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

		if ( null === $this->_queue )
		{
			$this->_queue = 'default';
		}

		if ( null === $this->_dm )
		{
			$this->_dm = \Kisma\Core\Utility\ChairLift::createDocumentManager( $settings );
		}

		//	Add a reference to the queue to the kisma global space
		\Kisma::set( static::QueueServiceKeyPrefix . $this->_queue, $this );
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
	 * @param string $queue      The name of the queue for this item
	 * @param string $ownerId    The owner of this queue item
	 * @param mixed  $payload    Any kind of info you want to pass the dequeuer process
	 *
	 * @return mixed|bool The _rev of the saved message, false if unchanged
	 */
	public function enqueue( $queue, $ownerId, $payload = null )
	{
		if ( is_object( $payload ) && !( $payload instanceof \stdClass ) )
		{
			$_queueType = Inflector::tag( get_class( $payload ), true, true );
		}

		//	New item
		$_item = new \Kisma\Core\Containers\QueueItem();
		$_item->setQueue( $queue );
		$_item->setOwnerId( $ownerId );
		$_item->setPayload( $payload );
		$this->_dm->persist( $_item );
		$this->_dm->flush();

		Log::debug( 'Enqueue:' . $this->_queue . ':' . $_item->getId() );

		return $_item->getId();
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
	 * @param string     $queue
	 * @param string     $ownerId
	 * @param int        $since
	 * @param array      $query
	 * @param string     $params
	 * @param string     $filter
	 *
	 * @throws \Doctrine\CouchDB\HTTP\HTTPException
	 * @return array
	 */
	protected function _getFeedChanges( $queue, $ownerId, $since = 0, $query = array(), $params = null, $filter = '/queue' )
	{
		$_client = $this->_dm->getCouchDBClient();

		$_path = '/' . $_client->getDatabase() . '/_changes?';

		if ( empty( $query ) )
		{
			$query = array();
		}

		$query['queue'] = $queue;
		$query['ownerId'] = $ownerId;
		$query['since'] = $since;

		$_path .= http_build_query( $query ) . '&filter=' . $_client->getDatabase() . $filter;

		$_response = $_client->getHttpClient()->request( 'GET', $_path, $params );

		if ( 200 != $_response->status )
		{
			throw \Doctrine\CouchDB\HTTP\HTTPException::fromResponse( $_path, $_response );
		}

		return $_response->body;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param \Doctrine\ODM\CouchDB\DocumentManager $dm
	 *
	 * @return Queue
	 */
	public function setDm( $dm )
	{
		$this->_dm = $dm;

		return $this;
	}

	/**
	 * @return \Doctrine\ODM\CouchDB\DocumentManager
	 */
	public function getDm()
	{
		return $this->_dm;
	}

	/**
	 * @param string $prefix
	 *
	 * @return Queue
	 */
	public function setPrefix( $prefix )
	{
		$this->_prefix = $prefix;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->_prefix;
	}

	/**
	 * @param string $queue
	 *
	 * @return Queue
	 */
	public function setQueue( $queue )
	{
		$this->_queue = $queue;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getQueue()
	{
		return $this->_queue;
	}

}
