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

/**
 * Seed
 * A nugget of goodness that grows into something wonderful
 *
 * Seed provides two services for a class. You're free to use it or not. Never required.
 *
 * Attribute Storage
 * =================
 * The first service is attribute storage. A seed can have pre-defined and late-bound
 * attributes simply by calling the object's set() method.
 *
 * Conversely, getting an attribute value is done by calling get().
 *
 * You can pass an array of attributes to the constructor to have them set for you. Otherwise you must call set()
 *
 * To disable this feature, set the '$attributeStorage' attribute to false before calling the parent constructor.
 *
 * Publish/Subscribe
 * =================
 * The second is a publish/subscribe service. Yeah, fancy name for event system.
 *
 * An event is defined by the presence of a method whose name starts with 'on'.
 * The event name is the method name. When an event is triggered, event handlers attached
 * to the event will be invoked automatically.
 *
 * An event is triggered by calling the {@link trigger} method. Attached event
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
 * To disable this feature, set the '$autoAttachEvents' attribute to false before calling the parent constructor.
 *
 * Default Attributes
 * ===================
 *
 * The following attributes are default in every Seed object:
 *
 * @property-read string                            $id                     The unique ID of this seed
 * @property string                                 $tag                    Defaults to class name with $id
 * @property string                                 $name                   Defaults to class name
 * @property bool                                   $autoAttachEvents       Defaults to true.
 * @property \Kisma\Core\Interfaces\StorageProvider $attributeStorage       The attributes storage object
 * @property string                                 $eventManager           Defaults to \Kisma\Utility\EventManager
 */
class Seed implements \Kisma\Core\Interfaces\SeedEvents, \Kisma\Core\Interfaces\SeedAttributes, \Kisma\Core\Interfaces\StorageProvider
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
	const DefaultEventManager = '\\Kisma\\Utility\\EventManager';

	//********************************************************************************
	//* Member Variables
	//********************************************************************************

	/**
	 * @var \Kisma\Core\Services\Storage Attributes storage.
	 */
	protected $_attributeStorage = null;

	//********************************************************************************
	//* Constructor/Magic
	//********************************************************************************

	/**
	 * Base constructor
	 *
	 * @param array|object $attributes An array of name/value pairs that will be placed into storage
	 *
	 * @throws \Kisma\InvalidAttributeValueException
	 */
	public function __construct( $attributes = array() )
	{
		//	Initialize the storage system
		$this->_initializeStorage( $attributes );

		//	Wake-up the events
		$this->__wakeup();
	}

	/**
	 * When unserializing an object, this will re-attach any event handlers...
	 */
	public function __wakeup()
	{
		//	Attach any event handlers we find if desired and object is a reactor...
		if ( true === $this->getAutoAttachEvents( true ) && $this instanceOf \Kisma\Core\Interfaces\Reactor )
		{
			\Kisma\Utility\EventManager::subscribe( $this );

			//	Publish after_construct event
			$this->trigger( $this, self::AfterConstruct );

			//	Register our faux-destructor
			$_seed = $this;

			\register_shutdown_function(
				function () use ( $_seed )
				{
					//	He's dead Jim.
					\Kisma\Utility\EventManager::publish( $_seed, Seed::BeforeDestruct );
				}
			);
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
	public function trigger( $eventName, $eventData = null )
	{
		$_manager = $this->getEventManager();

		if ( empty( $_manager ) )
		{
			return false;
		}

		return call_user_func( array( $_manager, 'publish' ), $this, $eventName, $eventData );
	}

	/**
	 * Allows for attribute access by using "get[_]<AttributeName>" and "set[_]<AttributeName>"
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 * @convenience
	 */
	public function __call( $name, $arguments )
	{
		//	If we don't have any attribute storage, bail
		if ( $this->_attributeStorage )
		{
			$_prefix = strtolower( substr( trim( $name ), 0, 3 ) );

			if ( 'get' == $_prefix || 'set' == $_prefix )
			{
				array_unshift( $arguments, substr( $name, ( '_' == $name[4] ? 4 : 3 ) ) );
				return call_user_func_array( array( $this->_attributeStorage, $_prefix ), $arguments );
			}
		}
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * @param array|object $attributes
	 *
	 * @return bool
	 * @throws \Kisma\InvalidAttributeValueException
	 */
	protected function _initializeStorage( $attributes = array() )
	{
		$_storageProvider = \Kisma\Utility\Option::get( $attributes, self::AttributeStorage, $this->_attributeStorage, true );

		if ( false === $_storageProvider )
		{
			return false;
		}

		//	Set our default storage class
		if ( null === $_storageProvider )
		{
			$_storageProvider = self::DefaultStorageProvider;
		}

		//	Must be a class name or implement the base storage provider interface
		if ( !is_string( $_storageProvider ) && !( $_storageProvider instanceof \Kisma\Core\Interfaces\StorageProvider ) )
		{
			throw new \Kisma\InvalidAttributeValueException( self::AttributeStorage, $_storageProvider );
		}

		$this->_attributeStorage = new $_storageProvider(
			$this->getDefaultAttributes()
		);

		//	Now add the attributes...
		$this->set( $attributes );

		//	We're done!
		return true;
	}

	//*************************************************************************
	//* Attribute Management
	//*************************************************************************

	/**
	 * Returns an array of default attributes to initialize storage
	 *
	 * @return array
	 */
	public function getDefaultAttributes()
	{
		return array(
			//	This is my hash. There are many like it, but this one is mine.
			self::Id               => spl_object_hash( $this ),
			self::Tag              => \Kisma\Utility\Inflector::tag( get_called_class(), true ),
			self::Name             => \Kisma\Utility\Inflector::tag( get_called_class() ),
			self::AutoAttachEvents => ( $this instanceof \Kisma\Core\Interfaces\Reactor ),
			self::EventManager     => ( $this instanceof \Kisma\Core\Interfaces\Reactor ? self::DefaultEventManager : null ),
		);
	}

	/**
	 * {@InheritDoc}
	 */
	public function get( $key = null, $defaultValue = null, $burnAfterReading = false )
	{
		return
			!empty( $this->_attributeStorage )
				?
				$this->_attributeStorage->get( $key, $defaultValue, $burnAfterReading )
				:
				$defaultValue;
	}

	/**
	 * {@InheritDoc}
	 */
	public function set( $key, $value = null, $overwrite = true )
	{
		return
			!empty( $this->_attributeStorage )
				?
				$this->_attributeStorage->set( $key, $value, $overwrite = true )
				:
				false;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Interfaces\StorageProvider $storage
	 *
	 * @return \Kisma\Core\Seed
	 */
	public function setAttributeStorage( $storage )
	{
		$this->_attributeStorage = $storage;
		return $this;
	}

	/**
	 * @return \Kisma\Core\Interfaces\StorageProvider
	 */
	public function getAttributeStorage()
	{
		return $this->_attributeStorage;
	}

}
