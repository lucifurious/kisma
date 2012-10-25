<?php
/**
 * BaseClient.php
 * A base class for OAuth clients
 *
 * Copyright (c) 2012 Silverpop Systems, Inc.
 * http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @filesource
 */
namespace CIS\Services\OAuth;

use CIS\CisPath;
use CIS\Components as Components;
use CIS\Utility\Curl;
use CIS\Utility\Option;

/**
 *
 */
abstract class BaseClient extends Components\BaseObject
{
	//**************************************************************************
	//* Constants
	//**************************************************************************

	/**
	 * @var string The access token we're using
	 */
	const AccessToken = '_access_token';
	/**
	 * @var string
	 */
	const RequestToken = '_request_token';
	/**
	 * @var string
	 */
	const RefreshToken = '_refresh_token';

	//**************************************************************************
	//* Private Members
	//**************************************************************************

	/**
	 * @var string
	 */
	protected $_endpoint = null;
	/**
	 * @var string
	 */
	protected $_consumerKey = null;
	/**
	 * @var string
	 */
	protected $_consumerSecret = null;
	/**
	 * @var string
	 */
	protected $_redirectUri = null;
	/**
	 * @var string The OAuth access token for the service
	 */
	protected $_accessToken = null;
	/**
	 * @var string The OAuth refresh token for the service
	 */
	protected $_refreshToken = null;
	/**
	 * @var string
	 */
	protected $_authorizeUrl = null;
	/**
	 * @var string
	 */
	protected $_requestTokenUrl = '/oauth2/authorize';
	/**
	 * @var string
	 */
	protected $_accessTokenUrl = '/oauth2/token';
	/**
	 * @var string
	 */
	protected $_lastError = null;
	/**
	 * @var string
	 */
	protected $_lastErrorCode = null;
	/**
	 * @var int
	 */
	protected $_lastHttpCode = null;
	/**
	 * @var array The default options for the curl object
	 */
	protected $_defaultCurlOptions
		= array(
			CURLOPT_HTTPHEADER => array(
				'Content-type: application/json',
				'X-PrettyPrint: 1'
			),
		);
	/**
	 * @var bool If true, arrays of data are returned instead of objects (\stdClass)
	 */
	protected $_returnArrays = false;
	/**
	 * @var string
	 */
	protected $_scope = null;
	/**
	 * @var string
	 */
	protected $_appName = null;
	/**
	 * @var bool|mixed|null The results of the OAuth progress check
	 */
	protected $_tokenPayload = null;
	/**
	 * @var null
	 */
	protected $_lastResponseId = null;
	/**
	 * @var null
	 */
	protected $_lastResponseType = null;
	/**
	 * @var null
	 */
	protected $_lastRequest = null;

	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * @param array|\stdClass $options
	 *
	 * @throws \CIS\Exceptions\ConfigurationException
	 * @return \CIS\Services\OAuth\BaseClient
	 */
	public function __construct( $options = array() )
	{
		parent::__construct( $options );

		if ( null === $this->_appName )
		{
			throw new \CIS\Exceptions\ConfigurationException( 'You must specify a unique value for the "appName" property in order to use this object.' );
		}

		if ( !$this->_loadToken() )
		{
			$this->_checkAuthenticationProgress();
		}
	}

	/**
	 * Given a path, build a full url
	 *
	 * @param string|null $path
	 *
	 * @return string
	 */
	public function buildEndpoint( $path = null )
	{
		return rtrim( $this->_endpoint, '/' ) . '/' . ltrim( $path, '/' );
	}

	//**************************************************************************
	//* Private Members
	//**************************************************************************

	/**
	 * Checks the progress of any in-flight OAuth requests
	 *
	 * @param array $parameters
	 *
	 * @return mixed
	 */
	protected function _checkAuthenticationProgress( $parameters = array() )
	{
		if ( isset( $_GET['code'] ) )
		{
			$_response = Curl::post(
				$this->buildEndpoint( $this->_accessTokenUrl ),
				array_merge(
					array(
						'client_id'     => $this->_consumerKey,
						'client_secret' => $this->_consumerSecret,
						'redirect_uri'  => $this->_redirectUri,
						'code'          => $_REQUEST['code'],
					),
					$parameters
				)
			);

			return $_response;
		}

		$this->_getAuthorizationUrl();

		return false;
	}

	/**
	 * Loads a token from session/storage
	 *
	 * @return bool|mixed
	 */
	abstract protected function _loadToken();

	/**
	 * Saves a token to session/storage
	 *
	 * @return bool
	 */
	abstract protected function _saveToken();

	/**
	 * Construct a link to authorize the application
	 *
	 * @param array $parameters
	 *
	 * @return string
	 */
	protected function _getAuthorizationUrl( $parameters = array() )
	{
		return $this->_authorizeUrl = $this->buildEndpoint( $this->_requestTokenUrl ) . '?' . http_build_query(
			array_merge(
				array(
					'client_id'     => $this->_consumerKey,
					'client_secret' => $this->_consumerSecret,
					'redirect_uri'  => $this->_redirectUri,
					'scope'         => $this->_scope,
				),
				$parameters
			)
		);
	}

	//**************************************************************************
	//* Properties
	//**************************************************************************

	/**
	 * Clear out the last errors and stuff from last request
	 *
	 * @return void
	 */
	protected function _resetRequest()
	{
		$this->_lastErrorCode = $this->_lastHttpCode = $this->_lastError = null;
	}

