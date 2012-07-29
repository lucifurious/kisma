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

use Kisma\Utility\EventManager;

/**
 * Seed
 * A nugget of goodness that grows into something wonderful
 *
 * Seed implements a protocol of defining properties and events.
 *
 * A property is defined by a getter method, and/or a setter method.
 * Properties can be accessed in the way like accessing normal object members.
 * Reading or writing a property will cause the invocation of the corresponding
 * getter or setter method, e.g
 * <pre>
 * $_a = $_object->text;    // equivalent to $_a = $_object->getText();
 * $_object->text = 'abc';    // equivalent to $_object->setText( 'abc' );
 * </pre>
 *
 * The signature of getter and setter methods is as follows,
 *
 * <pre>
 * //    Getter method defines a readable property
 * public function getText() { ... }
 *
 * //    Setter method defines a writable property with $value to be set to the property
 * public function setText( $value ) { ... }
 * </pre>
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
 * To disable this feature, set 'autoAttachEvents' to false during construction
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
	 * @var array|mixed The place where this objects data is stored
	 */
	protected $_storage = array();

	//********************************************************************************
	//* Constructor/Magic
	//********************************************************************************

	/**
	 * Base constructor
	 *
	 * @param array|\stdClass $options An array of name/value pairs that will get assigned to object properties should those properties exist
	 */
	public function __construct( $options = array() )
	{
		//	This is my seed. There are many like it, but this one is mine.
		$this->_seedId = spl_object_hash( $this );

		//	If an option list is specified, try to assign values to properties automatically
		$this->set( $options );

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
			EventManager::subscribe( $this );
		}

		//	Publish after_construct event
		EventManager::publish( $this, self::AfterConstruct );
	}

	/**
	 * Destructor stub
	 */
	public function __destruct()
	{
		//	Fire the initialize event
		try
		{
			//	To prevent that freaky frame 0 error, I'm wrapping and gagging
			@EventManager::publish( $this, self::BeforeDestruct );
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
		return EventManager::publish( $this, $eventName, $eventData );
	}

	/**
	 * Sets the values of one or more keys in storage
	 *
	 * @param string|array $key
	 * @param mixed|null   $value
	 *
	 * @param bool         $mergeOptions
	 *
	 * @return mixed
	 */
	public function set( $key, $value = null, $mergeOptions = true )
	{
		if ( is_array( $key ) && null === $value )
		{
			$_options = $key;
		}
		else
		{
			$_options = array(
				$key,
				$value,
			);
		}

		//	Catch null input, non-traversable, or empty options
		if ( empty( $_options ) || ( !is_array( $_options ) && !( $_options instanceof \Traversable ) && !( $_options instanceof \stdClass ) ) )
		{
			$_options = array();
		}

		//	Set our own options and work from there
		if ( true !== $mergeOptions || !is_array( $_options ) )
		{
			//	Overwrite the options...
			$this->_storage = (array)$_options;
		}
		else
		{
			//	Merge the options...
			$this->_storage = array_merge( $this->_storage, (array)$_options );
		}

		return $this;
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
	 * @return array|mixed
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
