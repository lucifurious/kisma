<?php
/**
 * @file
 * A generic Kisma event class
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Events
 * @package kisma.events
 * @since 1.0.0
 *
 * @ingroup events
 */

namespace Kisma\Event;

/**
 * KismaEvent
 * Wrapper for an event triggered within Kisma
 *
 * @property-read \Kisma\Core\Seed $target
 */
abstract class KismaEvent extends \Symfony\Component\EventDispatcher\Event implements \Kisma\IContainer
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * The object where this event was thrown
	 *
	 * @var \Kisma\Core\Seed|\Silex\Application|array
	 */
	protected $_target;

	/**
	 * @param array|\Kisma\Core\Seed|\Silex\Application $target The event in which the event occurred
	 */
	public function __construct( $target )
	{
		$this->_target = $target;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * Returns the app in which this event was thrown
	 *
	 * @return \Kisma\Core\Seed
	 */
	public function getTarget()
	{
		return $this->_target;
	}

}
