<?php
/**
 * SeedQueue.php
 */
namespace Kisma\Core;
/**
 * SeedQueue
 * An eerily simple FIFO queue
 */
class SeedQueue extends \Kisma\Core\Seed
{
	//**************************************************************************
	//* Private Members
	//**************************************************************************

	/**
	 * @var array The keeper of the cheese
	 */
	protected $_queue = array();

	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * Pushes the thing on to the end of the queue
	 *
	 * @param mixed $queueItem
	 *
	 * @return int The NEW number of requests in the queue
	 */
	public function push( $queueItem )
	{
		return array_push( $this->_queue, $queueItem );
	}

	/**
	 * Pop the thing off of the end of the queue
	 *
	 * @return mixed Returns null if the queue is empty
	 */
	public function pop()
	{
		return array_pop( $this->_queue );
	}
}
