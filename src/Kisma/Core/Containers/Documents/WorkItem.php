<?php
/**
 * WorkItem.php
 * An item to be picked up and worked on
 */
namespace Kisma\Core\Containers\Documents;
use \Doctrine\ODM\CouchDB\Mapping\Annotations;

/**
 * @Document @Index
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
	 * @Index @Field(type="integer")
	 * @var int
	 */
	private $_owner_id;
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
	 * @Field(type="boolean")
	 * @var bool
	 */
	private $_in_flight = false;
	/**
	 * @Field(type="mixed")
	 * @var mixed
	 */
	private $_response;
	/**
	 * @Field(type="string")
	 * @var string
	 */
	private $_processed_at;

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
		$this->_in_flight = $inFlight;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getInFlight()
	{
		return $this->_in_flight;
	}

	/**
	 * @param int $ownerId
	 *
	 * @return WorkItem
	 */
	public function setOwnerId( $ownerId )
	{
		$this->_owner_id = $ownerId;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getOwnerId()
	{
		return $this->_owner_id;
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
		$this->_processed_at = $processedAt;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getProcessedAt()
	{
		return $this->_processed_at;
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

}
