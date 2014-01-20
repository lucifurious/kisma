<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
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
namespace Kisma\Core\Services;

use Kisma\Core\Interfaces\HttpMethod;
use Kisma\Core\Utility\Curl;

/**
 * HttpService
 * The base class for http services.
 *
 * Pass-through Curl properties:
 *
 * @property array $callInfo
 * @property array $lastError
 * @property int   $lastHttpCode
 */
abstract class HttpService extends SeedService implements HttpMethod
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int When an error occurs, boolean false is returned.
	 */
	const ReturnFalse = 0;
	/**
	 * @var int When an error occurs, an exception is thrown
	 */
	const ThrowException = 1;

	//********************************************************************************
	//* Members
	//********************************************************************************

	/**
	 * @var int
	 */
	protected $_errorHandling = self::ReturnFalse;
	/**
	 * @var int
	 */
	protected $_hostPort = null;
	/**
	 * @var string
	 */
	protected $_userName = null;
	/**
	 * @var string
	 */
	protected $_password = null;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Makes a service request
	 *
	 * @param string      $url
	 * @param array|mixed $payload
	 * @param array       $curlOptions
	 * @param string      $method
	 *
	 * @return string
	 */
	public function request( $url, $payload = array(), $curlOptions = array(), $method = Curl::Get )
	{
		$_response = null;

		if ( $this->_hostPort )
		{
			Curl::setHostPort( $this->_hostPort );
		}

		if ( $this->_userName )
		{
			Curl::setUserName( $this->_userName );

			if ( $this->_password )
			{
				Curl::setPassword( $this->_password );
			}
		}

		return Curl::request( $method, $url, $payload, $curlOptions );
	}

	/**
	 * @return array
	 */
	public function getCallInfo()
	{
		return Curl::getInfo();
	}

	/**
	 * @return array
	 */
	public function getLastError()
	{
		return Curl::getError();
	}

	/**
	 * @return int
	 */
	public function getLastHttpCode()
	{
		return Curl::getLastHttpCode();
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param int $hostPort
	 *
	 * @return \Kisma\Core\Services\HttpService
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
	 * @param string $password
	 *
	 * @return \Kisma\Core\Services\HttpService
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
	 * @return \Kisma\Core\Services\HttpService
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

	/**
	 * @param $errorHandling
	 *
	 * @return \Kisma\Core\Services\HttpService
	 */
	public function setErrorHandling( $errorHandling )
	{
		$this->_errorHandling = $errorHandling;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getErrorHandling()
	{
		return $this->_errorHandling;
	}
}
