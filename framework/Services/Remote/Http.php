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
 * @namespace     \Kisma\Services
 * @since         v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Services\Remote
 */
namespace Kisma\Services\Remote
{
	//*************************************************************************
	//* Aliases
	//*************************************************************************

	/**
	 * Kisma Aliases
	 */
	use Kisma\Aspects as Aspects;
	use Kisma\Components as Components;
	use Kisma\Services as Services;

	/**
	 * Http
	 * A base service to provide communication to an HTTP-based service
	 */
	class Http extends Services\Remote
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * The curl_info() from the last call
		 * @var array|null
		 */
		protected $_callInfo = null;

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
				throw new ServiceException( 'The Http service requires the installation of cURL.' );
			}

			parent::__construct( $options );
		}

		/**
		 * Makes a service call
		 *
		 * @param string $method
		 * @param $url The URL of the resource
		 * @param array|\Kisma\Services\Remote\An $payload An array of key/value pairs to send with the request. Values are assumed to be URL encoded.
		 * @param array $curlOptions
		 * @return mixed|null|false
		 */
		public function call( $method = \HttpMethods::Get, $url, $payload = array(), $curlOptions = array() )
		{
			//	Not an array of payload?
			if ( !empty( $payload ) && !is_array( $payload ) )
			{
				throw new ServiceException( '"$payload" must be an array of key => value pairs.' );
			}

			//	Our return results
			$_result = null;
			$_method = strtoupper( $method );
			$_payload = $payload ?: array();

			if ( empty( $curlOptions ) )
			{
				$curlOptions = array();
			}

			$_curl = curl_init();

			K::sins( $curlOptions, CURLOPT_RETURNTRANSFER, true );
			K::sins( $curlOptions, CURLOPT_FAILONERROR, true );
			K::sins( $curlOptions, CURLOPT_TIMEOUT, 30 );
			K::sins( $curlOptions, CURLOPT_SSL_VERIFYPEER, false );
			K::sins( $curlOptions, CURLOPT_FOLLOWLOCATION, true );

			//	Now set all the options at once!
			curl_setopt_array( $_curl, $curlOptions );

			//	If this is a post, we have to put the post data in another field...
			if ( \HttpMethods::Post == $_method )
			{
				curl_setopt( $_curl, CURLOPT_URL, $url );
				curl_setopt( $_curl, CURLOPT_POST, true );
				curl_setopt( $_curl, CURLOPT_POSTFIELDS, $_payload );
			}
			else
			{
				curl_setopt( $_curl, CURLOPT_URL, $url . ( \HttpMethods::Get == $_method ? ( !empty( $_payload ) ? '?' . trim( $_payload, '&' ) : '' ) : '' ) );
			}

			$this->_callInfo = null;
			$_result = curl_exec( $_curl );
			$this->_callInfo = curl_getinfo( $_curl );
			$this->_callInfo['curl.error'] = curl_error( $_curl );
			$this->_callInfo['curl.errno'] = curl_errno( $_curl );

			return $_result;
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

	}

}