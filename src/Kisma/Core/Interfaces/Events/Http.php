<?php
/**
 * Http.php
 */
namespace Kisma\Core\Interfaces\Events;
/**
 * Http
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
	const RequestReceived = 'kisma.core.services.network.http.request_received';

}
