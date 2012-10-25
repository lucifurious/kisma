<?php
/**
 * Client.php
 * An OAuth client helper class. Requires the PEAR OAuth extension
 *
 * Copyright (c) 2011 Silverpop Systems, Inc.
 * http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @author  Jerry Ablan <jablan@silverpop.com>
 * @package cis.oauth
 * @filesource
 */
namespace CIS\Services\OAuth;

use CIS\Utility\Session;

/**
 * Client
 * A generic OAuth client
 */
class Client extends \CIS\Services\BaseService implements \CIS\Interfaces\HttpMethod
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string
	 */
	protected $_oauthSignatureMethod = OAUTH_SIG_METHOD_HMACSHA1;
	/**
	 * @var string
	 */
	protected $_oauthAuthType = OAUTH_AUTH_TYPE_AUTHORIZATION;
	/**
	 * Our OAuth object
	 *
	 * @var \OAuth
	 */
	protected $_oauth = null;
	/**
	 * The current token
	 *
	 * @var array
	 */
	protected $_currentToken = null;
	/**
	 * @var string
	 */
	protected $_redirectUri;
	/**
	 * @var bool
	 */
	protected $_isAuthorized = false;
	/**
	 * @var string
	 */
	protected $_accessTokenUrl = '/oauth/access_token';
	/**
	 * @var string
	 */
	protected $_authorizeUrl = '/oauth/authorize';
	/**
	 * @var string
	 */
	protected $_requestTokenUrl = '/oauth/request_token';
	/**
	 * @var string The OAuth public key
	 */
	protected $_consumerKey = null;
	/**
	 * @var string The OAuth secret key
	 */
	protected $_consumerSecret = null;
	/**
	 * @var string The OAuth endpoint
	 */
	protected $_oauthEndpoint = null;
	/**
	 * @var callback An optional callback for when an authorization occurs
	 */
	protected $_authorizationCallback = null;
	/**
	 * @var string
	 */
	protected $_oauthToken = null;
	/**
	 * @var string
	 */
	protected $_oauthTokenSecret = null;
	/**
	 * @var bool
	 */
	protected $_returnArrays = false;

	//********************************************************************************
	//* Constructor
	//********************************************************************************

	/***
	 * Constructor
	 *
	 * @param array|\stdClass $options
	 *
	 * @throws \Exception
	 */
	public function __construct( $options = array() )
	{
		//	No oauth? No run...
		if ( !extension_loaded( 'oauth' ) )
		{
			throw new \Exception( 'The PECL "oauth" extension is not loaded. Please install and/or load the oath extension.' );
		}

		//	Call daddy...
		parent::__construct( $options );

		$this->_initializeClient( $options );
	}

	//********************************************************************************
	//* Public Methods
	//********************************************************************************

	/**                                         I
	 * Appends the current token to the authorizeUrl option
	 *
	 * @return string
	 */
	public function getAuthorizeUrl()
	{
		$_token = $this->_oauth->getRequestToken(
			$this->_oauthEndpoint . $this->_requestTokenUrl,
			$this->_redirectUri
		);

		return $this->_oauthEndpoint . $this->_authorizeUrl . '?oauth_token=' . $_token['oauth_token'];
	}

	/**
	 * Stores the current token in a member variable and in the user state oAuthToken
	 *
	 * @param array $token
	 *
	 * @throws \Exception
	 *
	 * @return void
	 */
	public function storeToken( $token = array() )
	{
		$_name = spl_object_hash( $this );

		try
		{
			Session::set( $_name . '_oAuthToken', $token );
			Session::set( $_name . '_isAuthorized', $this->_isAuthorized );
			$this->_currentToken = $token;
		}
		catch ( \Exception $_ex )
		{
			throw new \Exception( 'Exception while storing OAuth token: ' . $_ex->getMessage(), $_ex->getCode() );
		}
	}

	/**
	 * Loads a token from the user state oAuthToken
	 *
	 * @return bool
	 */
	public function loadToken()
	{
		$_name = spl_object_hash( $this );

		if ( null != ( $_token = Session::get( $_name . '_OAuthToken' ) ) )
		{
			$this->_currentToken = $_token;
			$this->_isAuthorized = Session::get( $_name . '_isAuthorized', false );
			return true;
		}

		if ( null !== $this->_oauthToken && null !== $this->_oauthTokenSecret )
		{
			$this->_oauth->setToken( $this->_oauthToken, $this->_oauthTokenSecret );
		}

		return false;
	}

	/**
	 * Given a path, build a full url
	 *
	 * @param null|string $path
	 *
	 * @return string
	 */
	public function buildEndpoint( $path = null )
	{
		return $this->_oauthEndpoint . '/' . ltrim( $path, '/' );
	}

	//********************************************************************************
	//* Private Methods
	//********************************************************************************

	/**
	 * Initialize client
	 *
	 * @param array $options
	 *
	 * @return void
	 */
	protected function _initializeClient( $options = array() )
	{
		//	Create our object...
		$this->_oauth = new \OAuth(
			$this->_consumerKey,
			$this->_consumerSecret,
			$this->_oauthSignatureMethod,
			$this->_oauthAuthType
		);

		//	Load any tokens we have...
		$this->loadToken();

		//	Have we been authenticated?
		if ( !$this->_isAuthorized )
		{
			if ( isset( $_REQUEST['oauth_token'] ) )
			{
				if ( $this->_oauth->setToken( $_REQUEST['oauth_token'], $_REQUEST['oauth_verifier'] ) )
				{
					$_token = $this->_oauth->getAccessToken(
						$this->_oauthEndpoint . $this->_accessTokenUrl,
						null,
						$_REQUEST['oauth_verifier']
					);

					$this->storeToken( $_token );
					$this->_isAuthorized = true;
				}

				//	Call callback
				if ( $this->_authorizationCallback && is_callable( $this->_authorizationCallback ) )
				{
					call_user_func( $this->_authorizationCallback, $this );
				}
			}
		}
	}

	/***
	 * Fetches a protected resource using the tokens stored
	 *
	 * @param string $action
	 * @param array  $payload
	 * @param string $method
	 * @param array  $headers
	 *
	 * @throws \Exception
	 * @return \stdClass
	 */
	protected function makeRequest( $action, $payload = array(), $method = self::Get, $headers = array() )
	{
		//	Default...
		$_payload = $payload;

		//	Build the url...
		$_url = $this->buildEndpoint( $action );

		//	Make the call...
		try
		{
			$_token = $this->_currentToken;

			if ( $this->_oauth->setToken( $_token['oauth_token'], $_token['oauth_token_secret'] ) )
			{
				if ( $this->_oauth->fetch( $_url, $_payload, $method, $headers ) )
				{
					//	Return results...
					return json_decode( $this->_oauth->getLastResponse() );
				}
			}

			//	Boo
			return false;
		}
		catch ( \Exception $_ex )
		{
			$_response = null;
			throw new \Exception( 'Exception while making OAuth fetch request: ' . $_ex->getMessage(), $_ex->getCode() );
		}
	}

	//**************************************************************************
	//* Properties
	//**************************************************************************

	/**
	 * @param string $accessTokenUrl
	 *
	 * @return \CIS\OAuth\Client
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
	 * @param callback $authorizationCallback
	 *
	 * @return \CIS\OAuth\Client
	 */
	public function setAuthorizationCallback( $authorizationCallback )
	{
		$this->_authorizationCallback = $authorizationCallback;
		return $this;
	}

	/**
	 * @return callback
	 */
	public function getAuthorizationCallback()
	{
		return $this->_authorizationCallback;
	}

	/**
	 * @param string $redirectUri
	 *
	 * @return \CIS\OAuth\Client
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
	 * @param array $currentToken
	 *
	 * @return \CIS\OAuth\Client
	 */
	public function setCurrentToken( $currentToken )
	{
		$this->_currentToken = $currentToken;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getCurrentToken()
	{
		return $this->_currentToken;
	}

	/**
	 * @param boolean $isAuthorized
	 *
	 * @return \CIS\OAuth\Client
	 */
	public function setIsAuthorized( $isAuthorized )
	{
		$this->_isAuthorized = $isAuthorized;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getIsAuthorized()
	{
		return $this->_isAuthorized;
	}

	/**
	 * @param \OAuth $oauth
	 *
	 * @return \CIS\OAuth\Client
	 */
	public function setOauth( $oauth )
	{
		$this->_oauth = $oauth;
		return $this;
	}

	/**
	 * @return \OAuth
	 */
	public function &getOauth()
	{
		return $this->_oauth;
	}

	/**
	 * @param string $oauthEndpoint
	 *
	 * @return \CIS\OAuth\Client
	 */
	public function setOauthEndpoint( $oauthEndpoint )
	{
		$this->_oauthEndpoint = $oauthEndpoint;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOauthEndpoint()
	{
		return $this->_oauthEndpoint;
	}

	/**
	 * @param string $requestTokenUrl
	 *
	 * @return \CIS\OAuth\Client
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
	 * @param string $consumerKey
	 *
	 * @return \CIS\OAuth\Client
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
	 * @return \CIS\OAuth\Client
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
	 * @param string $oauthAuthType
	 *
	 * @return Client
	 */
	public function setOauthAuthType( $oauthAuthType )
	{
		$this->_oauthAuthType = $oauthAuthType;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOauthAuthType()
	{
		return $this->_oauthAuthType;
	}

	/**
	 * @param string $oauthSignatureMethod
	 *
	 * @return Client
	 */
	public function setOauthSignatureMethod( $oauthSignatureMethod )
	{
		$this->_oauthSignatureMethod = $oauthSignatureMethod;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOauthSignatureMethod()
	{
		return $this->_oauthSignatureMethod;
	}

	/**
	 * @param string $oauthToken
	 *
	 * @return Client
	 */
	public function setOauthToken( $oauthToken )
	{
		$this->_currentToken['oauth_token'] = $this->_oauthToken = $oauthToken;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOauthToken()
	{
		return $this->_oauthToken;
	}

	/**
	 * @param string $oauthTokenSecret
	 *
	 * @return Client
	 */
	public function setOauthTokenSecret( $oauthTokenSecret )
	{
		$this->_currentToken['oauth_token_secret'] = $this->_oauthTokenSecret = $oauthTokenSecret;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOauthTokenSecret()
	{
		return $this->_oauthTokenSecret;
	}

	/**
	 * @param boolean $returnArrays
	 *
	 * @return Client
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

}
