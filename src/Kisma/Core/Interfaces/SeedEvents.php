<?php
/**
 * SeedEvents.php
 *
 * @description Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * @copyright   Copyright (c) 2009-2012 Jerry Ablan
 * @license     http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author      Jerry Ablan <get.kisma@gmail.com>
 */
namespace Kisma\Core\Interfaces;

/**
 * SeedEvents
 * Defines the event interface for all seeds
 */
interface SeedEvents
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const AfterConstruct = 'kisma.foundation.events.after_construct';
	/**
	 * @var string
	 */
	const BeforeDestruct = 'kisma.foundation.events.before_destruct';
}
