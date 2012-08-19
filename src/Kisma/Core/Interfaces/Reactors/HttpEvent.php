<?php
/**
 * HttpEvent.php
 * Standard ANSI color attributes
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2012 Jerry Ablan
 * @license   http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author    Jerry Ablan <get.kisma@gmail.com>
 */
namespace Kisma\Core\Interfaces\Reactors;
/**
 * HttpEvent
 * Defines an interface the Http service class knows how to deal with
 */
interface HttpEvent extends \Kisma\Core\Interfaces\Reactor
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const RequestReceived = 'kisma.core.services.http.request_received';

}
