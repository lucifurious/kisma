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
 * If an event handler sets {@link stopPropagation} to true, propagation will halt.
 */
class SeedEvent
{
	//**************************************************************************
	//* Private Members
	//**************************************************************************

	/**
	 * @var object The source of this event
	 */
	protected $_source;
	/**
	 * @var boolean Events will continue to propagate if this is "false"
	 */
	protected $_stopPropagation = false;
	/**
	 * @var mixed Any event data the sender wants to convey
	 */
	protected $_data;

	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * Constructor.
	 *
	 * @param object $source
	 * @param mixed  $data
	 */
	public function __construct( $source = null, $data = null )
	{
		$this->_source = $source;
		$this->_data = $data;
	}

	//**************************************************************************
	//* Properties
	//**************************************************************************

	/**
	 * @param mixed $data
	 *
	 * @return SeedEvent
	 */
	public function setData( $data )
	{
		$this->_data = $data;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->_data;
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
	 * @param boolean $stopPropagation
	 *
	 * @return SeedEvent
	 */
	public function setStopPropagation( $stopPropagation )
	{
		$this->_stopPropagation = $stopPropagation;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getStopPropagation()
	{
		return $this->_stopPropagation;
	}
}
