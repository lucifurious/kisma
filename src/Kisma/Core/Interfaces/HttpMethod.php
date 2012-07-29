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
	 * @const string Available HTTP Methods
	 */
	const Get = 'GET';
	/**
	 * @const string Available HTTP Methods
	 */
	const Put = 'PUT';
	/**
	 * @const string Available HTTP Methods
	 */
	const Head = 'HEAD';
	/**
	 * @const string Available HTTP Methods
	 */
	const Post = 'POST';
	/**
	 * @const string Available HTTP Methods
	 */
	const Delete = 'DELETE';
	/**
	 * @const string Available HTTP Methods
	 */
	const Options = 'OPTIONS';
	/**
	 * @const string Available HTTP Methods
	 */
	const Copy = 'COPY';
	/**
	 * @const string Available HTTP Methods
	 */
	const Patch = 'PATCH';
}
