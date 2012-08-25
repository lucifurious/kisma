<?php
/**
 * Seed.php
 * Provides a base for Kisma components and objects
 *
 * @description Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * @copyright   Copyright (c) 2009-2012 Jerry Ablan
 * @license     http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author      Jerry Ablan <get.kisma@gmail.com>
 */
namespace Kisma\Core;

use Kisma\Core\Interfaces;
use BadMethodCallException;
use Kisma\Core\Exceptions\InvalidSettingKeyException;

/**
 * Seed
 * A nugget of goodness that grows into something wonderful
 *
 * Seed provides two services for a class. You're free to use it or not. Never required.
 *
 * Settings Storage
 * =================
 * The first service is settings, or attribute, storage. A seed can have pre-defined
 * and late-bound settings simply by calling the object's set() method.
 *
 * Conversely, getting any setting value is done by calling get().
 *
 * You can pass an array of settings to the constructor to have them set for you. Otherwise you must call set()
 *
 * Publish/Subscribe
 * =================
 * The second is a publish/subscribe service. Yeah, fancy name for event system.
 *
 * An event is defined by the presence of a method whose name starts with 'on'.
 * The event name is the method name. When an event is triggered, event handlers attached
 * to the event will be invoked automatically.
 *
 * An event is triggered by calling the {@link publish} method. Attached event
 * handlers will be invoked automatically in the order they were attached to the event.
 *
 * Event handlers should have the following signature:
 * <pre>
 * public|protected|private function [_]onEventName( $event = null ) { ... }
 * </pre>
 *
 * $event (\Kisma\Core\Events\SeedEvent) will contain details about the event in question.
 *
 * To subscribe to an event, call the {@link EventManager::subscribe} method.
 *
 * You may also use closures for event handlers, ala jQuery
 *
 * This class has a two default events:
 *   - after_construct
 *   - before_destruct
 *
 * Unless otherwise specified, the object will automatically search for and
 * attach any event handlers that exist in your object.
 *
 * To disable this feature, set the '$autoAttachEvents' setting to false before calling the parent constructor.
 *
 * Properties
 * ==========
 *
 * The following properties are default in every Seed object:
 *
 * @property-read string                            $id
 * @property string                                 $tag
 * @property string                                 $name
 * @property bool                                   $autoAttachEvents Defaults to true.
 * @property \Kisma\Core\Interfaces\StorageProvider $settings         The settings storage object
 * @property string                                 $eventManager     Defaults to \Kisma\Core\Utility\EventManager
 */
