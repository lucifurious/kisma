<?php
/**
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
 * @category      Kisma_Aspects_Storage_CouchDb
 * @package       kisma.aspects.storage
 * @namespace     \Kisma\Aspects\Storage
 * @since         v1.0.0
 * @filesource
 */

/**
 * Global namespace declarations
 */
namespace
{
	//*************************************************************************
	//* Requirements
	//*************************************************************************

	/**
	 *  Require the Sag library, but keep it outside of Kisma
	 */
	/** @noinspection PhpIncludeInspection */
	require_once \K::glue(
		DIRECTORY_SEPARATOR,
		\K::getSetting( \KismaSettings::BasePath ),
		'..',
		'vendors',
		'sag',
		'Sag.php'
	);
}

/**
 * @namespace Kisma\Aspects\Storage
 */
namespace Kisma\Aspects\Storage
{
	//*************************************************************************
	//* Aliases
	//*************************************************************************

	/**
	 * Kisma Aliases
	 */
	use Kisma\Components as Components;
	use Kisma\Aspects as Aspects;

	//*************************************************************************
	//* Requirements
	//*************************************************************************

	/**
	 * CouchDb
	 * An aspect that wraps the Sag library for working with a CouchDb instance
	 *
	 * @property string $hostName
	 * @property int $hostPort
	 * @property string $userName
	 * @property string $password
	 * @property string $database
	 * @property \Sag $sag
	 * 
	 */
	class CouchDb extends Components\Aspect implements \Kisma\IStorage
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var string
		 */
		protected $_hostName = '127.0.0.1';
		/**
		 * @var int
		 */
		protected $_hostPort = 5984;
		/**
		 * @var string
		 */
		protected $_userName = null;
		/**
		 * @var string
		 */
		protected $_password = null;
		/**
		 * @var string
		 */
		protected $_database = null;
		/**
		 * @var \Sag
		 */
		protected $_sag = null;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * @param array $options
		 */
		public function __construct( $options = array() )
		{
			//	Now call the constructor
			parent::__construct( $options );

			//	Instantiate Sag
			$this->_sag = new \Sag( $this->_hostName, $this->_hostPort );

			if ( null !== $this->_userName && null !== $this->_password )
			{
				$this->_sag->login( $this->_userName, $this->_password );
			}

			if ( null !== $this->_database )
			{
				$this->_sag->setDatabase(
					$this->_database,
					$this->getOption( \KismaOptions::CreateIfNotFound, false )
				);
			}
		}

		//*************************************************************************
		//* Default/Magic Methods
		//*************************************************************************

		/**
		 * Allow calling Aspect methods from the object
		 *
		 * @throws \BadMethodCallException
		 * @param string $method
		 * @param array  $arguments
		 * @return mixed
		 */
		public function __call( $method, $arguments )
		{
			//	Sag pass-through...
			if ( method_exists( $this->_sag, $method ) )
			{
				return call_user_func_array(
					array(
						$this->_sag,
						$method
					),
					$arguments
				);
			}

			//	No worky
			throw new \BadMethodCallException( __CLASS__ . '::' . $method . ' is undefined.' );
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param string $hostName
		 * @return \Kisma\Aspects\CouchDb
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
		 * @return \Kisma\Aspects\CouchDb
		 */
		public function setHostPort( $hostPort = 5984 )
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
		 * @return \Kisma\Aspects\CouchDb
		 */
		public function setPassword( $password = null )
		{
			$this->_password = $password;

			if ( null !== $this->_sag && null !== $this->_userName && null !== $this->_password )
			{
				$this->_sag->login( $this->_userName, $this->_password );
			}

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
		 * @return \Kisma\Aspects\CouchDb
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
		 * @param string $database
		 * @param bool $createIfNotFound
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		public function setDatabase( $database = null, $createIfNotFound = false )
		{
			$this->_database = $database;

			if ( null !== $this->_sag )
			{
				$this->_sag->setDatabase( $database, $createIfNotFound );
			}

			return $this;
		}

		/**
		 * @return string
		 */
		public function getDatabase()
		{
			return $this->_database;
		}

		/**
		 * @param \Sag $sag
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		public function setSag( $sag = null )
		{
			$this->_sag = $sag;
			return $this;
		}

		/**
		 * @return \Sag
		 */
		public function getSag()
		{
			return $this->_sag;
		}

	}
}