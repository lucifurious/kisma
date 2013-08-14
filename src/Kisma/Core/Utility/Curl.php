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

		//	Defaults
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

			case static::Delete:
				$_curlOptions[CURLOPT_CUSTOMREQUEST] = static::Merge;
				$_curlOptions[CURLOPT_POSTFIELDS] = $payload;
				break;

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
			if ( !empty( $_result ) && 'application/json' == Option::get( static::$_info, 'content_type' ) )
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
				}
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
	 * @return string
	 */
	public static function currentUrl()
	{
		//	Are we SSL? Check for load balancer protocol as well...
		$_port = Option::get( $_SERVER, 'HTTP_X_FORWARDED_PORT', Option::get( $_SERVER, 'SERVER_PORT', 80 ) );
		$_proto = Option::get( $_SERVER, 'HTTP_X_FORWARDED_PROTO', 'http' . ( Option::getBool( $_SERVER, 'HTTPS' ) ? 's' : null ) ) . '://';
		$_host = Option::get( $_SERVER, 'HTTP_X_FORWARDED_HOST', Option::get( $_SERVER, 'HTTP_HOST', gethostname() ) );
		$_parts = parse_url( $_proto . $_host . Option::get( $_SERVER, 'REQUEST_URI' ) );

		if ( null !== ( $_query = Option::get( $_parts, 'query' ) ) )
		{
			$_query = '?' . http_build_query( explode( '&', $_query ) );
		}

		return $_proto . $_host . ( $_port != 80 ? ':' . $_port : null ) . Option::get( $_parts, 'path' ) . $_query;
	}
}
