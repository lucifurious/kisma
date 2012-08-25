<?php
/**
 * HttpRequest.php
 */
namespace Kisma\Core\Services\Network;

use Kisma\Core\Utility\Option;

/**
 * HttpRequest
 * Encapsulates an HTTP application request
 */
class HttpRequest extends \Kisma\Core\Services\SeedRequest
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string
	 */
	protected $_uri = null;
	/**
	 * @var \Kisma\Core\Enums\HttpMethod
	 */
	protected $_method = null;
	/**
	 * @var string
	 */
	protected $_content = null;
	/**
	 * @var array
	 */
	protected $_headers = null;

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * Load the inbound $_REQUEST object
	 */
	protected function _loadRequest()
	{
		//	Initialize the request stuff
		parent::_loadRequest();

		//	Parse the rest
		$this->_content = array(
			'server'  => $_SERVER,
			'request' => isset( $_REQUEST ) ? $_REQUEST : null,
		);

		$this->_uri = Option::get( $_SERVER, 'REQUEST_URI' );
		$this->_method = Option::get( $_SERVER, 'REQUEST_METHOD' );

		//	Pull out the headers
		$this->_headers = array();

		foreach ( $_SERVER as $_key => $_value )
		{
			if ( false === stripos( $_key, 'HTTP_', 0 ) )
			{
				continue;
			}

			$_clean = str_replace(
				'HTTP_',
				null,
				strtoupper( $_key )
			);

			$_key = \Kisma\Core\Utility\Inflector::tag( strtolower( $_clean ), true );

			$this->_headers[$_key] = $_value;
		}

		return true;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param string $content
	 *
	 * @return HttpRequest
	 */
	public function setContent( $content )
	{
		$this->_content = $content;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->_content;
	}

	/**
	 * @param array $headers
	 *
	 * @return HttpRequest
	 */
	public function setHeaders( $headers )
	{
		$this->_headers = $headers;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}

	/**
	 * @param \Kisma\Core\Enums\HttpMethod $method
	 *
	 * @return HttpRequest
	 */
	public function setMethod( $method )
	{
		$this->_method = $method;

		return $this;
	}

	/**
	 * @return \Kisma\Core\Enums\HttpMethod
	 */
	public function getMethod()
	{
		return $this->_method;
	}

	/**
	 * @param string $uri
	 *
	 * @return HttpRequest
	 */
	public function setUri( $uri )
	{
		$this->_uri = $uri;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUri()
	{
		return $this->_uri;
	}

}
