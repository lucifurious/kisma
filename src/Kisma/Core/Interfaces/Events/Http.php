<?php
/**
 * Http.php
 */
namespace Kisma\Core\Interfaces\Events;
/**
 * HttpEvent
 * Defines an interface the Http service class knows how to deal with
 */
interface Http
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const RequestReceived = 'kisma.core.services.http.request_received';

}
