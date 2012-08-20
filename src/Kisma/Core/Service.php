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
 * @property bool $initialized Set to true by service once initialized
 */
abstract class Service extends Seed
{
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
		$this->set( 'initialized', true );
		return true;
	}

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
		return $this->get( 'initialized' ) || $this->initialize( $event->getData() );
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
