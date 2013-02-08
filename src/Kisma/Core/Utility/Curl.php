<?php
/**
 * Curl.php
 */
namespace Kisma\Core\Utility;

/**
 * Curl
 * A kick-ass cURL wrapper
 */
class Curl extends \Kisma\Core\Enums\HttpMethod
{
	//*************************************************************************
	//* Private Members
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
	//* Public Methods
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
		return self::_httpRequest( self::Get, $url, $payload, $curlOptions );
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
		return self::_httpRequest( self::Put, $url, $payload, $curlOptions );
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
		return self::_httpRequest( self::Post, $url, $payload, $curlOptions );
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
		return self::_httpRequest( self::Delete, $url, $payload, $curlOptions );
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
		return self::_httpRequest( self::Head, $url, $payload, $curlOptions );
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
		return self::_httpRequest( self::Options, $url, $payload, $curlOptions );
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
		return self::_httpRequest( self::Copy, $url, $payload, $curlOptions );
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
		return self::_httpRequest( self::Patch, $url, $payload, $curlOptions );
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
		return self::_httpRequest( $method, $url, $payload, $curlOptions );
	}

	//**************************************************************************
	//* Private Methods
	//**************************************************************************

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
		if ( !self::contains( $method ) )
		{
			throw new \InvalidArgumentException( 'Invalid method "' . $method . '" specified.' );
		}

		//	Reset!
		self::$_lastResponseHeaders = self::$_lastHttpCode = self::$_error = self::$_info = $_tmpFile = null;

		//	Build a curl request...
		$_curl = curl_init( $url );

		//	Defaults
		$_curlOptions = array(
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => true,
			CURLOPT_SSL_VERIFYPEER => false,
		);

		//	Merge in the global options if any
		if ( !empty( self::$_curlOptions ) )
		{
			$curlOptions = array_merge(
				$curlOptions,
				self::$_curlOptions
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

		if ( null !== self::$_userName || null !== self::$_password )
		{
			$_curlOptions[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
			$_curlOptions[CURLOPT_USERPWD] = self::$_userName . ':' . self::$_password;
		}

		switch ( $method )
		{
			case self::Get:
				//	Do nothing, like the goggles...
				break;

			case self::Put:
				$_payload = json_encode( !empty( $payload ) ? $payload : array() );

				$_tmpFile = tmpfile();
				fwrite( $_tmpFile, $_payload );
				rewind( $_tmpFile );

				$_curlOptions[CURLOPT_PUT] = true;
				$_curlOptions[CURLOPT_INFILE] = $_tmpFile;
				$_curlOptions[CURLOPT_INFILESIZE] = mb_strlen( $_payload );
				break;

			case self::Post:
				$_curlOptions[CURLOPT_POST] = true;
				$_curlOptions[CURLOPT_POSTFIELDS] = $payload;
				break;

			case self::Head:
				$_curlOptions[CURLOPT_NOBODY] = true;
				break;

			case self::Patch:
				$_curlOptions[CURLOPT_CUSTOMREQUEST] = self::Patch;
				$_curlOptions[CURLOPT_POSTFIELDS] = $payload;
				break;

			case self::Delete:
			case self::Options:
			case self::Copy:
				$_curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
				break;
		}

		if ( null !== self::$_hostPort && !isset( $_curlOptions[CURLOPT_PORT] ) )
		{
			$_curlOptions[CURLOPT_PORT] = self::$_hostPort;
		}

		//	Set our collected options
		curl_setopt_array( $_curl, $_curlOptions );

		//	Make the call!
		$_result = curl_exec( $_curl );

		self::$_info = curl_getinfo( $_curl );
		self::$_lastHttpCode = Option::get( self::$_info, 'http_code' );

		if ( self::$_debug )
		{
			//	@todo Add debug output
		}

		if ( false === $_result )
		{
			self::$_error = array(
				'code'    => curl_errno( $_curl ),
				'message' => curl_error( $_curl ),
			);
		}
		elseif ( true === $_result )
		{
			//	Worked, but no data...
			$_result = null;
		}

		//	Split up the body and headers if requested
		if ( $_curlOptions[CURLOPT_HEADER] )
		{
			static::$_lastResponseHeaders = array();

			list( $_headers, $_body ) = explode( "\r\n\r\n", $_result, 2 );

			if ( $_headers )
			{
				$_raw = explode( "\r\n", $_headers );

				if ( !empty( $_raw ) )
				{
					foreach ( $_raw as $_line )
					{
						$_parts = explode( ':', $_line, 1 );

						if ( !empty( $_parts ) )
						{
							static::$_lastResponseHeaders[trim( $_parts[0] )] = trim( $_parts[1] );
						}
					}
				}
			}

			$_result = $_body;
		}

		//	Attempt to auto-decode inbound JSON
		if ( !empty( $_result ) && 'application/json' == Option::get( self::$_info, 'content_type' ) )
		{
			try
			{
				if ( false !== ( $_json = @json_decode( $_result, self::$_decodeToArray ) ) )
				{
					$_result = $_json;
				}
			}
			catch ( \Exception $_ex )
			{
				//	Ignored
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
		if ( !empty( self::$_error ) )
		{
			return self::$_error['message'] . ' (' . self::$_error['code'] . ')';
		}

		return null;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param array $error
	 *
	 * @return void
	 */
	protected static function _setError( $error )
	{
		self::$_error = $error;
	}

	/**
	 * @return array
	 */
	public static function getError()
	{
		return self::$_error;
	}

	/**
	 * @param int $hostPort
	 *
	 * @return void
	 */
	public static function setHostPort( $hostPort )
	{
		self::$_hostPort = $hostPort;
	}

	/**
	 * @return int
	 */
	public static function getHostPort()
	{
		return self::$_hostPort;
	}

	/**
	 * @param array $info
	 *
	 * @return void
	 */
	protected static function _setInfo( $info )
	{
		self::$_info = $info;
	}

	/**
	 * @return array
	 */
	public static function getInfo()
	{
		return self::$_info;
	}

	/**
	 * @param string $password
	 *
	 * @return void
	 */
	public static function setPassword( $password )
	{
		self::$_password = $password;
	}

	/**
	 * @return string
	 */
	public static function getPassword()
	{
		return self::$_password;
	}

	/**
	 * @param string $userName
	 *
	 * @return void
	 */
	public static function setUserName( $userName )
	{
		self::$_userName = $userName;
	}

	/**
	 * @return string
	 */
	public static function getUserName()
	{
		return self::$_userName;
	}

	/**
	 * @param array $curlOptions
	 *
	 * @return void
	 */
	public static function setCurlOptions( $curlOptions )
	{
		self::$_curlOptions = $curlOptions;
	}

	/**
	 * @return array
	 */
	public static function getCurlOptions()
	{
		return self::$_curlOptions;
	}

	/**
	 * @param int $lastHttpCode
	 */
	protected static function _setLastHttpCode( $lastHttpCode )
	{
		self::$_lastHttpCode = $lastHttpCode;
	}

	/**
	 * @return int
	 */
	public static function getLastHttpCode()
	{
		return self::$_lastHttpCode;
	}

	/**
	 * @param boolean $debug
	 */
	public static function setDebug( $debug )
	{
		self::$_debug = $debug;
	}

	/**
	 * @return boolean
	 */
	public static function getDebug()
	{
		return self::$_debug;
	}

	/**
	 * @param boolean $autoDecodeJson
	 */
	public static function setAutoDecodeJson( $autoDecodeJson )
	{
		self::$_autoDecodeJson = $autoDecodeJson;
	}

	/**
	 * @return boolean
	 */
	public static function getAutoDecodeJson()
	{
		return self::$_autoDecodeJson;
	}

	/**
	 * @param boolean $decodeToArray
	 */
	public static function setDecodeToArray( $decodeToArray )
	{
		self::$_decodeToArray = $decodeToArray;
	}

	/**
	 * @return boolean
	 */
	public static function getDecodeToArray()
	{
		return self::$_decodeToArray;
	}

	/**
	 * @return array
	 */
	public static function getLastResponseHeaders()
	{
		return self::$_lastResponseHeaders;
	}

}
