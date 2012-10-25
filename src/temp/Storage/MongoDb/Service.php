<?php
/**
 * Service.php
 *
 * @copyright Copyright (c) 2012 Silverpop Systems, Inc.
 * @link      http://www.silverpop.com Silverpop Systems, Inc.
 * @author    Jerry Ablan <jablan@silverpop.com>
 *
 * @filesource
 */
namespace CIS\Services\Storage\MongoDb;

/**
 * Service
 */
class Service extends \CIS\Services\BaseStorageService
{
	//**************************************************************************
	//* Private Members
	//**************************************************************************

	/**
	 * @var \Mongo
	 */
	protected $_mongo = null;
	/**
	 * @var bool
	 */
	protected $_connect = true;
	/**
	 * @var int
	 */
	protected $_timeout = 15000;
	/**
	 * @var string
	 */
	protected $_replicaSet = null;

	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * @param array $options
	 *
	 * @throws \CIS\Exceptions\ConfigurationException
	 */
	public function __construct( $options = array() )
	{
		if ( is_string( $options ) )
		{
			$options = array(
				'connectionString' => $options,
			);
		}

		parent::__construct( $options );

		if ( null === $this->_connectionString )
		{
			throw new \CIS\Exceptions\ConfigurationException( 'You must specify the "connectionString" for this service.' );
		}

		if ( null === $this->_mongo )
		{
			$this->_mongo = new \Mongo(
				$this->_connectionString,
				array(
					'connect'    => $this->_connect,
					'timeout'    => $this->_timeout,
					'replicaSet' => $this->_replicaSet,
				)
			);
		}
	}

	//**************************************************************************
	//* Properties
	//**************************************************************************

	/**
	 * @param boolean $connect
	 *
	 * @return boolean
	 */
	public function setConnect( $connect )
	{
		$this->_connect = $connect;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getConnect()
	{
		return $this->_connect;
	}

	/**
	 * @param \Mongo $mongo
	 *
	 * @return \Mongo
	 */
	public function setMongo( $mongo )
	{
		$this->_mongo = $mongo;
		return $this;
	}

	/**
	 * @return \Mongo
	 */
	public function getMongo()
	{
		return $this->_mongo;
	}

	/**
	 * @param string $replicaSet
	 *
	 * @return string
	 */
	public function setReplicaSet( $replicaSet )
	{
		$this->_replicaSet = $replicaSet;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getReplicaSet()
	{
		return $this->_replicaSet;
	}

	/**
	 * @param int $timeout
	 *
	 * @return int
	 */
	public function setTimeout( $timeout )
	{
		$this->_timeout = $timeout;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getTimeout()
	{
		return $this->_timeout;
	}

}
