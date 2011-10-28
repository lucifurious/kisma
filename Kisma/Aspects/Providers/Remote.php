<?php
/**
 * Remote.php
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
 * @category      Kisma_Providers
 * @package       kisma.providers
 * @since         v1.0.0
 * @filesource
 */
namespace Kisma\Providers
{
	/**
	 * Remote
	 *
	 * A base provider class for remote providers
	 * services (i.e. XML, HTTP, CouchDb, etc.)
	 *
	 * Contains properties useful for talking to these services
	 *
	 * @property string $hostName
	 * @property int $hostPort
	 * @property string $userName
	 * @property string $password
	 */
	abstract class Remote extends \Kisma\Components\Component implements IProvider
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var string
		 */
		protected $_hostName = null;
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
		/**
		 * @var string The remote endpoint
		 */
		protected $_endpoint = null;

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param string $hostName
		 * @return \Kisma\Services\RemoteService
		 */
		public function setHostName( $hostName = null )
		{
			$this->_hostName = $hostName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getHostName()
		{
			return $this->_hostName;
		}

		/**
		 * @param int $hostPort
		 * @return \Kisma\Services\RemoteService
		 */
		public function setHostPort( $hostPort = null )
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
		 * @return \Kisma\Services\RemoteService
		 */
		public function setPassword( $password = null )
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
		 * @return \Kisma\Services\RemoteService
		 */
		public function setUserName( $userName = null )
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
		 * @param string $endpoint
		 * @return \Kisma\Services\Remote
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
	}

}