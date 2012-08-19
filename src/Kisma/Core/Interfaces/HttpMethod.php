<?php
/**
 * HttpMethod.php
 */
namespace Kisma\Core\Interfaces;

/**
 * HttpMethod
 * Defines the available Http methods for CURL
 */
interface HttpMethod
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const Get = 'GET';
	/**
	 * @var string
	 */
	const Put = 'PUT';
	/**
	 * @var string
	 */
	const Head = 'HEAD';
	/**
	 * @var string
	 */
	const Post = 'POST';
	/**
	 * @var string
	 */
	const Delete = 'DELETE';
	/**
	 * @var string
	 */
	const Options = 'OPTIONS';
	/**
	 * @var string
	 */
	const Copy = 'COPY';
	/**
	 * @var string
	 */
	const Patch = 'PATCH';
	/**
	 * @var string
	 */
	const Trace = 'TRACE';
	/**
	 * @var string
	 */
	const Connect = 'CONNECT';
}
