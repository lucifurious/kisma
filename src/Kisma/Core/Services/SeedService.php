<?php
/**
 * SeedService.php
 */
namespace Kisma\Core\Services;

/**
 * SeedService
 * The base class for services provided
 *
 * Provides two event handlers:
 *
 * onBeforeRequest and onAfterRequest which are called before and after
 * a service request is processed, respectively.
 *
 * @property bool $initialized Set to true by service once initialized
 */
abstract class SeedService extends \Kisma\Core\Seed
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var bool Set to true by service once initialized
	 */
	protected $_initialized = false;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * When a service is constructed, this method is called by default
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
	//* Event Handlers
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
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool Default implementation always returns true
	 */
	public function onBeforeServiceCall( $event = null )
	{
		return true;
	}

	/**
	 * @return array
	 */
	public function getDefaultSettings()
	{
		return array(
			'initialized' => false,
		);
	}

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool Default implementation always returns true
	 */
	public function onAfterServiceCall( $event = null )
	{
		return true;
	}

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool Default implementation always returns true
	 */
	public function onSuccess( $event = null )
	{
		return true;
	}

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onFailure( $event = null )
	{
		return true;
	}
}
