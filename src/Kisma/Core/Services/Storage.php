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
class Storage extends \Kisma\Core\Service implements \Kisma\Core\Interfaces\StorageProvider, \Kisma\Core\Interfaces\StorageService
{
	//********************************************************************************
	//* Member Variables
	//********************************************************************************

	/**
	 * @var array|\Kisma\Core\Interfaces\StorageService The storage array
	 */
	protected $_storage = array();

	//*************************************************************************
	//* Magical Crap
	//*************************************************************************

	/**
	 * @param array $data
	 */
	public function __construct( $data = array() )
	{
		$_storage = array();

		array_walk( \Kisma\Core\Utility\Option::clean( $data ),
			function ( $value, $key ) use ( &$_storage )
			{
				\Kisma\Core\Utility\Option::set( $_storage, $key, $value );
			}
		);

		//	Set the cleaned up storage center
		$this->_storage = $_storage;
		unset( $_storage );

		parent::__construct();
	}

	/**
	 * @param string $name
	 *
	 * @return array|bool|mixed|null|string
	 */
	public function __get( $name )
	{
		return call_user_func_array( array( $this, 'get' ), func_get_args() );
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return Storage
	 */
	public function __set( $name, $value )
	{
		return call_user_func_array( array( $this, 'set' ), func_get_args() );
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset( $name )
	{
		return is_array( $this->_storage ) &&
			array_key_exists( \Kisma\Core\Utility\Inflector::tag( $name, true ), $this->_storage );
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
		return true;
	}

	/**
	 * {@InheritDoc}
	 */
	public function initializeStorage( $options = array() )
	{
		$this->_storage = array();

		foreach ( \Kisma\Core\Utility\Option::merge( $this->getDefaultSettings(), $options ) as $_key => $_value )
		{
			\Kisma\Core\Utility\Option::set( $this->_storage, $_key, $_value );
		}

		return $this->_storage;
	}

	/**
	 * {@InheritDoc}
	 */
	public function set( $key, $value = null, $overwrite = false )
	{
		if ( empty( $this->_storage ) )
		{
			$this->initializeStorage();
		}

		$_settings = \Kisma\Core\Utility\Option::collapse( $key, $value );

		//	Can't do nothing 'til they stop sparklin'
		if ( !empty( $_settings ) )
		{
			//	Set the value(s)
			foreach ( $_settings as $_key => $_value )
			{
				//	Don't overwrite if not wanted...
				if ( false === $overwrite && isset( $this->_storage[\Kisma\Core\Utility\Inflector::tag( $_key, true )] ) )
				{
					continue;
				}

				\Kisma\Core\Utility\Option::set( $this->_storage, $_key, $_value );
			}
		}

		return $this;
	}

	/**
	 * {@InheritDoc}
	 */
	public function get( $key = null, $defaultValue = null, $burnAfterReading = false )
	{
		if ( empty( $this->_storage ) )
		{
			$this->initializeStorage();
		}

		if ( !is_array( $key ) )
		{
			return \Kisma\Core\Utility\Option::get( $this->_storage, $key, $defaultValue );
		}

		foreach ( $key as $_key )
		{
			if ( isset( $this->_storage[$_key] ) )
			{
				$key[$_key] = \Kisma\Core\Utility\Option::get( $this->_storage, $_key, $defaultValue );
			}
		}

		return $key;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param array|mixed $storage
	 *
	 * @return \Kisma\Core\Services\Storage
	 */
	public function setStorage( $storage )
	{
		$this->_storage = $storage;
		return $this;
	}

	/**
	 * @return array|\Kisma\Core\Interfaces\StorageService
	 */
	public function getStorage()
	{
		return $this->_storage;
	}

}
