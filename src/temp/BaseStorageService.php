<?php
/**
 * BaseStorageService.php
 *
 * Copyright (c) 2012 Silverpop Systems, Inc.
 * http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 * @filesource
 */
namespace CIS\Services;

/**
 * BaseStorageService
 * The base class for storage services.
 */
abstract class BaseStorageService extends BaseService implements \CIS\Interfaces\StorageEvents
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
	protected $_passwordName = null;
	/**
	 * @var string
	 */
	protected $_connectionString = null;

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * @param \CIS\Services\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeFind( $event )
	{
		//	Default implementation
		return true;
	}

	/**
	 * @param \CIS\Services\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onAfterFind( $event )
	{
		//	Default implementation
		return true;
	}

	/**
	 * @param \CIS\Services\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeSave( $event )
	{
		//	Default implementation
		return true;
	}

	/**
	 * @param \CIS\Services\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onAfterSave( $event )
	{
		//	Default implementation
		return true;
	}

	/**
	 * @param \CIS\Services\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeDelete( $event )
	{
		//	Default implementation
		return true;
	}

	/**
	 * @param \CIS\Services\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onAfterDelete( $event )
	{
		//	Default implementation
		return true;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param string $hostName
	 *
	 * @return string
	 */
	public function setHostName( $hostName )
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
	 *
	 * @return int
	 */
	public function setHostPort( $hostPort )
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
	 * @param string $passwordName
	 *
	 * @return string
	 */
	public function setPasswordName( $passwordName )
	{
		$this->_passwordName = $passwordName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPasswordName()
	{
		return $this->_passwordName;
	}

	/**
	 * @param string $userName
	 *
	 * @return string
	 */
	public function setUserName( $userName )
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
	 * @param string $connectionString
	 *
	 * @return string
	 */
	public function setConnectionString( $connectionString )
	{
		$this->_connectionString = $connectionString;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getConnectionString()
	{
		return $this->_connectionString;
	}
}
