<?php
/**
 * HttpResponse.php
 */
namespace Kisma\Core\Enums;
/**
 * HttpResponse
 * Available HTTP responses
 */
class HttpResponse extends SeedEnum implements \Kisma\Core\Interfaces\HttpResponse
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The default: 200 OK
	 */
	const __default = self::Ok;
}
