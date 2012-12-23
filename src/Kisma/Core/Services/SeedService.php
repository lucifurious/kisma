<?php
/**
 * SeedService.php
 */
namespace Kisma\Core\Services;
/**
 * SeedService
 * The base class for services provided
 *
 * Provides three event handlers:
 *
 * @event onSuccess Raised after a success
 * @event onFailure Raised after a failure
 * @event onComplete Raised upon completion of a service call
 */
use Kisma\Core\SeedBag;
use Kisma\Core\Interfaces\ServiceLike;
use Kisma\Core\Interfaces\ServiceState;
use Kisma\Core\Interfaces\ConsumerLike;

abstract class SeedService extends SeedBag implements ServiceLike, ServiceState
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Kisma\Core\Interfaces\ConsumerLike
	 */
	protected $_consumer = null;
	/**
	 * @var bool|int The current state of the service
	 */
	protected $_state = self::Uninitialized;
	/**
	 * @var \Kisma\Core\Interfaces\RequestLike
	 */
	protected $_request = null;

	//*************************************************************************
	//* Interface Methods
	//*************************************************************************

	/**
	 * Create the service
	 *
	 * @param \Kisma\Core\Interfaces\ConsumerLike $consumer
	 * @param array                               $settings
	 */
	public function __construct( ConsumerLike $consumer, $settings = array() )
	{
		parent::__construct( $settings );

		$this->_consumer = $consumer;
		$this->_state = self::Initialized;
	}

	/**
	 * Default implementation
	 */
	public function perform()
	{
		//	Service complete
		$this->publish( self::Complete );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Interfaces\ConsumerLike $consumer
	 *
	 * @return \Kisma\Core\Services\SeedService
	 */
	public function setConsumer( $consumer )
	{
		$this->_consumer = $consumer;

		return $this;
	}

	/**
	 * @return \Kisma\Core\Interfaces\ConsumerLike
	 */
	public function getConsumer()
	{
		return $this->_consumer;
	}

	/**
	 * @param bool|int $state
	 *
	 * @return \Kisma\Core\Services\SeedService
	 */
	public function setState( $state )
	{
		$this->_state = $state;

		return $this;
	}

	/**
	 * @return bool|int
	 */
	public function getState()
	{
		return $this->_state;
	}

	/**
	 * @param \Kisma\Core\Interfaces\RequestLike $request
	 *
	 * @return \Kisma\Core\Services\SeedService
	 */
	public function setRequest( $request )
	{
		$this->_request = $request;

		return $this;
	}

	/**
	 * @return \Kisma\Core\Interfaces\RequestLike
	 */
	public function getRequest()
	{
		return $this->_request;
	}

}
