<?php
/**
 * @file
 * Controller event class
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/lucifurious/kisma/)
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
 * ControllerEvent
 * Contains the events triggered by a controller
 */
class ControllerEvent extends KismaEvent
{
	//*************************************************************************
	//* Class Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const AfterAction = 'after_action';

	/**
	 * @var string
	 */
	const BeforeAction = 'before_action';

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var mixed The result of the event
	 */
	protected $_result = null;

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param mixed $result
	 * @return \Kisma\Event\ControllerEvent
	 */
	public function setResult( $result )
	{
		$this->_result = $result;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getResult()
	{
		return $this->_result;
	}
}
