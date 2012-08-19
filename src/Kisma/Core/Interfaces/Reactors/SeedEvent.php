<?php
/**
 * SeedEvent.php
 *
 * @description Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * @copyright   Copyright (c) 2009-2012 Jerry Ablan
 * @license     http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author      Jerry Ablan <get.kisma@gmail.com>
 */
namespace Kisma\Core\Interfaces\Reactors;

/**
 * SeedEvent
 * Defines the event interface for all seeds
 */
interface SeedEvent extends \Kisma\Core\Interfaces\Reactor
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const AfterConstruct = 'kisma.core.seed.after_construct';
	/**
	 * @var string
	 */
	const BeforeDestruct = 'kisma.core.seed.before_destruct';

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeDestruct( $event = null );

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onAfterConstruct( $event = null );

}