class Seed implements Interfaces\Events\Publisher, Interfaces\Events\Seed, Interfaces\StorageProvider
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The default storage provider for all objects
	 */
	const DefaultStorageProvider = '\\Kisma\\Core\\Services\\Storage';
	/**
	 * @var string The default event manager for an object
	 */
	const DefaultEventManager = '\\Kisma\\Core\\Utility\\EventManager';

	//********************************************************************************
	//* Member Variables
	//********************************************************************************

	/**
	 * @var string The unique ID of this seed
	 */
	protected $_id;
	/**
	 * @var string Defaults to base class name (i.e. seed)
	 */
	protected $_tag;
	/**
	 * @var string Defaults to the full class name (i.e. kisma.core.seed)
	 */
	protected $_name;
	/**
	 * @var bool If false, event handlers must be defined manually (i.e. by you)
	 */
	protected $_discoverEvents = true;
	/**
	 * @var array A map of services available to this object and their service class
	 */
	protected $_serviceMap = array();
	/**
	 * @var \Kisma\Core\Interfaces\StorageProvider Settings storage service
	 */
	protected $_settings = null;

	//********************************************************************************
	//* Constructor/Magic
	//********************************************************************************

	/**
	 * Base constructor
	 *
	 * @param array|object $settings An array of key/value pairs that will be placed into storage
	 */
	public function __construct( $settings = array() )
	{
		//	Wake-up the events
		$this->__wakeup();

		//	Initialize storage
		$this->_initializeStorage( $settings );
	}

	/**
	 * When unserializing an object, this will re-attach any event handlers...
	 */
	public function __wakeup()
	{
		//	This is my hash. There are many like it, but this one is mine.
		$this->_id = spl_object_hash( $this );
		$this->_tag = \Kisma\Core\Utility\Inflector::tag( get_called_class(), true );
		$this->_name = \Kisma\Core\Utility\Inflector::tag( get_called_class() );

		//	Wire in the event service if we're a subscriber
		if ( false !== ( $this->_discoverEvents = ( $this->_discoverEvents && ( $this instanceof \Kisma\Core\Interfaces\Subscriber ) ) ) )
		{
			//	Add the event service and attach any event handlers we find...
			if ( false !== ( $_manager = $this->getServiceClass( 'event_manager', self::DefaultEventManager ) ) )
			{
				//	Add the service
				$this->addServiceClass( 'event_manager', $_manager );

				//	Subscribe to events...
				call_user_func(
					array( $_manager, 'subscribe' ),
					$this
				);
			}
		}

		//	Publish after_construct event
		$this->publish( self::AfterConstruct );
	}

	/**
	 *
	 */
	public function __destruct()
	{
		try
		{
			//	Publish after_destruct event
			$this->publish( self::BeforeDestruct );
		}
		catch ( \Exception $_ex )
		{
			//	Does nothing, like the goggles.,,
			//	Well, may stop those bogus frame 0 errors too...
		}
	}

	//********************************************************************************
	//* Base Event Handlers
	//********************************************************************************

	/**
	 * The default afterConstruct event handler
	 *
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onAfterConstruct( $event = null )
	{
		return true;
	}

	/**
	 * The default beforeDestruct event handler
	 *
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeDestruct( $event = null )
	{
		return true;
	}

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Triggers an object event to all subscribers. Convenient wrapper on EM::publish
	 *
	 * @param string $eventName
	 * @param mixed  $eventData
	 *
	 * @return bool|int
	 */
	public function publish( $eventName, $eventData = null )
	{
		//	Can we publish?
		if ( false === ( $_manager = \Kisma\Core\Utility\EventManager::canPublish( $this ) ) )
		{
			return false;
		}

		//	A little chicanery...
		return call_user_func( array( $_manager, 'publish' ), $this, $eventName, $eventData );
	}

	/**
	 * Allows for attribute access by using "get[_]<AttributeName>" and "set[_]<AttributeName>"
	 *
	 * @param string $name
	 * @param array  $arguments
	 *
	 * @return mixed
	 */
	public function __call( $name, $arguments )
	{
		//	If we don't have any settings storage, bail
		if ( !empty( $this->_settings ) )
		{
			$_prefix = strtolower( substr( trim( $name ), 0, 3 ) );

			if ( 'get' == $_prefix || 'set' == $_prefix )
			{
				array_unshift( $arguments, substr( $name, ( '_' == $name[4] ? 4 : 3 ) ) );
				return call_user_func_array( array( $this->_settings, $_prefix ), $arguments );
			}
		}

		//	Done
		throw new \BadMethodCallException( 'The method "' . $name . '" is not defined.' );
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * @param array|object $settings
	 *
	 * @return bool
	 */
	protected function _initializeStorage( $settings = array() )
	{
		//	Storage services have to deal with their own junk
		if ( !( $this instanceof \Kisma\Core\Interfaces\StorageService ) )
		{
			$_storageProvider = is_string( $settings ) ? $settings : \Kisma\Core\Utility\Option::get( $settings, 'storageProvider', null, true );

			//	Set our default storage class
			if ( null === $_storageProvider )
			{
				$_storageProvider = self::DefaultStorageProvider;
			}

			//	Must be a class name or implement the base storage provider interface
			if ( !is_string( $_storageProvider ) && !( $_storageProvider instanceof \Kisma\Core\Interfaces\StorageProvider ) )
			{
				throw new InvalidSettingKeyException( 'The setting "storageProvider" is either not set or bogus: ' . $_storageProvider );
			}

			//	Now add the settings...
			$this->_settings = new $_storageProvider(
				\Kisma\Core\Utility\Option::merge(
					$settings,
					$this->getDefaultSettings()
				)
			);
		}

		//	We're done!
		return true;
	}

	//*************************************************************************
	//* Setting Management
	//*************************************************************************

	/**
	 * Returns an array of default settings to initialize storage
	 *
	 * @return array
	 */
	public function getDefaultSettings()
	{
		return array();
	}

	/**
	 * {@InheritDoc}
	 */
	public function get( $key = null, $defaultValue = null, $burnAfterReading = false )
	{
		return
			$this->_settings instanceof \Kisma\Core\Interfaces\StorageProvider
				?
				$this->_settings->get( $key, $defaultValue, $burnAfterReading )
				:
				$defaultValue;
	}

	/**
	 * {@InheritDoc}
	 */
	public function set( $key, $value = null, $overwrite = true )
	{
		return
			$this->_settings instanceof \Kisma\Core\Interfaces\StorageProvider
				?
				$this->_settings->set( $key, $value, $overwrite )
				:
				false;
	}

	/**
	 * @param string $name
	 * @param bool   $defaultClass
	 *
	 * @return string|SeedService
	 */
	public function getServiceClass( $name, $defaultClass = false )
	{
		return \Kisma\Core\Utility\Option::get( $this->_serviceMap, $name, $defaultClass );
	}

	/**
	 * @param string         $name
	 * @param string|SeedService $service
	 *
	 * @return string|SeedService
	 */
	public function addServiceClass( $name, $service )
	{
		\Kisma\Core\Utility\Option::set( $this->_serviceMap, $name, $service );
		return $service;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Interfaces\StorageProvider $storage
	 *
	 * @return \Kisma\Core\Seed
	 */
	public function setSettings( $storage )
	{
		$this->_settings = $storage;
		return $this;
	}

	/**
	 * @return \Kisma\Core\Interfaces\StorageProvider
	 */
	public function getSettings()
	{
		return $this->_settings;
	}

	/**
	 * @param boolean $discoverEvents
	 *
	 * @return \Kisma\Core\Seed
	 */
	public function setDiscoverEvents( $discoverEvents = true )
	{
		$this->_discoverEvents = $discoverEvents;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getDiscoverEvents()
	{
		return $this->_discoverEvents;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param string $name
	 *
	 * @return \Kisma\Core\Seed
	 */
	public function setName( $name )
	{
		$this->_name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param array $serviceMap
	 *
	 * @return \Kisma\Core\Seed
	 */
	public function setServiceMap( $serviceMap = array() )
	{
		$this->_serviceMap = $serviceMap;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getServiceMap()
	{
		return $this->_serviceMap;
	}

	/**
	 * @param string $tag
	 *
	 * @return \Kisma\Core\Seed
	 */
	public function setTag( $tag )
	{
		$this->_tag = $tag;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTag()
	{
		return $this->_tag;
	}

}