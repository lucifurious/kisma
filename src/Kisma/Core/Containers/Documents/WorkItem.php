<?php
/**
 * WorkItem.php
 * An item to be picked up and worked on
 *
 * @property string      $queue    The name of this queue in which this entry resides
 * @property string      $ownerId  An owner of this entry
 * @property mixed       $payload
 * @property string      $created
 * @property string      $updated
 * @property bool        $processed
 * @property mixed       $response
 */
namespace Kisma\Core\Containers\Documents;
use \Doctrine\ODM\CouchDB\Mapping\Annotations;

/**
 * @Document
 */
class WorkItem extends SeedDocument
{
	//*************************************************************************
	//* Fields
	//*************************************************************************

	/**
	 * @Index @Field(type="string")
	 * @var string
	 */
	private $_queue = 'default';
	/**
	 * @Index @Field(type="integer", jsonName="owner_id")
	 * @var int
	 */
	private $_ownerId;
	/**
	 * @Field(type="string")
	 * @var string|callable
	 */
	private $_handler;
	/**
	 * @Field(type="mixed")
	 * @var mixed
	 */
	private $_payload;
	/**
	 * @Field(type="boolean")
	 * @var bool
	 */
	private $_processed = false;
	/**
	 * @Field(type="boolean", jsonName="in_flight")
	 * @var bool
	 */
	private $_inFlight = false;
	/**
	 * @Field(type="mixed")
	 * @var mixed
	 */
	private $_response;
	/**
	 * @Field(type="integer", jsonName="created_at")
	 * @var int
	 */
	private $_createdAt;
	/**
	 * @Field(type="integer", jsonName="updated_at")
	 * @var int
	 */
	private $_updatedAt;
	/**
	 * @Field(type="integer", jsonName="processed_at")
	 * @var int
	 */
	private $_processedAt;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Set defaults
	 */
	public function __construct()
	{
		$this->_createdAt = time();
	}

	/**
	 * @param int $createdAt
	 *
	 * @return WorkItem
	 */
	public function setCreatedAt( $createdAt )
	{
		$this->_createdAt = $createdAt;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getCreatedAt()
	{
		return $this->_createdAt;
	}

	/**
	 * @param callable|string $handler
	 *
	 * @return WorkItem
	 */
	public function setHandler( $handler )
	{
		$this->_handler = $handler;

		return $this;
	}

	/**
	 * @return callable|string
	 */
	public function getHandler()
	{
		return $this->_handler;
	}

	/**
	 * @param boolean $inFlight
	 *
	 * @return WorkItem
	 */
	public function setInFlight( $inFlight )
	{
		$this->_inFlight = $inFlight;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getInFlight()
	{
		return $this->_inFlight;
	}

	/**
	 * @param int $ownerId
	 *
	 * @return WorkItem
	 */
	public function setOwnerId( $ownerId )
	{
		$this->_ownerId = $ownerId;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getOwnerId()
	{
		return $this->_ownerId;
	}

	/**
	 * @param mixed $payload
	 *
	 * @return WorkItem
	 */
	public function setPayload( $payload )
	{
		$this->_payload = $payload;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPayload()
	{
		return $this->_payload;
	}

	/**
	 * @param boolean $processed
	 *
	 * @return WorkItem
	 */
	public function setProcessed( $processed )
	{
		$this->_processed = $processed;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getProcessed()
	{
		return $this->_processed;
	}

	/**
	 * @param int $processedAt
	 *
	 * @return WorkItem
	 */
	public function setProcessedAt( $processedAt )
	{
		$this->_processedAt = $processedAt;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getProcessedAt()
	{
		return $this->_processedAt;
	}

	/**
	 * @param string $queue
	 *
	 * @return WorkItem
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
	 * @param mixed $response
	 *
	 * @return WorkItem
	 */
	public function setResponse( $response )
	{
		$this->_response = $response;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getResponse()
	{
		return $this->_response;
	}

	/**
	 * @param int $updatedAt
	 *
	 * @return WorkItem
	 */
	public function setUpdatedAt( $updatedAt )
	{
		$this->_updatedAt = $updatedAt;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getUpdatedAt()
	{
		return $this->_updatedAt;
	}

}
