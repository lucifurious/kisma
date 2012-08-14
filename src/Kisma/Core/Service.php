<?php
/**
 * Service.php
 *
 * @description Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * @copyright   Copyright (c) 2009-2012 Jerry Ablan
 * @license     http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author      Jerry Ablan <get.kisma@gmail.com>
 */
namespace Kisma\Core;

/**
 * Service
 * The base class for services provided
 *
 * Provides two event handlers:
 *
 * onBeforeServiceCall and onAfterServiceCall which are called before and after
 * the service is run, respectively.
 *
 * @property string $serviceName The name of this service
 */
abstract class Service extends Seed implements \Kisma\Core\Interfaces\ServiceEvents
{
	//********************************************************************************
	//* Private Members
	//********************************************************************************

	/**
	 * @var string The name of this service
	 */
	protected $_serviceName = null;
	/**
	 * @var string The tag of this service
	 */
	protected $_serviceTag = null;

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
	abstract public function initialize( $options = array() );

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * After the base object is constructed, call the service's initialize method
	 *
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onAfterConstruct( $event = null )
	{
		return
			(
				parent::onAfterConstruct( $event ) && $this->initialize( $event->getData() )
			);
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

	//********************************************************************************
	//* Property Accessors
	//********************************************************************************

	/**
	 * @param string $serviceName
	 *
	 * @return \Kisma\Core\Service
	 */
	public function setServiceName( $serviceName )
	{
		$this->_serviceName = $serviceName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getServiceName()
	{
		return $this->_serviceName;
	}

}
