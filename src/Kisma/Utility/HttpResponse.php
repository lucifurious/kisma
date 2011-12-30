<?php
/**
 * HttpResponse.php
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
namespace Kisma\Utility
{
	/**
	 * HttpResponse
	 * A response to an HTTP request
	 */
	class HttpResponse extends \Kisma\Components\SubComponent
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
		 * @param array $options
		 */
		public function __construct( $options = array() )
		{
			$this->_code = \K::o( $options, 'code' );
			$this->_body = \K::o( $options, 'body' );
			$this->_headers = \K::o( $options, 'headers', array() );
			$this->_info = \K::o( $options, 'info' );
			$this->_url = \K::o( $this->_info, 'url' );
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param string $body
		 * @return \Kisma\Utility\HttpResponse
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
		 * @return \Kisma\Utility\HttpResponse
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
		 * @return \Kisma\Utility\HttpResponse
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
		 * @return \Kisma\Utility\HttpResponse
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
		 * @return \Kisma\Utility\HttpResponse
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
}