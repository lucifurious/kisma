<?php
/**
 * WorkQueue.php
 */
namespace Kisma\Core\Services\Storage\CouchDb;
use Kisma\Core\Utility\Log;
use Kisma\Core\Utility\Inflector;

/**
 * WorkQueue
 * A queuing service using CouchDb
 *
 * Keys in the document store are in this format:
 *
 *     namespace:queue:<_id>
 *
 * The queue control document's key is:
 *
 *     namespace:queue:manager
 */
class WorkQueue extends \Kisma\Core\Services\SeedService
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string
	 */
	protected $_queue = 'default';
	/**
	 * @var string Used to construct keys in the queue
	 */
	protected $_namespace = 'work';
	/**
	 * @var \Doctrine\ODM\CouchDB\DocumentManager
	 */
	protected $_dm = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Interfaces\ConsumerLike $consumer
	 * @param array                               $settings
	 *
	 * @return \Kisma\Core\Services\Storage\CouchDb\WorkQueue
	 */
	public function __construct( \Kisma\Core\Interfaces\ConsumerLike $consumer, $settings = array() )
	{
		parent::__construct( $consumer, $settings );

		if ( null === $this->_namespace )
		{
			$this->_namespace = 'work';
		}

		if ( null === $this->_queue )
		{
			$this->_queue = 'default';
		}

		//	Set our document manager up...
		$this->_dm = \Kisma\Core\Utility\ChairLift::documentManager( $settings );

		//	Add a reference to the queue to the kisma global space
		\Kisma::set( $this->_key( false ), $this );
	}

	/**
	 * Get work items from the queue
	 * The descending param is used to get LIFO (if true) or FIFO (if false) behavior.
	 *
	 * @param \Kisma\Core\Containers\Documents\Session $owner
	 * @param int                                      $since
	 * @param array|string                             $queryParams
	 * @param string                                   $filter
	 *
	 * @return array|bool
	 */
	public function dequeue( $owner, $since = 0, $queryParams = array(), $filter = '/queue' )
	{
		Log::debug( 'Requesting changes...' );

		$_changes = $this->_getFeedChanges( $owner, $since, $queryParams, null, $filter );

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
	 * @param string|callable                           $handler    The name of the handler class or closure
	 * @param \Kisma\Core\Containers\Documents\Session  $owner      The owner of this queue item
	 * @param mixed                                     $payload    Any kind of info you want to pass the worker process
	 * @param string                                    $queue
	 *
	 * @return string|bool The _rev of the saved message, false if unchanged
	 */
	public function enqueue( $handler, $owner = null, $payload = null, $queue = null )
	{
		//	New item
		$_item = new \Kisma\Core\Containers\Documents\WorkItem();
		$_item->setQueue( $queue ? : $this->_queue );
		$_item->setHandler( $handler );
		$_item->setOwner( $owner );
		$_item->setPayload( $payload );
		$_item->setId( $_key = $this->_key( $this->_dm->getCouchDBClient()->getUuids() ) );
		$this->_dm->persist( $_item );
		$this->_dm->flush();

		Log::debug(
			'Enqueue',
			array(
				'key'   => $_key,
				'queue' => $this->_queue,
				'id'    => $_item->getId(),
				'rev'   => $_item->getVersion(),
			)
		);

		return $_item->getVersion();
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
		return \Kisma\Core\Utility\Hasher::hash( $this->_key( $id ), \Kisma\Core\Enums\HashType::SHA1, 40 );
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
		//	Return encrypted string
		return \Kisma\Core\Utility\Hasher::encryptString( $this->_key( $id ), $salt );
	}

	/**
	 * Pulls a changes feed for this queue
	 *
	 * @param string     $queue
	 * @param mixed      $owner
	 * @param int        $since
	 * @param array      $query
	 * @param string     $params
	 * @param string     $filter
	 *
	 * @throws \Doctrine\CouchDB\HTTP\HTTPException
	 * @return array
	 */
	protected function _getFeedChanges( $queue, $owner, $since = 0, $query = array(), $params = null, $filter = '/queue' )
	{
		$_client = $this->_dm->getCouchDBClient();

		$_path = '/' . $_client->getDatabase() . '/_changes?';

		if ( empty( $query ) )
		{
			$query = array();
		}

		$query['queue'] = $queue;
		$query['owner'] = $owner;
		$query['since'] = $since;

		$_path .= http_build_query( $query ) . '&filter=' . $_client->getDatabase() . $filter;

		/** @noinspection PhpUndefinedMethodInspection */
		$_response = $_client->getHttpClient()->request( 'GET', $_path, $params );

		if ( 200 != $_response->status )
		{
			throw \Doctrine\CouchDB\HTTP\HTTPException::fromResponse( $_path, $_response );
		}

		return $_response->body;
	}

	/**
	 * Creates a key for a document.
	 *
	 * @param string $id Set to FALSE to generate a manager key
	 * @param string $delimiter
	 * @param string $innerDelimiter
	 *
	 * @return string
	 */
	protected function _key( $id = null, $delimiter = ':', $innerDelimiter = '.' )
	{
		$_parts = array(
			$this->_namespace,
			$this->_queue,
		);

		if ( false === $id )
		{
			$_parts[] = 'manager';
		}
		else if ( null !== $id )
		{
			if ( is_array( $id ) )
			{
				$id = implode( $innerDelimiter, $id );
			}

			$_parts[] = $id;
		}

		return implode( $delimiter, $_parts );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param string $namespace
	 *
	 * @return WorkQueue
	 */
	public function setNamespace( $namespace )
	{
		$this->_namespace = $namespace;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->_namespace;
	}

	/**
	 * @param string $queue
	 *
	 * @return WorkQueue
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

	/**
	 * @param \Doctrine\ODM\CouchDB\DocumentManager $dm
	 *
	 * @return WorkQueue
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

}
