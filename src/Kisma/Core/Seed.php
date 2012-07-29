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
 * public function onEventName( $event ) { ... }
 * </pre>
 *
 * OR
 *
 * <pre>
 * [private|protected] function _onEventName( $event ) { ... }
 * </pre>
 *
 * $event will contain details about the event in question.
 *
 * To subscribe to an event, call {@link EventManager::subscribe} method.
 *
 * Both property names and event names are case-insensitive.
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
 * To disable this feature, set the 'autoAttachEvents' attribute to false during construction
 *
 * Built-in Attributes
 * ===================
 *
 * @property bool $autoAttachEvents
 */
abstract class Seed implements \Kisma\Core\Interfaces\SeedEvents
{
	//********************************************************************************
	//* Member Variables
	//********************************************************************************

	/**
	 * @var string My unique id
	 */
	private $_seedId = null;
	/**
	 * @var array|mixed Object attributes storage. Set to false to disable feature
	 */
	protected $_storage = array();

	//********************************************************************************
	//* Constructor/Magic
	//********************************************************************************

	/**
	 * Base constructor
	 *
	 * @param array|\stdClass $attributes An array of name/value pairs that will be placed into storage
	 */
	public function __construct( $attributes = array() )
	{
		//	This is my seed. There are many like it, but this one is mine.
		$this->_seedId = spl_object_hash( $this );

		//	Set the attributes
		$this->set( $attributes );

		//	Wake-up the events
		$this->__wakeup();
	}

	/**
	 * When unserializing an object, this will re-attach any event handlers...
	 */
	public function __wakeup()
	{
		//	Attach any event handlers we find if desired and object is a reactor...
		if ( $this instanceOf \Kisma\Core\Interfaces\Reactor && $this->get( 'auto_attach_events' ) )
		{
			\Kisma\Utility\EventManager::subscribe( $this );
		}

		//	Publish after_construct event
		$this->trigger( $this, self::AfterConstruct );
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		//	Fire the initialize event
		try
		{
			//	To prevent that freaky frame 0 error, I'm wrapping and gagging
			@$this->trigger( $this, self::BeforeDestruct );
		}
		catch ( \Exception $_ex )
		{
			//	Ignored on porpoise
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
		return \Kisma\Utility\EventManager::publish( $this, $eventName, $eventData );
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
			'auto_attach_events' => true,
		);
	}

	/**
	 * Sets the values of one or more attributes in storage
	 *
	 * @param string|array $key
	 * @param mixed|null   $value
	 * @param bool         $overwrite If an array of keys was passed, setting this to true will replace the existing storage contents
	 *
	 * @return mixed
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

		$_attributes = ( is_array( $key ) && null === $value ) ? $key : array( $key => $value );

		//	Can't do nothing 'til they stop sparklin'
		if ( !empty( $_attributes ) )
		{
			//	Overwrite if the conditions are right
			if ( true === $overwrite && is_array( $key ) && null === $value )
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

		return $this;
	}

	/**
	 * Gets the values of one or more attributes from storage
	 *
	 * @param string|array $key
	 *
	 * @return array|bool|mixed|null|string
	 */
	public function get( $key = null )
	{
		if ( false === $this->_storage )
		{
			return false;
		}

		if ( empty( $key ) )
		{
			return $this->getStorage();
		}

		if ( !is_array( $key ) )
		{
			return \Kisma\Utility\Option::get( $key );
		}

		foreach ( $key as $_key )
		{
			if ( isset( $this->_storage[$_key] ) )
			{
				$key[$_key] = \Kisma\Utility\Option::get( $this->_storage, $_key );
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
	 * @return string
	 */
	public function getSeedId()
	{
		return $this->_seedId;
	}

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
