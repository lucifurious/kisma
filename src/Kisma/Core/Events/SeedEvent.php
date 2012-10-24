<?php
/**
 * SeedEvent.php
 */
namespace Kisma\Core\Events;
/**
 * SeedEvent
 * The base class for Kisma events
 *
 * It encapsulates the parameters associated with an event.
 * The {@link source} property describes who raised the event.
 *
 * If an event handler calls the kill() method, propagation will halt.
 */
class SeedEvent extends \SplPriorityQueue
{
	//**************************************************************************
	//* Members
	//**************************************************************************

	/**
	 * @var int My own private Idaho.
	 */
	private $_id = null;
	/**
	 * @var string A user-defined event ID
	 */
	protected $_eventId = null;
	/**
	 * @var string
	 */
	protected $_eventTag = null;
	/**
	 * @var object The source of this event
	 */
	protected $_source;
	/**
	 * @var mixed Event payload
	 */
	protected $_payload;
	/**
	 * @var bool Set to TRUE to halt propagation
	 */
	protected $_kill = false;

	//**************************************************************************
	//* Methods
	//**************************************************************************

	/**
	 * Constructor.
	 *
	 * @param object $source
	 * @param mixed  $data
	 */
	public function __construct( $source = null, $data = null )
	{
		$this->_setId();

		$this->_source = $source;
		$this->_data = $data;
	}

	/**
	 * @param callable|callable[] $listener One or more handlers
	 * @param int                 $priority
	 *
	 * @throws \InvalidArgumentException
	 * @return \Kisma\Core\Events\SeedEvent
	 */
	public function subscribe( $listener, $priority = 0 )
	{
		if ( !is_array( $listener ) )
		{
			$listener = array( $listener );
		}

		foreach ( $listener as $_handler )
		{
			if ( !is_callable( $_handler ) )
			{
				throw new \InvalidArgumentException( 'Invalid listener provided.' );
			}

			$this->insert( $_handler, $priority );
		}

		return $this;
	}

	/**
	 * @param string $eventName
	 * @param mixed  $eventData
	 *
	 * @return mixed|array
	 */
	public function publish( $eventName, $eventData = null )
	{
		$this->top();
		$this->setExtractFlags( static::EXTR_DATA );

		$_results = array();

		while ( $this->valid() )
		{
			$_handler = $this->current();

			$_results[] = call_user_func( $_handler, $this );

			if ( $this->wasKilled() )
			{
				break;
			}

			$this->next();
		}

		return $_results;
	}

	/**
	 * @return bool
	 */
	public function wasKilled()
	{
		return ( false !== $this->_kill );
	}

	/**
	 * Handles scalar comparisons for priority
	 *
	 * @param mixed $first
	 * @param mixed $second
	 *
	 * @return int
	 */
	public function compare( $first, $second )
	{
		return
			$first === $second ?
				0
				:
				( $first < $second ?
					-1
					:
					1
				);
	}

	//**************************************************************************
	//* Properties
	//**************************************************************************

	/**
	 * Sets the internal event ID
	 */
	private function _setId()
	{
		static $_serial = 0;

		$this->_id = ++$_serial;
	}

	/**
	 * @param mixed $payload
	 *
	 * @return SeedEvent
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
	 * @param \Kisma\Core\Seed $source
	 *
	 * @return SeedEvent
	 */
	public function setSource( $source )
	{
		$this->_source = $source;

		return $this;
	}

	/**
	 * @return \Kisma\Core\Seed
	 */
	public function getSource()
	{
		return $this->_source;
	}

	/**
	 * @param string $eventId
	 *
	 * @return SeedEvent
	 */
	public function setEventId( $eventId )
	{
		$this->_eventId = $eventId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEventId()
	{
		return $this->_eventId;
	}

	/**
	 * @param string $eventTag
	 *
	 * @return SeedEvent
	 */
	public function setEventTag( $eventTag )
	{
		$this->_eventTag = $eventTag;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEventTag()
	{
		return $this->_eventTag;
	}

	/**
	 * @param boolean $kill
	 *
	 * @return SeedEvent
	 */
	public function setKill( $kill )
	{
		$this->_kill = $kill;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getKill()
	{
		return $this->_kill;
	}

}
