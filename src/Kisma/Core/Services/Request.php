<?php
/**
 * Request.php
 */
namespace Kisma\Core\Services;

use Kisma\Core\Utility\Option;

/**
 * Request
 * Encapsulates an application request
 */
class Request extends \Kisma\Core\Seed implements \Kisma\Core\Interfaces\RequestSource
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var int
	 */
	protected $_source = self::Http;
	/**
	 * @var array Depending on the source, contains the command line arguments
	 */
	protected $_arguments = null;
	/**
	 * @var int Depending on the source, contains the count of command line arguments
	 */
	protected $_argumentCount = null;
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
	/**
	 * @var array
	 */
	protected $_raw = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function __construct( $settings = array() )
	{
		$this->_loadRequest();
		parent::__construct( $settings );
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * Load the inbound $_REQUEST object
	 */
	protected function _loadRequest()
	{
		//	Command line request...
		if ( isset( $_SERVER['argc'] ) )
		{
			$this->_source = self::Cli;
			$this->_arguments = Option::get( $_SERVER, 'argv' );
			$this->_argumentCount = Option::get( $_SERVER, 'argc' );
		}

		//	Pull out some info...
		$this->_raw = array(
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
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}

	/**
	 * @return \Kisma\Core\Enums\HttpMethod
	 */
	public function getMethod()
	{
		return $this->_method;
	}

	/**
	 * @return int
	 */
	public function getSource()
	{
		return $this->_source;
	}

	/**
	 * @return string
	 */
	public function getUri()
	{
		return $this->_uri;
	}

	/**
	 * @return array
	 */
	public function getRaw()
	{
		return $this->_raw;
	}

	/**
	 * @return int
	 */
	public function getArgumentCount()
	{
		return $this->_argumentCount;
	}

	/**
	 * @return array
	 */
	public function getArguments()
	{
		return $this->_arguments;
	}
}