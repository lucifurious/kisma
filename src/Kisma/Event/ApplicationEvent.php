<?php
/**
 * @file
 * Framework event class
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
 * ApplicationEvents
 * Contains the events triggered by an application
 *
 * @property-read \Kisma\Kisma $app
 */
class ApplicationEvent extends \Symfony\Component\EventDispatcher\Event
{
	//*************************************************************************
	//* Class Constants
	//*************************************************************************

	/**
	 * Triggered as application initialization begins
	 * dispatching
	 *
	 * @var string
	 */
	const INITIALIZE = 'kisma.initialize';

	/**
	 * The TERMINATE is triggered when the application is ending
	 *
	 * @var string
	 */
	const TERMINATE = 'kisma.terminate';

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * The app in which this event was thrown
	 *
	 * @var \Kisma\Kisma
	 */
	protected $_app;

	/**
	 * @param \Kisma\Kisma $app
	 */
	public function __construct( \Kisma\Kisma $app )
	{
		$this->_app = $app;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * Returns the app in which this event was thrown
	 *
	 * @return \Kisma\Kisma
	 */
	public function getApp()
	{
		return $this->_app;
	}

}
