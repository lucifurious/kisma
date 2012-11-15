<?php
/**
 * QueueItem.php
 */
namespace Kisma\Core\Containers;
/**
 * QueueItem
 * A queue item. Nothing more than a subclass that sets some standard queue item properties
 *
 * @property string      $queue    The name of this queue in which this entry resides
 * @property string      $ownerId  An owner of this entry
 * @property mixed       $payload
 * @property string      $created
 * @property string      $updated
 * @property bool        $processed
 * @property mixed       $response
 *
 * @Document
 */
class QueueItem extends \Kisma\Core\Containers\Document
{
	//*************************************************************************
	//* Fields
	//*************************************************************************

	/**
	 * @Index
	 * @Field(type="string")
	 */
	private $_queue;
	/**
	 * @Index
	 * @Field(type="integer")
	 */
	private $_ownerId;
	/** @Field(type="string") */
	private $_handler;
	/** @Field(type="mixed") */
	private $_payload;
	/** @Field(type="boolean") */
	private $_processed;
	/** @Field(type="mixed") */
	private $_response;
	/** @Field(type="string") */
	private $_created;
	/** @Field(type="string") */
	private $_updated;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param $created
	 *
	 * @return QueueItem
	 */
	public function setCreated( $created )
	{
		$this->_created = $created;

		return $this;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	/**
	 * @param $handler
	 *
	 * @return QueueItem
	 */
	public function setHandler( $handler )
	{
		$this->_handler = $handler;

		return $this;
	}

	public function getHandler()
	{
		return $this->_handler;
	}

	/**
	 * @param $ownerId
	 *
	 * @return QueueItem
	 */
	public function setOwnerId( $ownerId )
	{
		$this->_ownerId = $ownerId;

		return $this;
	}

	public function getOwnerId()
	{
		return $this->_ownerId;
	}

	/**
	 * @param $payload
	 *
	 * @return QueueItem
	 */
	public function setPayload( $payload )
	{
		$this->_payload = $payload;

		return $this;
	}

	public function getPayload()
	{
		return $this->_payload;
	}

	/**
	 * @param $processed
	 *
	 * @return QueueItem
	 */
	public function setProcessed( $processed )
	{
		$this->_processed = $processed;

		return $this;
	}

	public function getProcessed()
	{
		return $this->_processed;
	}

	/**
	 * @param $queue
	 *
	 * @return QueueItem
	 */
	public function setQueue( $queue )
	{
		$this->_queue = $queue;

		return $this;
	}

	public function getQueue()
	{
		return $this->_queue;
	}

	/**
	 * @param $response
	 *
	 * @return QueueItem
	 */
	public function setResponse( $response )
	{
		$this->_response = $response;

		return $this;
	}

	public function getResponse()
	{
		return $this->_response;
	}

	/**
	 * @param $updated
	 *
	 * @return QueueItem
	 */
	public function setUpdated( $updated )
	{
		$this->_updated = $updated;

		return $this;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}

}
