<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license			http://github.com/Pogostick/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @package			kisma.container.couchdb
 * @since			v1.0.0
 * @filesource
 */
namespace Kisma\Container\Document\CouchDb;

use Kisma\K;
use Kisma\Utility;
use Doctrine\ODM\CouchDB\Mapping\Annotations\Document;
use Doctrine\Common\Annotations\Annotation;

/**
 * QueueItem
 * A queue item. Nothing more than a subclass that sets some standard queue item properties
 *
 * @Document
 * @property int $create_time
 * @property int $update_time
 * @property int $expire_time
 * @property mixed $queueData
 * @property mixed $locked
 * @property string $version
 */
class QueueItem extends \Kisma\Components\Seed
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The "name" of this document
	 */
	const DocumentName = 'Kisma\\Provider\\CouchDb\\Document\\QueueItem';

	//*************************************************************************
	//* Document Fields
	//*************************************************************************

	/**
	 * @Id
	 */
	protected $_id = null;
	/**
	 * @Version
	 */
	protected $_version = null;
	/**
	 * @Field(type="date")
	 */
	protected $_createTime = null;
	/**
	 * @Field(type="date")
	 */
	protected $_expireTime = -1;
	/**
	 * @Field(type="date")
	 */
	protected $_updateTime = null;
	/**
	 * @Field(type="object")
	 */
	protected $_queueData = null;
	/**
	 * @Field(type="boolean")
	 */
	protected $_locked = false;

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param $createTime
	 *
	 * @return QueueItem
	 */
	public function setCreateTime( $createTime )
	{
		$this->_createTime = $createTime;
		return $this;
	}

	/**
	 * @return null
	 */
	public function getCreateTime()
	{
		return $this->_createTime;
	}

	/**
	 * @param $expireTime
	 *
	 * @return QueueItem
	 */
	public function setExpireTime( $expireTime )
	{
		$this->_expireTime = $expireTime;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getExpireTime()
	{
		return $this->_expireTime;
	}

	/**
	 * @param $id
	 *
	 * @return QueueItem
	 */
	public function setId( $id )
	{
		$this->_id = $id;
		return $this;
	}

	/**
	 * @return null
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param $locked
	 *
	 * @return QueueItem
	 */
	public function setLocked( $locked )
	{
		$this->_locked = $locked;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function getLocked()
	{
		return $this->_locked;
	}

	/**
	 * @param $queueData
	 *
	 * @return QueueItem
	 */
	public function setQueueData( $queueData )
	{
		$this->_queueData = $queueData;
		return $this;
	}

	/**
	 * @return null
	 */
	public function getQueueData()
	{
		return $this->_queueData;
	}

	/**
	 * @param $updateTime
	 *
	 * @return QueueItem
	 */
	public function setUpdateTime( $updateTime )
	{
		$this->_updateTime = $updateTime;
		return $this;
	}

	/**
	 * @return null
	 */
	public function getUpdateTime()
	{
		return $this->_updateTime;
	}

	/**
	 * @param string $version
	 * @return QueueItem
	 */
	public function setVersion( $version )
	{
		$this->_version = $version;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return $this->_version;
	}
}