	/**
	 * @param string $accessToken
	 *
	 * @return BaseClient
	 */
	public function setAccessToken( $accessToken )
	{
		$this->_accessToken = $accessToken;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAccessToken()
	{
		return $this->_accessToken;
	}

	/**
	 * @param string $accessTokenUrl
	 *
	 * @return BaseClient
	 */
	public function setAccessTokenUrl( $accessTokenUrl )
	{
		$this->_accessTokenUrl = $accessTokenUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAccessTokenUrl()
	{
		return $this->_accessTokenUrl;
	}

	/**
	 * @param string $appName
	 *
	 * @return BaseClient
	 */
	public function setAppName( $appName )
	{
		$this->_appName = $appName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAppName()
	{
		return $this->_appName;
	}

	/**
	 * @param string $authorizeUrl
	 *
	 * @return BaseClient
	 */
	public function setAuthorizeUrl( $authorizeUrl )
	{
		$this->_authorizeUrl = $authorizeUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAuthorizeUrl()
	{
		return $this->_authorizeUrl;
	}

	/**
	 * @param string $consumerKey
	 *
	 * @return BaseClient
	 */
	public function setConsumerKey( $consumerKey )
	{
		$this->_consumerKey = $consumerKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getConsumerKey()
	{
		return $this->_consumerKey;
	}

	/**
	 * @param string $consumerSecret
	 *
	 * @return BaseClient
	 */
	public function setConsumerSecret( $consumerSecret )
	{
		$this->_consumerSecret = $consumerSecret;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getConsumerSecret()
	{
		return $this->_consumerSecret;
	}

	/**
	 * @param array $defaultCurlOptions
	 *
	 * @return BaseClient
	 */
	public function setDefaultCurlOptions( $defaultCurlOptions )
	{
		$this->_defaultCurlOptions = $defaultCurlOptions;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getDefaultCurlOptions()
	{
		return $this->_defaultCurlOptions;
	}

	/**
	 * @param string $endpoint
	 *
	 * @return BaseClient
	 */
	public function setEndpoint( $endpoint )
	{
		$this->_endpoint = $endpoint;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEndpoint()
	{
		return $this->_endpoint;
	}

	/**
	 * @param string $lastError
	 *
	 * @return BaseClient
	 */
	public function setLastError( $lastError )
	{
		$this->_lastError = $lastError;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastError()
	{
		return $this->_lastError;
	}

	/**
	 * @param string $lastErrorCode
	 *
	 * @return BaseClient
	 */
	public function setLastErrorCode( $lastErrorCode )
	{
		$this->_lastErrorCode = $lastErrorCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastErrorCode()
	{
		return $this->_lastErrorCode;
	}

	/**
	 * @param int $lastHttpCode
	 *
	 * @return BaseClient
	 */
	public function setLastHttpCode( $lastHttpCode )
	{
		$this->_lastHttpCode = $lastHttpCode;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getLastHttpCode()
	{
		return $this->_lastHttpCode;
	}

	/**
	 * @param string $redirectUri
	 *
	 * @return BaseClient
	 */
	public function setRedirectUri( $redirectUri )
	{
		$this->_redirectUri = $redirectUri;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRedirectUri()
	{
		return $this->_redirectUri;
	}

	/**
	 * @param string $refreshToken
	 *
	 * @return BaseClient
	 */
	public function setRefreshToken( $refreshToken )
	{
		$this->_refreshToken = $refreshToken;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRefreshToken()
	{
		return $this->_refreshToken;
	}

	/**
	 * @param string $requestTokenUrl
	 *
	 * @return BaseClient
	 */
	public function setRequestTokenUrl( $requestTokenUrl )
	{
		$this->_requestTokenUrl = $requestTokenUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRequestTokenUrl()
	{
		return $this->_requestTokenUrl;
	}

	/**
	 * @param boolean $returnArrays
	 *
	 * @return BaseClient
	 */
	public function setReturnArrays( $returnArrays )
	{
		$this->_returnArrays = $returnArrays;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getReturnArrays()
	{
		return $this->_returnArrays;
	}

	/**
	 * @param string $scope
	 *
	 * @return BaseClient
	 */
	public function setScope( $scope )
	{
		$this->_scope = $scope;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getScope()
	{
		return $this->_scope;
	}

	/**
	 * @param bool|mixed|null $tokenPayload
	 *
	 * @return BaseClient
	 */
	public function setTokenPayload( $tokenPayload )
	{
		$this->_tokenPayload = $tokenPayload;
		return $this;
	}

	/**
	 * @return bool|mixed|null
	 */
	public function getTokenPayload()
	{
		return $this->_tokenPayload;
	}

	/**
	 * @param null $lastRequest
	 *
	 * @return BaseClient
	 */
	public function setLastRequest( $lastRequest )
	{
		$this->_lastRequest = $lastRequest;
		return $this;
	}

	/**
	 * @return null
	 */
	public function getLastRequest()
	{
		return $this->_lastRequest;
	}

	/**
	 * @param null $lastResponseId
	 *
	 * @return BaseClient
	 */
	public function setLastResponseId( $lastResponseId )
	{
		$this->_lastResponseId = $lastResponseId;
		return $this;
	}

	/**
	 * @return null
	 */
	public function getLastResponseId()
	{
		return $this->_lastResponseId;
	}

	/**
	 * @param null $lastResponseType
	 *
	 * @return BaseClient
	 */
	public function setLastResponseType( $lastResponseType )
	{
		$this->_lastResponseType = $lastResponseType;
		return $this;
	}

	/**
	 * @return null
	 */
	public function getLastResponseType()
	{
		return $this->_lastResponseType;
	}

}
