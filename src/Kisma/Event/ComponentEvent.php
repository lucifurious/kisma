<?php
/**
 * @file
 * Component event class
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
 * ComponentEvent
 * Contains the events triggered by a component
 */
class ComponentEvent extends KismaEvent
{
	//*************************************************************************
	//* Class Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const AfterConstruct = 'after_construct';

	/**
	 * @var string
	 */
	const BeforeDestruct = 'before_destruct';

}
