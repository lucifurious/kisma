<?php
/**
 * Storage.php
 * Provides a base for data storage services
 *
 * @description Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * @copyright   Copyright (c) 2009-2012 Jerry Ablan
 * @license     http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author      Jerry Ablan <get.kisma@gmail.com>
 */
namespace Kisma\Core\Services;

/**
 * Storage
 * A dead-simple storage class.  Keeps key value pairs in an array.
 */
class Storage extends \Kisma\Core\Service
{
	//********************************************************************************
	//* Member Variables
	//********************************************************************************

	/**
	 * @var array|mixed Object attributes storage. Set to false to disable feature
	 */
	protected $_storage = array();

	//*************************************************************************
	//* Magical Crap
	//*************************************************************************

	/**
	 * @param string $name
	 *
	 * @return array|bool|mixed|null|string
	 */
	public function __get( $name )
	{
		return $this->get( $name );
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return Storage
	 */
	public function __set( $name, $value )
	{
		return $this->set( $name, $value );
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset( $name )
	{
		return isset( $this->_attributes, $this->_attributes[$name] );
	}

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * When a service is constructed, this method is called by default
	 *
	 * @param array $options
	 *
	 * @return bool
	 */
	public function initialize( $options = array() )
	{
		parent::initialize( $options );

		if ( empty( $this->_serviceName ) )
		{
			$this->_serviceName = \Kisma\Utility\Inflector::tag( get_called_class() );
		}

		return true;
	}

	/**
	 * Sets the values of one or more attributes in storage
	 *
	 * @param string|array $key
	 * @param mixed|null   $value
	 * @param bool         $overwrite If an array of keys was passed, setting this to true will replace the existing storage contents
	 *
	 * @return bool
	 */
	public function set( $key, $value = null, $overwrite = false )
	{
		if ( false === $this->_storage )
		{
			return false;
		}

		//	First time in?
		if ( null === $this->_storage )
		{
			$this->initializeStorage();
		}

		$_attributes = \Kisma\Utility\Option::collapse( $key, $value );

		//	Can't do nothing 'til they stop sparklin'
		if ( !empty( $_attributes ) )
		{
			//	Overwrite if the conditions are right
			if ( false !== $overwrite && is_array( $key ) && null === $value )
			{
				//	Overwrite the attributes...
				$this->_storage = $key;
			}
			else
			{
				//	Merge the options...
				\Kisma\Utility\Option::set( $this->_storage, $_attributes );
			}
		}

		return true;
	}

	/**
	 * Gets the values of one or more attributes from storage
	 *
	 * @param string|array $key          Single key or an array of keys
	 * @param mixed        $defaultValue Will affect default value of all array values if array requested
	 *
	 * @return array|bool|mixed|null|string
	 */
	public function get( $key = null, $defaultValue = null )
	{
		if ( false === $this->_storage )
		{
			return null;
		}

		if ( empty( $key ) )
		{
			return $this->getStorage();
		}

		if ( !is_array( $key ) )
		{
			return \Kisma\Utility\Option::get( $this->_storage, $key, $defaultValue );
		}

		foreach ( $key as $_key )
		{
			if ( isset( $this->_storage[$_key] ) )
			{
				$key[$_key] = \Kisma\Utility\Option::get( $this->_storage, $_key, $defaultValue );
			}
		}

		return $key;
	}

	/**
	 * Base initialization of storage. Child classes can override to use a database or document store
	 *
	 * @return array
	 */
	public function initializeStorage()
	{
		$this->_storage = array();

		foreach ( $this->getDefaultAttributes() as $_attribute => $_defaultValue )
		{
			\Kisma\Utility\Option::set( $this->_storage, $_attribute, $_defaultValue );
		}

		return $this->_storage;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param array|mixed $storage
	 *
	 * @return \Kisma\Core\Seed
	 */
	public function setStorage( $storage )
	{
		$this->_storage = $storage;
		return $this;
	}

	/**
	 * @return array|mixed
	 */
	public function getStorage()
	{
		return $this->_storage;
	}

}
