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
 * onSuccess, onFailure, and onComplete
 *
 * @property bool|int                            $state       The current state of the service
 * @property \Kisma\Core\Interfaces\ConsumerLike $consumer    The consumer, if any, who owns this service.
 */
abstract class SeedService extends \Kisma\Core\SeedBag implements \Kisma\Core\Interfaces\ServiceLike, \Kisma\Core\Interfaces\ServiceState
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
	 * Initialize the service. Called automatically
	 *
	 * @param \Kisma\Core\Interfaces\ConsumerLike $consumer
	 * @param \Kisma\Core\Interfaces\RequestLike  $request
	 *
	 * @return bool
	 */
	public function initialize( \Kisma\Core\Interfaces\ConsumerLike $consumer, $request = null )
	{
		$this->_consumer = $consumer;
		$this->_request = $request;

		return true;
	}

	//*************************************************************************
	//* Default Event Handlers
	//*************************************************************************

	/**
	 * Drives the service forward!
	 *
	 * After the base object is constructed, call the service's initialize method,
	 * then process the request
	 *
	 * @param \Kisma\Core\Events\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onAfterConstruct( $event = null )
	{
		if ( false === $this->initialize( $event->getSource(), $event->getData() ) )
		{
			return false;
		}

		$this->_state = self::Initialized;

		if ( false === ( $_result = $this->process( $this->_request ) ) )
		{
			$this->_state = self::Completed;
			$this->publish( self::Failure );
		}
		else
		{
			$this->_state = self::Completed;
			$this->publish( self::Success );
		}

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
