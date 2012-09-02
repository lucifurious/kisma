<?php
/**
 * SeedDispatcher.php
 */
namespace Kisma\Core\Dispatchers;
/**
 * SeedDispatcher
 * The base class for all dispatchers.
 *
 * A dispatcher links inbound service requests with available services.
 *
 * P.S. I love the name! Reminds of The Big Lebowski!! Specifically the
 * part where Maude Lebowski wanted him only for his "seed dispatching"
 * capabilities. heh. vagina.
 *
 * @property \Kisma\Core\Interfaces\RequestSource $source   The source of the request
 * @property \Kisma\Core\Services\SeedService[]   $services The services which are recipients of the dispatcher
 */
abstract class SeedDispatcher extends \Kisma\Core\Seed implements \Kisma\Core\Interfaces\Events\Dispatcher, \Kisma\Core\Interfaces\RequestSource
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Kisma\Core\Interfaces\RequestSource
	 */
	protected $_source = null;
	/**
	 * @var \Kisma\Core\Services\SeedService[]
	 */
	protected $_services = array();

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array|\Kisma\Core\Services\SeedService $settings
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( $settings = array() )
	{
		if ( empty( $settings ) )
		{
			$settings = array();
		}

		//	Allow for passing in a service
		if ( $settings instanceof \Kisma\Core\Interfaces\Events\Service )
		{
			$this->registerService( $settings );
			$settings = array();
		}

		//	Register any services passed in as a setting
		foreach ( \Kisma\Core\Utility\Option::get( $settings, 'services', array(), true ) as $_service )
		{
			$this->registerService( $_service );
		}

		//	Otherwise it's bogus
		if ( !is_array( $settings ) )
		{
			throw new \InvalidArgumentException( 'Hey dude, this $settings thing you passed to me, it\'s bogus: ' . print_r( $settings, true ) );
		}

		parent::__construct( $settings );
	}

	/**
	 * Registers a service with the dispatcher
	 *
	 * @param string|\Kisma\Core\Services\SeedService $service
	 *
	 * @throws \InvalidArgumentException
	 */
	public function registerService( $service )
	{
		if ( is_string( $service ) )
		{
			$_service = new $service();
		}
		elseif ( $service instanceof \Kisma\Core\Services\SeedService )
		{
			$_service = $service;
		}
		else
		{
			throw new \InvalidArgumentException( '$service must be a class name or a subclass of SeedService.' );
		}

		$this->_service[$_service->getId()] = $_service;
	}

	/**
	 * @param \Kisma\Core\Interfaces\ServiceRequester $requester That which requests this service
	 * @param \Kisma\Core\Services\SeedRequest        $request   The request itself (like raab)
	 * @param string                                  $serviceId If set, dispatches request to a specific, single service
	 *
	 * @return mixed
	 */
	public function dispatchRequest( $requester, $request, $serviceId = null )
	{
		$_result = array();

		foreach ( $this->_services as $_service )
		{
			if ( null === $serviceId || $serviceId == $_service->getId() )
			{
				if ( false === $_service->processRequest( $request, $requester ) )
				{
					break;
				}
			}

			unset( $_service );
		}

		return $_result;
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	//*************************************************************************
	//* Properties
	//*************************************************************************

}
