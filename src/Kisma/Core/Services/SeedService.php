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
 * @property bool|int                        $state       The current state of the service
 * @property \Kisma\Core\Interfaces\Consumer $consumer    The consumer, if any, who owns this service.
 */
abstract class SeedService extends \Kisma\Core\Seed implements \Kisma\Core\Interfaces\Service, \Kisma\Core\Interfaces\Services\ServiceState
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Kisma\Core\Interfaces\Consumer
	 */
	protected $_consumer = null;
	/**
	 * @var bool|int The current state of the service
	 */
	protected $_state = self::Uninitialized;

	//*************************************************************************
	//* Interface Methods
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function initialize( $consumer = null )
	{
		$this->_consumer = $consumer;

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

		if ( false === ( $_result = $this->processRequest( $this->_request ) ) )
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
	 * @param \Kisma\Core\Interfaces\Consumer $consumer
	 *
	 * @return SeedService
	 */
	public function setConsumer( $consumer )
	{
		$this->_consumer = $consumer;

		return $this;
	}

	/**
	 * @return \Kisma\Core\Interfaces\Consumer
	 */
	public function getConsumer()
	{
		return $this->_consumer;
	}

	/**
	 * @param bool|int $state
	 *
	 * @return SeedService
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

}
