<?php
/**
 * WorkItem.php
 * An item to be picked up and worked on
 */
namespace Kisma\Core\Containers\Documents;
use Doctrine\ODM\CouchDB\Mapping\Annotations;

/**
 * @Document
 * @Index
 */
class WorkItem extends SeedDocument
{
	//*************************************************************************
	//* Fields
	//*************************************************************************

	/**
	 * @Index
	 * @Field(type="string")
	 * @var string
	 */
	private $_queue = 'default';
	/**
	 * @ReferenceOne(targetDocument="Session")
	 * @var Session
	 */
	private $_owner;
	/**
	 * @Field(type="mixed")
	 * @var mixed
	 */
	private $_handler;
	/**
	 * @Field(type="integer")
	 * @var int How to interpret the $handler (i.e. PHP Class, bash script, etc.)
	 */
	private $_handle_via = 0;
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
	 * @param int $handle_via
	 *
	 * @return WorkItem
	 */
	public function setHandleVia( $handle_via )
	{
		$this->_handle_via = $handle_via;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getHandleVia()
	{
		return $this->_handle_via;
	}

	/**
	 * @param mixed $handler
	 *
	 * @return WorkItem
	 */
	public function setHandler( $handler )
	{
		$this->_handler = $handler;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getHandler()
	{
		return $this->_handler;
	}

	/**
	 * @param boolean $in_flight
	 *
	 * @return WorkItem
	 */
	public function setInFlight( $in_flight )
	{
		$this->_in_flight = $in_flight;

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
	 * @param \Kisma\Core\Containers\Documents\Session $owner
	 *
	 * @return WorkItem
	 */
	public function setOwner( $owner )
	{
		$this->_owner = $owner;

		return $this;
	}

	/**
	 * @return \Kisma\Core\Containers\Documents\Session
	 */
	public function getOwner()
	{
		return $this->_owner;
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
	 * @param string $processed_at
	 *
	 * @return WorkItem
	 */
	public function setProcessedAt( $processed_at )
	{
		$this->_processed_at = $processed_at;

		return $this;
	}

	/**
	 * @return string
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
