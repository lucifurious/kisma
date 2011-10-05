<?php
/**
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
 * @category	  Kisma_Aspects_Storage_CouchDb
 * @package	   kisma.aspects.storage
 * @namespace	 \Kisma\Aspects\Storage
 * @since		 v1.0.0
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
		'src',
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
	 * Sag Methods
	 * ===========
	 * @method mixed login( $user, $pass, $type = null )
	 * @method stdClass getSession()
	 * @method \Sag decode( $decode )
	 * @method mixed get( $url )
	 * @method mixed head( $url )
	 * @method mixed delete( $id, $rev )
	 * @method mixed put( $id, $data )
	 * @method mixed post( $data, $path = null )
	 * @method mixed bulk( $docs, $allOrNothing = false )
	 * @method mixed copy( $sourceId, $destinationId, $destinationRev )
	 * @method \Sag setDatabase( $databaseName, $createIfNotFound = false )
	 * @method mixed getAllDocs( $incDocs = false, $limit = null, $startKey = null, $endKey = null, $keys = null )
	 * @method mixed getAllDatabases()
	 * @method mixed generateIDs()
	 * @method mixed createDatabase( $databaseName )
	 * @method mixed deleteDatabase( $datbaseName )
	 * @method mixed replicate( $src, $target, $continuous = false, $createTarget = null, $filter = null, $filterQueryParams = null )
	 * @method mixed compact( $viewName = null )
	 * @method mixed setAttachment( $name, $data, $contentType, $docID, $rev = null )
	 * @method \Sag setOpenTimeout( $seconds )
	 * @method \Sag setRWTimeout( $seconds, $microSeconds = 0 )
	 * @method \Sag setCache( &$cacheImplementer )
	 * @method \SagCache getCache()
	 * @method string currentDatabase()
	 * @method stdClass getStats()
	 * @method \Sag setStaleDefault( $stale )
	 * @method \Sag setCookie( $key, $value )
	 * @method string getCookie( $key )
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
		/**
		 * @var string When opening a database, you have the option of having a design document created for you. If you do so,
		 * it will be called this
		 */
		protected $_designDocumentName = '_design/document';

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
		 * @param bool $createDesignIfNotFound
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		public function setDatabase( $database = null, $createIfNotFound = false, $createDesignIfNotFound = true )
		{
			$this->_database = $database;

			if ( null !== $this->_sag )
			{
				$this->_sag->setDatabase( $database, $createIfNotFound );

				//	Create a design document if desired, and not created already
				if ( false !== $createDesignIfNotFound )
				{
					try
					{
						if ( $this->_sag->head( $this->_designDocumentName )->headers->_HTTP->status != '404' )
						{
							return true;
						}
					}
					catch ( \Exception $_ex )
					{
						//	Some kinda hot tub database issue
						if ( 409 == $_ex->getCode() )
						{
							return $this;
						}
					}

					//	Build the design document
					$_doc = new \stdClass();
					$_doc->_id = $this->_designDocumentName;
					$_doc->views = new \stdClass();
					$_doc->views->createDate = new \stdClass();
					$_doc->views->createDate->map = 'function(doc) { emit(doc.create_date, null); }';

					try
					{
						//	Store it
						$this->_sag->put( $this->_designDocumentName, $_doc );
					}
					catch ( \Exception $_ex )
					{
						/**
						 * A 409 status code means there was a conflict, so another client already created the design doc for us. This is fine. */
						if ( 409 == $_ex->getCode() )
						{
							return $this;
						}
					}
				}
			}

			return $this;
		}

		/**
		 * @return string
		 */
		public function getDesignDocumentName()
		{
			return $this->_designDocumentName;
		}

		/**
		 * @param string $designDocumentName
		 * @return $this
		 */
		public function setDesignDocumentName( $designDocumentName )
		{
			$this->_designDocumentName = $designDocumentName;
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