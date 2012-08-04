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

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * @param \Kisma\Event\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeServiceCall( \Kisma\Event\ServiceEvent $event )
	{
		//	Default implementation
		return true;
	}

	/**
	 * @param \Kisma\Event\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onAfterServiceCall( \Kisma\Event\ServiceEvent $event )
	{
		//	Default implementation
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
