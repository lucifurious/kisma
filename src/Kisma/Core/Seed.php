<?php
/**
 * Seed.php
 */
namespace Kisma\Core;
use \Kisma\Core\Utility;

/**
 * Seed
 * A nugget of goodness that grows into something wonderful
 *
 * Seed provides a simple publish/subscribe service. You're free to use it or not. Never required.
 *
 * Publish/Subscribe
 * =================
 * A simple publish/subscribe service. Yeah, fancy name for event system.
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
 * To disable this feature, set $discoverEvents to false before calling the parent constructor.
 *
 * Properties
 * ==========
 *
 * The properties below are default in every Seed object. In addition, when you constract a Seed
 * object, any values passed to the constructor will be set in the created object. There are no
 * checks for invalid properties. If the property does not exist, it will be added as public. No
 * getter or setter will be created however. Use of the new property is entirely up to you.
 *
 * @property-read string $id              A unique ID assigned to this object, the last part of which is the creation time
 * @property string      $tag             The tag of this object. Defaults to the base name of the class
 * @property string      $name            The name of this object. Defaults tot he class name
 * @property bool        $discoverEvents  Defaults to true.
 * @property string      $eventManager    Defaults to \Kisma\Core\Utility\EventManager
 */
class Seed implements \Kisma\Core\Interfaces\SeedLike, \Kisma\Core\Interfaces\PublisherLike
{
	//********************************************************************************
	//* Member Variables
	//********************************************************************************

	/**
	 * @var string The unique ID of this seed
	 */
	private $_id;
	/**
	 * @var string A "key" quality tag for this object. Defaults to the key-inflected base class name (i.e. "seed")
	 */
	protected $_tag;
	/**
	 * @var string A display quality name for this object. Defaults to the full class name (i.e. "\Kisma\Core\Seed")
	 */
	protected $_name;
	/**
	 * @var bool If false, event handlers must be defined manually
	 */
	protected $_discoverEvents = true;
	/**
	 * @var string The class name of the event manager
	 */
	protected $_eventManager = self::DefaultEventManager;

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
		//	Since $_id is read-only we remove if you try to set it
		if ( null !== ( $_id = Utility\Option::get( $settings, 'id' ) ) )
		{
			Utility\Option::remove( $settings, 'id' );
		}

		//	Otherwise, set the rest
		foreach ( $settings as $_key => $_value )
		{
			if ( property_exists( $this, $_key ) || property_exists( $this, '_' . $_key ) )
			{
				Utility\Option::set( $this, $_key, $_value );
				unset( $settings, $_key );
			}
		}

		//	Wake-up the events
		$this->__wakeup();
	}

	/**
	 * When unserializing an object, this will re-attach any event handlers...
	 */
	public function __wakeup()
	{
		//	This is my hash. There are many like it, but this one is mine.
		$this->_id = hash( 'sha512', spl_object_hash( $this ) . getmypid() . microtime( true ) );
		Utility\Log::debug( 'New seed spawned: ' . $this->_id );

		//	Auto-set tag and name if they're empty
		if ( null === $this->_tag )
		{
			$this->_tag = \Kisma\Core\Utility\Inflector::tag( get_called_class(), true );
		}

		if ( null === $this->_name )
		{
			$this->_name = \Kisma\Core\Utility\Inflector::tag( get_called_class() );
		}

		if ( !( $this instanceof \Kisma\Core\Interfaces\SubscriberLike ) || empty( $this->_eventManager ) )
		{
			//	Ignore event junk later
			$this->_eventManager = false;
			$this->_discoverEvents = false;
		}

		//	Add the event service and attach any event handlers we find...
		if ( false !== $this->_discoverEvents )
		{
			//	Subscribe to events...
			call_user_func(
				array( $this->_eventManager, 'subscribe' ),
				$this
			);
		}

		//	Publish after_construct event
		$this->publish( self::AfterConstruct );
	}

	/**
	 * Choose your destructor!
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
		//	A little chicanery...
		return
			false !== $this->_eventManager
				?
				call_user_func( array( $this->_eventManager, 'publish' ), $this, $eventName, $eventData )
				:
				false;
	}

	/**
	 * @param string        $tag
	 * @param callable|null $listener
	 *
	 * @return bool
	 */
	public function on( $tag, $listener = null )
	{
		if ( !( $this instanceof \Kisma\Core\Interfaces\SubscriberLike ) || empty( $this->_eventManager ) )
		{
			return false;
		}

		//	Otherwise add handler
		return call_user_func(
			array( $this->_eventManager, 'on' ),
			$this,
			$tag,
			$listener
		);
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param boolean $discoverEvents
	 *
	 * @return Seed
	 */
	public function setDiscoverEvents( $discoverEvents )
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
	 * @param string $eventManager
	 *
	 * @return Seed
	 */
	public function setEventManager( $eventManager )
	{
		$this->_eventManager = $eventManager;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEventManager()
	{
		return $this->_eventManager;
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
	 * @return Seed
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
	 * @param string $tag
	 *
	 * @return Seed
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