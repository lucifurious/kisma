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

use Kisma\Core\Interfaces\SeedEvents;

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
abstract class Seed implements SeedEvents
{
	//********************************************************************************
	//* Member Variables
	//********************************************************************************

	/**
	 * @var string My unique id
	 */
	private $_seedId = null;
	/**
	 * @var \Kisma\Core\Services\Storage Attributes storage. Set to false to disable feature
	 */
	protected $_attributes = null;

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
		//	This is my hash. There are many like it, but this one is mine.
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
		if ( $this instanceOf \Kisma\Core\Interfaces\Reactor && $this->_attributes->get( 'auto_attach_events' ) )
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
		if ( false !== $this->_attributes )
		{
			if ( empty( $this->_attributes ) )
			{
				//	Create a new storage object
				$this->_attributes = new \Kisma\Core\Services\Storage(
					$this->getDefaultAttributes()
				);
			}
		}

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
		$this->_attributes = $storage;
		return $this;
	}

	/**
	 * @return array|mixed
	 */
	public function getStorage()
	{
		return $this->_attributes;
	}

}
