<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2013 Jerry Ablan <jerryablan@gmail.com>
 *
 * Kisma(tm) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kisma(tm) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kisma(tm).  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Kisma\Core\Utility;

use Kisma\Core\Enums\HttpMethod;

/**
 * Curl
 * A kick-ass cURL wrapper
 */
class Curl extends HttpMethod
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var string
	 */
	protected static $_userName = null;
	/**
	 * @var string
	 */
	protected static $_password = null;
	/**
	 * @var int
	 */
	protected static $_hostPort = null;
	/**
	 * @var array The error of the last call
	 */
	protected static $_error = null;
	/**
	 * @var array The results of the last call
	 */
	protected static $_info = null;
	/**
	 * @var array Default cURL options
	 */
	protected static $_curlOptions = array();
	/**
	 * @var int The last http code
	 */
	protected static $_lastHttpCode = null;
	/**
	 * @var array The last response headers
	 */
	protected static $_lastResponseHeaders = null;
	/**
	 * @var string
	 */
	protected static $_responseHeaders = null;
	/**
	 * @var int
	 */
	protected static $_responseHeadersSize = null;
	/**
	 * @var bool Enable/disable logging
	 */
	protected static $_debug = true;
	/**
	 * @var bool If true, and response is "application/json" content-type, it will be returned decoded
	 */
	protected static $_autoDecodeJson = true;
	/**
	 * @var bool If true, auto-decoded response is returned as an array
	 */
	protected static $_decodeToArray = false;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param string          $url
	 * @param array|\stdClass $payload
	 * @param array           $curlOptions
	 *
	 * @return string|\stdClass
	 */
	public static function get( $url, $payload = array(), $curlOptions = array() )
	{
		return static::_httpRequest( static::Get, $url, $payload, $curlOptions );
	}

	/**
	 * @param string          $url
	 * @param array|\stdClass $payload
	 * @param array           $curlOptions
	 *
	 * @return bool|mixed|\stdClass
	 */
	public static function put( $url, $payload = array(), $curlOptions = array() )
	{
		return static::_httpRequest( static::Put, $url, $payload, $curlOptions );
	}

	/**
	 * @param string          $url
	 * @param array|\stdClass $payload
	 * @param array           $curlOptions
	 *
	 * @return bool|mixed|\stdClass
	 */
	public static function post( $url, $payload = array(), $curlOptions = array() )
	{
		return static::_httpRequest( static::Post, $url, $payload, $curlOptions );
	}

	/**
	 * @param string          $url
	 * @param array|\stdClass $payload
	 * @param array           $curlOptions
	 *
	 * @return bool|mixed|\stdClass
	 */
	public static function delete( $url, $payload = array(), $curlOptions = array() )
	{
		return static::_httpRequest( static::Delete, $url, $payload, $curlOptions );
	}

	/**
	 * @param string          $url
	 * @param array|\stdClass $payload
	 * @param array           $curlOptions
	 *
	 * @return bool|mixed|\stdClass
	 */
	public static function head( $url, $payload = array(), $curlOptions = array() )
	{
		return static::_httpRequest( static::Head, $url, $payload, $curlOptions );
	}

	/**
	 * @param string          $url
	 * @param array|\stdClass $payload
	 * @param array           $curlOptions
	 *
	 * @return bool|mixed|\stdClass
	 */
	public static function options( $url, $payload = array(), $curlOptions = array() )
	{
		return static::_httpRequest( static::Options, $url, $payload, $curlOptions );
	}

	/**
	 * @param string          $url
	 * @param array|\stdClass $payload
	 * @param array           $curlOptions
	 *
	 * @return bool|mixed|\stdClass
	 */
	public static function copy( $url, $payload = array(), $curlOptions = array() )
	{
		return static::_httpRequest( static::Copy, $url, $payload, $curlOptions );
	}

	/**
	 * @param string          $url
	 * @param array|\stdClass $payload
	 * @param array           $curlOptions
	 *
	 * @return bool|mixed|\stdClass
	 */
	public static function merge( $url, $payload = array(), $curlOptions = array() )
	{
		return static::_httpRequest( static::Merge, $url, $payload, $curlOptions );
	}

	/**
	 * @param string          $url
	 * @param array|\stdClass $payload
	 * @param array           $curlOptions
	 *
	 * @return bool|mixed|\stdClass
	 */
	public static function patch( $url, $payload = array(), $curlOptions = array() )
	{
		return static::_httpRequest( static::Patch, $url, $payload, $curlOptions );
	}

	/**
	 * @param string          $method
	 * @param string          $url
	 * @param array|\stdClass $payload
	 * @param array           $curlOptions
	 *
	 * @return string|\stdClass
	 */
	public static function request( $method, $url, $payload = array(), $curlOptions = array() )
	{
		return static::_httpRequest( $method, $url, $payload, $curlOptions );
	}

	/**
	 * @param string $method
	 * @param string $url
	 * @param array  $payload
	 * @param array  $curlOptions
	 *
	 * @throws \InvalidArgumentException
	 * @return bool|mixed|\stdClass
	 */
	protected static function _httpRequest( $method = self::Get, $url, $payload = array(), $curlOptions = array() )
	{
		if ( !static::contains( $method ) )
		{
			throw new \InvalidArgumentException( 'Invalid method "' . $method . '" specified.' );
		}

		//	Reset!
		static::$_lastResponseHeaders = static::$_lastHttpCode = static::$_error = static::$_info = $_tmpFile = null;

		//	Build a curl request...
		$_curl = curl_init( $url );

		//	Default CURL options for this method
		$_curlOptions = array(
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => true,
			CURLINFO_HEADER_OUT    => true,
			CURLOPT_SSL_VERIFYPEER => false,
		);

		//	Merge in the global options if any
		if ( !empty( static::$_curlOptions ) )
		{
			$curlOptions = array_merge(
				$curlOptions,
				static::$_curlOptions
			);
		}

		//	Add/override user options
		if ( !empty( $curlOptions ) )
		{
			foreach ( $curlOptions as $_key => $_value )
			{
				$_curlOptions[$_key] = $_value;
			}
		}

		if ( null !== static::$_userName || null !== static::$_password )
		{
			$_curlOptions[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
			$_curlOptions[CURLOPT_USERPWD] = static::$_userName . ':' . static::$_password;
		}

		switch ( $method )
		{
			case static::Get:
				//	Do nothing, like the goggles...
				break;

			case static::Put:
				$_payload = json_encode( !empty( $payload ) ? $payload : array() );

				$_tmpFile = tmpfile();
				fwrite( $_tmpFile, $_payload );
				rewind( $_tmpFile );

				$_curlOptions[CURLOPT_PUT] = true;
				$_curlOptions[CURLOPT_INFILE] = $_tmpFile;
				$_curlOptions[CURLOPT_INFILESIZE] = mb_strlen( $_payload );
				break;

			case static::Post:
				$_curlOptions[CURLOPT_POST] = true;
				$_curlOptions[CURLOPT_POSTFIELDS] = $payload;
				break;

			case static::Head:
				$_curlOptions[CURLOPT_NOBODY] = true;
				break;

			case static::Patch:
				$_curlOptions[CURLOPT_CUSTOMREQUEST] = static::Patch;
				$_curlOptions[CURLOPT_POSTFIELDS] = $payload;
				break;

			case static::Merge:
			case static::Delete:
				/** Merge && Delete have payloads, but they and Options/Copy need CURLOPT_CUSTOMREQUEST set so just fall through... */
				$_curlOptions[CURLOPT_POSTFIELDS] = $payload;

			case static::Options:
			case static::Copy:
				$_curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
				break;
		}

		if ( null !== static::$_hostPort && !isset( $_curlOptions[CURLOPT_PORT] ) )
		{
			$_curlOptions[CURLOPT_PORT] = static::$_hostPort;
		}

		//	Set our collected options
		curl_setopt_array( $_curl, $_curlOptions );

		//	Make the call!
		$_result = curl_exec( $_curl );

		static::$_info = curl_getinfo( $_curl );
		static::$_lastHttpCode = Option::get( static::$_info, 'http_code' );
		static::$_responseHeaders = curl_getinfo( $_curl, CURLINFO_HEADER_OUT );
		static::$_responseHeadersSize = curl_getinfo( $_curl, CURLINFO_HEADER_SIZE );

		if ( static::$_debug )
		{
			//	@todo Add debug output
		}

		if ( false === $_result )
		{
			static::$_error = array(
				'code'    => curl_errno( $_curl ),
				'message' => curl_error( $_curl ),
			);
		}
		elseif ( true === $_result )
		{
			//	Worked, but no data...
			$_result = null;
		}
		else
		{
			//      Split up the body and headers if requested
			if ( $_curlOptions[CURLOPT_HEADER] )
			{
				if ( false === strpos( $_result, "\r\n\r\n" ) || empty( static::$_responseHeadersSize ) )
				{
					$_headers = $_result;
					$_body = null;
				}
				else
				{
					$_headers = substr( $_result, 0, static::$_responseHeadersSize );
					$_body = substr( $_result, static::$_responseHeadersSize );
				}

				if ( $_headers )
				{
					static::$_lastResponseHeaders = array();
					$_raw = explode( "\r\n", $_headers );

					if ( !empty( $_raw ) )
					{
						$_first = true;

						foreach ( $_raw as $_line )
						{
							//	Skip the first line (HTTP/1.x response)
							if ( $_first || preg_match( '/^HTTP\/[0-9\.]+ [0-9]+/', $_line ) )
							{
								$_first = false;
								continue;
							}

							$_parts = explode( ':', $_line, 2 );

							if ( !empty( $_parts ) )
							{
								static::$_lastResponseHeaders[trim( $_parts[0] )] = count( $_parts ) > 1 ? trim( $_parts[1] ) : null;
							}
						}
					}
				}

				$_result = $_body;
			}

			//	Attempt to auto-decode inbound JSON
			if ( !empty( $_result ) && false !== stripos( Option::get( static::$_info, 'content_type' ), 'application/json', 0 ) )
			{
				try
				{
					if ( false !== ( $_json = @json_decode( $_result, static::$_decodeToArray ) ) )
					{
						$_result = $_json;
					}
				}
				catch ( \Exception $_ex )
				{
					//	Ignored
					Log::debug( 'Exception decoding result: ' . $_ex->getMessage() );
				}
			}

			//	Don't confuse error with empty data...
			if ( false === $_result )
			{
				$_result = null;
			}
		}

		@curl_close( $_curl );

		//	Close temp file if any
		if ( null !== $_tmpFile )
		{
			@fclose( $_tmpFile );
		}

		return $_result;
	}

	/**
	 * @return array
	 */
	public static function getErrorAsString()
	{
		if ( !empty( static::$_error ) )
		{
			return static::$_error['message'] . ' (' . static::$_error['code'] . ')';
		}

		return null;
	}

	/**
	 * @param array $error
	 *
	 * @return void
	 */
	protected static function _setError( $error )
	{
		static::$_error = $error;
	}

	/**
	 * @return array
	 */
	public static function getError()
	{
		return static::$_error;
	}

	/**
	 * @param int $hostPort
	 *
	 * @return void
	 */
	public static function setHostPort( $hostPort )
	{
		static::$_hostPort = $hostPort;
	}

	/**
	 * @return int
	 */
	public static function getHostPort()
	{
		return static::$_hostPort;
	}

	/**
	 * @param array $info
	 *
	 * @return void
	 */
	protected static function _setInfo( $info )
	{
		static::$_info = $info;
	}

	/**
	 * @param string $key          Leaving this null will return the entire structure, otherwise just the value for the supplied key
	 * @param mixed  $defaultValue The default value to return if the $key was not found
	 *
	 * @return array
	 */
	public static function getInfo( $key = null, $defaultValue = null )
	{
		return null === $key ? static::$_info : Option::get( static::$_info, $key, $defaultValue );
	}

	/**
	 * @param string $password
	 *
	 * @return void
	 */
	public static function setPassword( $password )
	{
		static::$_password = $password;
	}

	/**
	 * @return string
	 */
	public static function getPassword()
	{
		return static::$_password;
	}

	/**
	 * @param string $userName
	 *
	 * @return void
	 */
	public static function setUserName( $userName )
	{
		static::$_userName = $userName;
	}

	/**
	 * @return string
	 */
	public static function getUserName()
	{
		return static::$_userName;
	}

	/**
	 * @param array $curlOptions
	 *
	 * @return void
	 */
	public static function setCurlOptions( $curlOptions )
	{
		static::$_curlOptions = $curlOptions;
	}

	/**
	 * @return array
	 */
	public static function getCurlOptions()
	{
		return static::$_curlOptions;
	}

	/**
	 * @param int $lastHttpCode
	 */
	protected static function _setLastHttpCode( $lastHttpCode )
	{
		static::$_lastHttpCode = $lastHttpCode;
	}

	/**
	 * @return int
	 */
	public static function getLastHttpCode()
	{
		return static::$_lastHttpCode;
	}

	/**
	 * @param boolean $debug
	 */
	public static function setDebug( $debug )
	{
		static::$_debug = $debug;
	}

	/**
	 * @return boolean
	 */
	public static function getDebug()
	{
		return static::$_debug;
	}

	/**
	 * @param boolean $autoDecodeJson
	 */
	public static function setAutoDecodeJson( $autoDecodeJson )
	{
		static::$_autoDecodeJson = $autoDecodeJson;
	}

	/**
	 * @return boolean
	 */
	public static function getAutoDecodeJson()
	{
		return static::$_autoDecodeJson;
	}

	/**
	 * @param boolean $decodeToArray
	 */
	public static function setDecodeToArray( $decodeToArray )
	{
		static::$_decodeToArray = $decodeToArray;
	}

	/**
	 * @return boolean
	 */
	public static function getDecodeToArray()
	{
		return static::$_decodeToArray;
	}

	/**
	 * @return array
	 */
	public static function getLastResponseHeaders()
	{
		return static::$_lastResponseHeaders;
	}

	/**
	 * Returns the validated URL that has been called to get here
	 *
	 * @param bool $includeQuery If true, query string is included
	 * @param bool $includePath  If true, the uri path is included
	 *
	 * @return string
	 */
	public static function currentUrl( $includeQuery = true, $includePath = true )
	{
		//	Are we SSL? Check for load balancer protocol as well...
		$_port = intval( Option::get( $_SERVER, 'HTTP_X_FORWARDED_PORT', Option::get( $_SERVER, 'SERVER_PORT', 80 ) ) );
		$_protocol = Option::get( $_SERVER, 'HTTP_X_FORWARDED_PROTO', 'http' . ( Option::getBool( $_SERVER, 'HTTPS' ) ? 's' : null ) ) . '://';
		$_host = Option::get( $_SERVER, 'HTTP_X_FORWARDED_HOST', Option::get( $_SERVER, 'HTTP_HOST', gethostname() ) );
		$_parts = parse_url( $_protocol . $_host . Option::get( $_SERVER, 'REQUEST_URI' ) );

		if ( ( empty( $_port ) || !is_numeric( $_port ) ) && null !== ( $_parsePort = Option::get( $_parts, 'port' ) ) )
		{
			$_port = @intval( $_parsePort );
		}

		if ( null !== ( $_query = Option::get( $_parts, 'query' ) ) )
		{
			$_query = static::urlSeparator( $_query ) . http_build_query( explode( '&', $_query ) );
		}

		if ( false !== strpos( $_host, ':' ) || ( $_protocol == 'https://' && $_port == 443 ) || ( $_protocol == 'http://' && $_port == 80 ) )
		{
			$_port = null;
		}
		else
		{
			$_port = ':' . $_port;
		}

		if ( false !== strpos( $_host, ':' ) )
		{
			$_port = null;
		}

		$_currentUrl =
			$_protocol . $_host . $_port . ( true === $includePath ? Option::get( $_parts, 'path' ) : null ) . ( true === $includeQuery ? $_query : null );

		if ( \Kisma::get( 'debug.curl.current_url' ) )
		{
			Log::debug( 'Parsed current URL to be: ' . $_currentUrl, $_parts );
		}

		return $_currentUrl;
	}

	/**
	 * Builds an URL, properly appending the payload as the query string.
	 *
	 * @param string $url           The target URL
	 * @param array  $payload       The query string data. May be an array or object containing properties. The array form may be a simple one-dimensional
	 *                              structure, or an array of arrays (who in turn may contain other arrays).
	 * @param string $numericPrefix If numeric indices are used in the base array and this parameter is provided, it will be prepended to the numeric index for
	 *                              elements in the base array only.
	 *                              This is meant to allow for legal variable names when the data is decoded by PHP or another CGI application later on.
	 * @param string $argSeparator  Character to use to separate arguments. Defaults to '&'
	 * @param int    $encodingType  If encodingType is PHP_QUERY_RFC1738 (the default), then encoding is as application/x-www-form-urlencoded, spaces will be
	 *                              encoded with plus (+) signs
	 *                              If encodingType is PHP_QUERY_RFC3986, spaces will be encoded with %20
	 *
	 * @return string an URL-encoded string
	 */
	public static function buildUrl( $url, $payload = array(), $numericPrefix = null, $argSeparator = '&', $encodingType = PHP_QUERY_RFC1738 )
	{
		$_query = \http_build_query( $payload, $numericPrefix, $argSeparator, $encodingType );

		return $url . static::urlSeparator( $url, $argSeparator ) . $_query;
	}

	/**
	 * Returns the proper separator for an addition to the URL (? or &)
	 *
	 * @param string $url          The URL to test
	 * @param string $argSeparator Defaults to '&' but you can override
	 *
	 * @return string
	 */
	public static function urlSeparator( $url, $argSeparator = '&' )
	{
		return ( false === strpos( $url, '?', 0 ) ? '?' : $argSeparator );
	}

}
