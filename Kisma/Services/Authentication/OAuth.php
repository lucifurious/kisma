<?php
/**
 * OAuth.php
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
 * @category      Kisma_Services_Authentication
 * @package       kisma.services.authentication
 * @since         v1.0.0
 * @filesource
 */
namespace Kisma\Services\Authentication
{
	//*************************************************************************
	//* OAuthService
	//*************************************************************************

	/**
	 * OAuthService
	 * A base class for OAuth services. Has properties for all the basics.
	 */
	abstract class OAuth extends \Kisma\Components\Service
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var string The developer key
		 */
		protected $_developerKey = '';
		/**
		 * @var string The Oauth consumer key
		 */
		protected $_consumerKey = null;
		/**
		 * @var string The Oauth consumer secret
		 */
		protected $_consumerSecret = null;
		/**
		 * @var string The uri to which you will be redirected for auth&auth
		 */
		protected $_authenticationUri = null;
		/**
		 * @var string The uri to which you will be redirected after an auth
		 */
		protected $_redirectUri = null;
		/**
		 * @var string Our Oauth access token
		 */
		protected $_accessToken = null;
		/**
		 * @var string
		 */
		protected $_oauthEndpoint = null;
		/**
		 * @var int
		 */
		protected $_accessTokenExpiresIn = 0;
		/**
		 * @var string
		 */
		protected $_accessTokenType = null;
		/**
		 * @var string
		 */
		protected $_refreshToken = null;
		/**
		 * @var string
		 */
		protected $_requestToken = null;

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param string $accessToken
		 * @return \Kisma\Services\Authentication\OAuth
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
		 * @param int $accessTokenExpiresIn
		 * @return \Kisma\Services\Authentication\OAuth
		 */
		public function setAccessTokenExpiresIn( $accessTokenExpiresIn )
		{
			$this->_accessTokenExpiresIn = $accessTokenExpiresIn;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getAccessTokenExpiresIn()
		{
			return $this->_accessTokenExpiresIn;
		}

		/**
		 * @param string $accessTokenType
		 * @return \Kisma\Services\Authentication\OAuth
		 */
		public function setAccessTokenType( $accessTokenType )
		{
			$this->_accessTokenType = $accessTokenType;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getAccessTokenType()
		{
			return $this->_accessTokenType;
		}

		/**
		 * @param string $authenticationUri
		 * @return \Kisma\Services\Authentication\OAuth
		 */
		public function setAuthenticationUri( $authenticationUri )
		{
			$this->_authenticationUri = $authenticationUri;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getAuthenticationUri()
		{
			return $this->_authenticationUri;
		}

		/**
		 * @param string $consumerKey
		 * @return \Kisma\Services\Authentication\OAuth
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
		 * @return \Kisma\Services\Authentication\OAuth
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
		 * @param string $developerKey
		 * @return \Kisma\Services\Authentication\OAuth
		 */
		public function setDeveloperKey( $developerKey )
		{
			$this->_developerKey = $developerKey;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDeveloperKey()
		{
			return $this->_developerKey;
		}

		/**
		 * @param string $oauthEndpoint
		 * @return \Kisma\Services\Authentication\OAuth
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
		 * @param string $redirectUri
		 * @return \Kisma\Services\Authentication\OAuth
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
		 * @return \Kisma\Services\Authentication\OAuth
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
	}
	
}