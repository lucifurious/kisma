<?php
/**
 * @file
 * Framework event class
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
 * ApplicationEvent
 * Contains the events triggered by an application
 */
class ApplicationEvent extends KismaEvent
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
	const Initialize = 'initialize';

	/**
	 * The TERMINATE is triggered when the application is ending
	 *
	 * @var string
	 */
	const Terminate = 'terminate';

}
