<?php
/**
 * HttpService.php
 */
namespace Kisma\Core\Services;
use Kisma\Core\Utility\Curl;
use Kisma\Core\Utility\Inflector;

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
abstract class HttpService extends SeedService implements \Kisma\Core\Interfaces\HttpMethod
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
