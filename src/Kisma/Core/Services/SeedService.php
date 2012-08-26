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
 * onSuccess, onFailure, and onOther
 *
 * @property bool                              $initialized Set to true by service once initialized
 * @property \Kisma\Core\Interfaces\Dispatcher $dispatcher  The dispatcher, if any, who owns this service.
 */
abstract class SeedService extends \Kisma\Core\Seed implements \Kisma\Core\Interfaces\Events\Service
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var bool Set to true by service once initialized
	 */
	protected $_initialized = false;
	/**
	 * @var \Kisma\Core\Interfaces\Dispatcher
	 */
	protected $_dispatcher = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * When a service is constructed, this method is called once, automatically
	 *
	 * @param array $options
	 *
	 * @return bool
	 */
	public function initialize( $options = array() )
	{
		return $this->_initialized = true;
	}

	/**
	 * {@InheritDoc}
	 */
	public function publish( $eventName, $eventData = null )
	{
		$_event = new \Kisma\Core\Events\ServiceEvent( $this );

		if ( !is_array( $eventData ) )
		{
			$eventData = array( $eventData );
		}

		//	Build the request stack
		foreach ( $eventData as $_request )
		{
			if ( !( $_request instanceof \Kisma\Core\Services\SeedRequest ) )
			{
				throw new \InvalidArgumentException( '$eventData must be an array or a SeedRequest.' );
			}

			$_event->pushRequest( $_request );
		}

		return parent::publish( $eventName, $_event );
	}

	//*************************************************************************
	//* Default Event Handlers
	//*************************************************************************

	/**
	 * After the base object is constructed, call the service's initialize method
	 *
	 * @param \Kisma\Core\Events\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onAfterConstruct( $event = null )
	{
		return $this->_initialized ? : $this->initialize( $event->getData() );
	}

	/**
	 * Receives events from dispatchers
	 *
	 * @param \Kisma\Core\Events\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onDispatch( $event = null )
	{
		if ( null !== $event )
		{
			$_result = true;
			$this->_dispatcher = $event->getSource();

			while ( null !== ( $_request = $event->popRequest() ) )
			{
				$this->_dispatcher->
				$this->_processRequest( $_request, $event );
			}

			return $_result;
		}

		return true;
	}

	/**
	 * @param \Kisma\Core\Events\ServiceEvent $event
	 *
	 * @return bool Default implementation always returns true
	 */
	public function onSuccess( $event = null )
	{
		return true;
	}

	/**
	 * @param \Kisma\Core\Events\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onFailure( $event = null )
	{
		return true;
	}

}
