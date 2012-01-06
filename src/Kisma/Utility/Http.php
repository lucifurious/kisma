<?php
/**
 * Http.php
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link		  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license	   http://github.com/Pogostick/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @package	   kisma.utility
 * @since		 v1.0.0
 * @filesource
 */
namespace Kisma\Utility;

//*************************************************************************
//* Requirements
//*************************************************************************

/**
 * Http
 * A generic HTTP class
 */
class Http extends \Kisma\Components\Seed implements \Kisma\IUtility
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string
	 */
	protected $_userName = null;
	/**
	 * @var string
	 */
	protected $_password = null;
	/**
	 * @var int
	 */
	protected $_hostPort = null;
	/**
	 * @var array The error of the last call
	 */
	protected $_error = null;
	/**
	 * @var array The results of the last call
	 */
	protected $_info = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param string     $url
	 * @param array|null $options
	 *
	 * @return bool|mixed
	 */
	public function get( $url, $options = array() )
	{
		return $this->_httpRequest( \Kisma\HttpMethod::Get, $url, array(), $options );
	}

	/**
	 * @param string                           $url
	 * @param array|\stdClass|\stdClass[]|null $payload
	 * @param array|null                       $options
	 *
	 * @return bool|mixed
	 */
	public function put( $url, $payload = array(), $options = array() )
	{
		return $this->_httpRequest( \Kisma\HttpMethod::Put, $url, $payload, $options );
	}

	/**
	 * @param string     $url
	 * @param array|null $payload
	 * @param array|null $options
	 *
	 * @return bool|mixed
	 */
	public function post( $url, $payload = array(), $options = array() )
	{
		return $this->_httpRequest( \Kisma\HttpMethod::Post, $url, $payload, $options );
	}

	/**
	 * @param string     $url
	 * @param array|null $payload
	 * @param array|null $options
	 *
	 * @return bool|mixed
	 */
	public function delete( $url, $payload = array(), $options = array() )
	{
		return $this->_httpRequest( \Kisma\HttpMethod::Delete, $url, $payload, $options );
	}

	/**
	 * @param string     $url
	 * @param array|null $payload
	 * @param array|null $options
	 *
	 * @return bool|mixed
	 */
	public function head( $url, $payload = array(), $options = array() )
	{
		return $this->_httpRequest( \Kisma\HttpMethod::Head, $url, $payload, $options );
	}

	/**
	 * @param string     $url
	 * @param array|null $payload
	 * @param array|null $options
	 *
	 * @return bool|mixed
	 */
	public function options( $url, $payload = array(), $options = array() )
	{
		return $this->_httpRequest( \Kisma\HttpMethod::Options, $url, $payload, $options );
	}

	/**
	 * @param string     $url
	 * @param array|null $payload
	 * @param array|null $options
	 *
	 * @return bool|mixed
	 */
	public function copy( $url, $payload = array(), $options = array() )
	{
		return $this->_httpRequest( \Kisma\HttpMethod::Copy, $url, $payload, $options );
	}

	/**
	 * @param \Kisma\HttpMethod|string $method
	 * @param string                   $url
	 * @param array                    $payload
	 * @param array                    $options
	 *
	 * @return bool|mixed
	 */
	protected function _httpRequest( $method = \Kisma\HttpMethod::Get, $url, $payload = array(), $options = array() )
	{
		//	Reset!
		$this->_error = $this->_info = $_tmpFile = null;

		//	Build a curl request...
		$_curl = curl_init( $url );

		//	Defaults
		$_curlOptions = array(
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_SSL_VERIFYPEER => false,
		);

		//	Special treatment of headers
		if ( isset( $options, $options[CURLOPT_HTTPHEADER] ) )
		{
			$_headers = Option::o( $_curlOptions, CURLOPT_HTTPHEADER, array(), true );

			foreach ( $options[CURLOPT_HTTPHEADER] as $_header )
			{
				$_headers[] = $_header;
			}

			$_curlOptions[CURLOPT_HTTPHEADER] = $_headers;
		}

		//	Now, add/override user options
		if ( !empty( $options ) )
		{
			foreach ( $_curlOptions as $_key => $_value )
			{
				$_curlOptions[$_key] = $_value;
			}
		}

		if ( null !== $this->_userName || null !== $this->_password )
		{
			$_curlOptions[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
			$_curlOptions[CURLOPT_USERPWD] = $this->_userName . ':' . $this->_password;
		}

		switch ( $method )
		{
			case \Kisma\HttpMethod::Get:
				//	Do nothing, like the goggles...
				break;

			case \Kisma\HttpMethod::Put:
				$_payload = json_encode( !empty( $payload ) ? $payload : array() );

				$_tmpFile = tmpfile();
				fwrite( $_tmpFile, $_payload );
				rewind( $_tmpFile );

				$_curlOptions[CURLOPT_PUT] = true;
				$_curlOptions[CURLOPT_INFILE] = $_tmpFile;
				$_curlOptions[CURLOPT_INFILESIZE] = mb_strlen( $_payload );
				break;

			case \Kisma\HttpMethod::Post:
				$_curlOptions[CURLOPT_POST] = true;
				$_curlOptions[CURLOPT_POSTFIELDS] = $payload;
				break;

			case \Kisma\HttpMethod::Head:
				$_curlOptions[CURLOPT_NOBODY] = true;
				break;

			case \Kisma\HttpMethod::Delete:
			case \Kisma\HttpMethod::Options:
			case \Kisma\HttpMethod::Copy:
				$_curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
				break;
		}

		if ( null !== $this->_hostPort && !isset( $_curlOptions[CURLOPT_PORT] ) )
		{
			$_curlOptions[CURLOPT_PORT] = $this->_hostPort;
		}

		//	Set our collected options
		curl_setopt_array( $_curl, $_curlOptions );

		//	Make the call!
		if ( false === ( $_result = curl_exec( $_curl ) ) )
		{
			$this->_error = array(
				'code' => curl_errno( $_curl ),
				'message' => curl_error( $_curl ),
			);

			return false;
		}

		if ( true === $_result )
		{
			//	Worked, but no data...
			$_result = null;
		}

		$this->_info = curl_getinfo( $_curl );
		curl_close( $_curl );

		//	Close temp file if any
		if ( null !== $_tmpFile )
		{
			fclose( $_tmpFile );
		}

		return $_result;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param array $error
	 *
	 * @return \Kisma\Utility\Http
	 */
	public function setError( $error )
	{
		$this->_error = $error;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getError()
	{
		return $this->_error;
	}

	/**
	 * @param int $hostPort
	 *
	 * @return \Kisma\Utility\Http
	 */
	public function setHostPort( $hostPort )
	{
		$this->_hostPort = $hostPort;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getHostPort()
	{
		return $this->_hostPort;
	}

	/**
	 * @param array $info
	 *
	 * @return \Kisma\Utility\Http
	 */
	public function setInfo( $info )
	{
		$this->_info = $info;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getInfo()
	{
		return $this->_info;
	}

	/**
	 * @param string $password
	 *
	 * @return \Kisma\Utility\Http
	 */
	public function setPassword( $password )
	{
		$this->_password = $password;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->_password;
	}

	/**
	 * @param string $userName
	 *
	 * @return \Kisma\Utility\Http
	 */
	public function setUserName( $userName )
	{
		$this->_userName = $userName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUserName()
	{
		return $this->_userName;
	}

}
