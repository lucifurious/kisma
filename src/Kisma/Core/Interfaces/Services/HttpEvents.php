<?php
/**
 * HttpEvents.php
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 *         Copyright (c) 2012 Silverpop Systems, Inc.
 *         http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 * @filesource
 */
namespace Kisma\Core\Interfaces\Services;

/**
 * HttpEvents
 * Defines an interface the Http service class knows how to deal with
 */
interface HttpEvents
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const RequestReceived = 'kisma.core.services.http.request_received';

}
