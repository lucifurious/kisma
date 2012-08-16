<?php
/**
 * HttpResponse.php
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/lucifurious/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 * @link		  http://github.com/lucifurious/kisma/ Kisma(tm)
 * @license	   http://github.com/lucifurious/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @package	   kisma.utility
 * @since		 v1.0.0
 * @filesource
 */
namespace Kisma\Core\Utility;

//*************************************************************************
//* Aliases
//*************************************************************************

use Kisma\K;

/**
 * HttpResponse
 * A response to an HTTP request
 */
class HttpResponse extends \Kisma\Core\Seed
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var int
	 */
	protected $_code;
	/**
	 * @var string
	 */
	protected $_body;
	/**
	 * @var array
	 */
	protected $_headers;
	/**
	 * @var array
	 */
	protected $_info;
	/**
	 * @var string
	 */
	protected $_url;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Constructor
	 *
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
		$this->_code = Option::o( $options, 'code' );
		$this->_body = Option::o( $options, 'body' );
		$this->_headers = Option::o( $options, 'headers', array() );
		$this->_info = Option::o( $options, 'info' );
		$this->_url = Option::o( $this->_info, 'url' );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param string $body
	 *
	 * @return \Kisma\Core\Utility\HttpResponse
	 */
	public function setBody( $body )
	{
		$this->_body = $body;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getBody()
	{
		return $this->_body;
	}

	/**
	 * @param int $code
	 *
	 * @return \Kisma\Core\Utility\HttpResponse
	 */
	public function setCode( $code )
	{
		$this->_code = $code;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCode()
	{
		return $this->_code;
	}

	/**
	 * @param array $headers
	 *
	 * @return \Kisma\Core\Utility\HttpResponse
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
	 * @param array $info
	 *
	 * @return \Kisma\Core\Utility\HttpResponse
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
	 * @param string $url
	 *
	 * @return \Kisma\Core\Utility\HttpResponse
	 */
	public function setUrl( $url )
	{
		$this->_url = $url;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->_url;
	}
}
