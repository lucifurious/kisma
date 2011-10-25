<?php
/**
 * HttpService.php
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright     Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link          http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license       http://github.com/Pogostick/kisma/licensing/
 * @author        Jerry Ablan <kisma@pogostick.com>
 * @category      Kisma_Services
 * @package       kisma.services
 * @since         v1.0.0
 * @filesource
 */
namespace Kisma\Services\Remote
{
	//*************************************************************************
	//* Aliases
	//*************************************************************************

	use Kisma\Services as Services;
	use Kisma\IHttpMethod as Method;

	/**
	 * Http
	 * A base service to provide HTTP services
	 */
	class Http extends Services\Remote implements \Kisma\IHttpMethod, \Kisma\IHttpResponse
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * The curl_info() from the last call
		 * @var array|null
		 */
		protected $_callInfo = null;
		/**
		 * @var resource The current cURL instance
		 */
		protected $_curl = null;
		/**
		 * @var mixed The last result from curl_exec()
		 */
		protected $_lastResult = null;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * @param array $options
		 */
		public function __construct( $options = array() )
		{
			if ( !function_exists( 'curl_init' ) )
			{
				throw new \Kisma\ServiceException( 'The Http service requires the cURL extension.' );
			}

			parent::__construct( $options );
		}

		/**
		 * Makes a service call
		 *
		 * @param string $method
		 * @param $url The URL of the resource
		 * @param array $payload
		 * @param array $curlOptions
		 * @return mixed|false
		 */
		public function call( $method = Method::Get, $url, $payload = array(), $curlOptions = array() )
		{
			//	Not an array of payload?
			if ( !empty( $payload ) && !is_array( $payload ) )
			{
				throw new \Kisma\ServiceException( '"$payload" must be an empty or an array of key/value pairs.' );
			}

			//	Our return results
			$_result = null;
			$_method = strtoupper( $method );
			$_payload = $payload ?: array();

			if ( empty( $curlOptions ) )
			{
				$curlOptions = array();
			}

			$this->_curl = curl_init();

			\K::sins( $curlOptions, CURLOPT_RETURNTRANSFER, true );
			\K::sins( $curlOptions, CURLOPT_FAILONERROR, true );
			\K::sins( $curlOptions, CURLOPT_TIMEOUT, 30 );
			\K::sins( $curlOptions, CURLOPT_SSL_VERIFYPEER, false );
			\K::sins( $curlOptions, CURLOPT_FOLLOWLOCATION, true );

			//	Now set all the options at once!
			curl_setopt_array( $this->_curl, $curlOptions );

			//	If this is a post, we have to put the post data in another field...
			if ( Method::Post == $_method )
			{
				curl_setopt( $this->_curl, CURLOPT_URL, $url );
				curl_setopt( $this->_curl, CURLOPT_POST, true );
				curl_setopt( $this->_curl, CURLOPT_POSTFIELDS, $_payload );
			}
			else
			{
				curl_setopt( $this->_curl, CURLOPT_URL, $url . ( Method::Get == $_method ? ( !empty( $_payload ) ? '?' . trim( $_payload, '&' ) : '' ) : '' ) );
			}

			$this->_callInfo = $this->_lastResult = null;

			$this->trigger( 'before_service_call', $this );
			$this->_lastResult = curl_exec( $this->_curl );

			//	Fill up the info array
			$this->_callInfo = curl_getinfo( $this->_curl );
			$this->_callInfo['curl.error'] = curl_error( $this->_curl );
			$this->_callInfo['curl.errno'] = curl_errno( $this->_curl );

			$this->trigger( 'after_service_call', $this );

			curl_close( $this->_curl );
			$this->_curl = null;

			return $this->_lastResult;
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param array $callInfo
		 * @return \Kisma\Services\Remote\Http
		 */
		public function setCallInfo( $callInfo )
		{
			$this->_callInfo = $callInfo;
			return $this;
		}

		/**
		 * @return array|null
		 */
		public function getCallInfo()
		{
			return $this->_callInfo;
		}

		/**
		 * @param \Kisma\Services\Remote\resource $curl
		 * @return \Kisma\Services\Remote\Http
		 */
		public function setCurl( $curl )
		{
			$this->_curl = $curl;
			return $this;
		}

		/**
		 * @return \Kisma\Services\Remote\resource
		 */
		public function getCurl()
		{
			return $this->_curl;
		}

		/**
		 * @param mixed $lastResult
		 * @return \Kisma\Services\Remote\Http
		 */
		public function setLastResult( $lastResult )
		{
			$this->_lastResult = $lastResult;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getLastResult()
		{
			return $this->_lastResult;
		}

	}

}