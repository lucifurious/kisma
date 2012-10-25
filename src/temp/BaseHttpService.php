<?php
/**
 * BaseHttpService.php
 *
 * Copyright (c) 2012 Silverpop Systems, Inc.
 * http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 * @filesource
 */
namespace CIS\Services;

use CIS\Utility\Curl;
use CIS\Utility\Inflector;
use CIS\Utility\Environment;

/**
 * BaseHttpService
 * The base class for http services.
 *
 * Pass-through Curl properties:
 *
 * @property array $callInfo
 * @property array $lastError
 * @property int   $lastHttpCode
 */
abstract class BaseHttpService extends \CIS\Services\BaseService implements \CIS\Interfaces\HttpMethod
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
	//* Private Members
	//********************************************************************************

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
	//* Public Methods
	//*************************************************************************

	/**
	 * Makes a service request
	 *
	 * @param string       $url
	 * @param array|mixed  $payload
	 * @param array        $curlOptions
	 * @param string       $method
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

		if ( $this->trigger( self::BeforeServiceCall ) )
		{
			$_response = Curl::request( $method, $url, $payload, $curlOptions );

			$this->trigger( self::AfterServiceCall, $_response );
		}

		return $_response;
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
	 * @return \CIS\Services\BaseHttpService
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
	 * @return \CIS\Services\BaseHttpService
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
	 * @return \CIS\Services\BaseHttpService
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
	 * @return BaseHttpService
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

	/**
	 * @param \CIS\Services\Profiler\Service $profiler
	 *
	 * @return \CIS\Services\Profiler\Service
	 */
	public function setProfiler( $profiler )
	{
		$this->_profiler = $profiler;
		return $this;
	}

	/**
	 * @return \CIS\Services\Profiler\Service
	 */
	public function getProfiler()
	{
		return $this->_profiler;
	}

}
