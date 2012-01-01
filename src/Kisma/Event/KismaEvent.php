<?php
/**
 * @file
 * A generic Kisma event class
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
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
 * @property-read \Kisma\Components\Seed $target
 */
abstract class KismaEvent extends \Symfony\Component\EventDispatcher\Event
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * The object where this event was thrown
	 *
	 * @var \Kisma\Components\Seed|\Silex\Application
	 */
	protected $_target;

	/**
	 * @param \Kisma\Components\Seed|\Silex\Application $target The event in which the event occurred
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
	 * @return \Kisma\Components\Seed
	 */
	public function getTarget()
	{
		return $this->_target;
	}

}
