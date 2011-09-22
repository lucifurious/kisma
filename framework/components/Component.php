<?php
/**
 * Kisma(tm) : PHP Microframework (http://github.com/lucifurious/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/lucifurious/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/lucifurious/kisma/ Kisma(tm)
 * @license			http://github.com/lucifurious/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @package			kisma
 * @namespace		\Kisma\Components
 * @since			v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @throws \BadMethodCallException|Components\InvalidArgumentException|Components\UndefinedPropertyException
 * @namespace Kisma\Components Kisma components
 */
namespace Kisma\Components;
use \Kisma\Kisma as K;
/**
 * Component
 * The womb within...
 *
 * @property array $options
 * @property Event[] $events
 * @property Aspect[] $aspects
 * @property array $errors
 * @property int $index
 * @property-read int $count
 * @property bool $skipNext
 * @property bool $readOnly
 * @property bool $logging
 * @property string $eventHandlerSignature
 */
class Component implements \Kisma\IKisma, \Kisma\IAspectable, \Kisma\IOptions, \Countable, \Iterator
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/***
	 * @var array This object's options
	 */
	protected $_options = array();
	/**
	 * @var array This component's events
	 */
	protected $_events = array();
	/**
	 * @var Aspect[] This object's aspects
	 */
	protected $_aspects = array();
	/**
	 * @var Exception[]|array
	 */
	protected $_errors = array();
	/**
	 * @var integer Iteration index
	 */
	protected $_index = 0;
	/**
	 * @var integer Holds the number of settings we have
	 */
	protected $_count = 0;
	/**
	 * @var boolean Used when un-setting values during iteration to ensure we do not skip the next element
	 */
	protected $_skipNext = false;
	/**
	 * @var boolean If true, configuration settings cannot be changed once loaded
	 */
	protected $_readOnly = true;
	/**
	 * @var bool|int The logging flags for this object
	 */
	protected $_logging = true;
	/**
	 * @var string The prefix of a method indicating it is an event handler
	 */
	protected $_eventHandlerSignature = 'on';

	//*************************************************************************
	//* Default/Magic Methods
	//*************************************************************************

	/**
	 * The base component constructor
	 * @param array $options
	 * @return \Kisma\Components\Component
	 */
	public function __construct( $options = array() )
	{
		//	Configure our properties
		$this->_loadConfiguration( $options, true );

		//	Create our arrays
		$this->_errors = array();
		$this->_events = array();
		$this->_aspects = array();

		//	Auto-bind events, remove from $options
		$this->bindEvents( K::o( $options, 'eventOptions' ) );

		//	Set our count...
		$this->_count = count( $this->_options );

		//	Trigger our afterConstruct event
		$this->trigger( 'after_constructor' );
	}

	/**
	 * Calls the named method which is not a class method.
	 */
	/**
	 * @throws \BadMethodCallException
	 * @param string $method
	 * @param array  $arguments
	 * @return mixed
	 */
	public function __call( $method, $arguments )
	{
//		if ( method_exists( $this, $method ) )
//		{
//			return call_user_func_array( array( $this, $method ), $arguments );
//		}

		//	Call aspect methods if they exist
		foreach ( $this->_aspects as $_aspect )
		{
			if ( method_exists( $_aspect, $method ) )
			{
				return call_user_func_array( array( $_aspect, $method ), $arguments );
			}
		}

		throw new \BadMethodCallException( __CLASS__ . '::' . $method . ' is undefined.' );
	}

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Looks for event handler signatures and auto-binds the events.
	 * Event handler signatures start with the word 'on'.
	 *
	 * @param array $options
	 * @return bool
	 */
	public function bindEvents( $options = array() )
	{
		$_this = new \ReflectionClass( $this );
		$_ignores = K::o( $options, 'ignores', array() );

		//	Clean up the ignore list
		array_walk( $_ignores, function( &$ignore ) {
			$ignore = K::standardizeName( $ignore );
		});

		foreach ( $_this->getMethods() as $_method )
		{
			/** @noinspection PhpUndefinedFieldInspection */
			$_methodName = $_method->name;

			if ( $this->_eventHandlerSignature == strtolower( substr( $_methodName, 0, strlen( $this->_eventHandlerSignature ) ) ) )
			{
				/** @noinspection PhpUndefinedFieldInspection */
				$_eventName = K::standardizeName( substr( $_methodName, strlen( $this->_eventHandlerSignature ) ) );

				if ( !in_array( $_eventName, $_ignores ) )
				{
					/** @noinspection PhpUndefinedFieldInspection */
					$this->bind(
						$_eventName,
						array(
							$this,
							$_method->name,
						)
					);
				}
			}
		}
	}

	/**
	 * @throws InvalidArgumentException
	 * @param string $name
	 * @param callback|null $callback
	 * @return void
	 */
	public function bind( $name, $callback = null )
	{
		//	Make $data optional
		if ( !is_callable( $callback ) )
		{
			throw new InvalidArgumentException( 'The specified event handler is not callable.' );
		}

		//	Standardize the event name
		$_eventName = K::standardizeName( $name );

		//	Get the handler list for this event
		if ( !isset( $this->_events[$_eventName] ) )
		{
			$this->_events[$_eventName] = array();
		}

		//	Add this handler to the array
		$this->_events[$_eventName][] = $callback;
	}

	/**
	 * @throws InvalidArgumentException
	 * @param string $name
	 * @param callback|null $callback
	 * @return void
	 */
	public function unbind( $name, callback $callback = null )
	{
		//	Standardize the event name
		$_eventName = K::standardizeName( $name );

		//	Get the handler list for this event
		if ( isset( $this->_events[$_eventName] ) )
		{
			foreach ( $this->_events[$_eventName] as $_index => $_callback )
			{
				if ( $_callback == $callback )
				{
					unset( $this->_events[$_eventName][$_index] );
				}
			}
		}
	}

	/**
	 * @param string $name
	 * @param \Kisma\Events\Event|null $data
	 * @return bool
	 */
	public function trigger( $name, $data = null )
	{
		$_eventName = K::standardizeName( $name );

		//	If there is no handler defined, act like it's all good
		if ( isset( $this->_events[$_eventName] ) )
		{
			//	Create an event object...
			if ( $data instanceof \Kisma\Events\Event )
			{
				$_event = $data;
			}
			else
			{
				$_event = new \Kisma\Events\Event( $this, $data );
			}

			//	Loop through the handlers for this event, passing data
			foreach ( $this->_events[$_eventName] as $_handler )
			{
				//	Call each handler
				$_result = call_user_func( $_handler, $_event );

				//	If an event returns false, stop propagation unsuccessfully
				if ( false === $_result )
				{
					return false;
				}

				//	Stop propagation successfully
				if ( true === $_event->getHandled() )
				{
					break;
				}
			}
		}

		//	We made it through, so return true
		return true;
	}

	/**
	 * Link an aspect to this component
	 * @param $name
	 * @param array $options
	 * @return array
	 */
	public function linkAspect( $name, $options = array() )
	{
		$_aspectName = K::standardizeName( $name );

		/** @var $_aspect \Kisma\Components\Aspect */
		$_aspect = new $_aspectName( $options );
		$_aspect->link( $this );

		return $this->_aspects[$_aspectName] = $_aspect;
	}

	/**
	 * Links multiple aspects to this component.
	 * @param array $aspects
	 * @return \Kisma\Components\Component
	 */
	public function linkAspects( $aspects = array() )
	{
		foreach ( $aspects as $_name => $_options )
		{
			$this->linkAspect( $_name, $_options );
		}

		return $this;
	}

	/**
	 * Unlinks all aspects from this component.
	 * @return \Kisma\Components\Component
	 */
	public function unlinkAspects()
	{
		foreach ( $this->_aspects as $_name => $_aspect )
		{
			$this->unlinkAspect( $_name );
			unset( $this->_aspects[$_name] );
		}

		//	Make a fresh array
		$this->_aspects = array();
		return $this;
	}

	/**
	 * UnDetaches a aspect from the component.
	 */

	/**
	 * Unlinks an aspect from this component
	 * @param string $name
	 * @return bool
	 * @see Aspect
	 */
	public function unlinkAspect( $name )
	{
		$_aspectName = K::standardizeName( $name );

		if ( isset( $this->_aspects[$_aspectName] ) )
		{
			$this->_aspects[$_aspectName]->unlink( $this );
			unset( $this->_aspects[$_aspectName] );
			return true;
		}

		return false;
	}

	/**
	 * Enables all aspects linked to this component.
	 * @param bool $disable If true, all aspects will be disabled
	 * @return \Kisma\Components\Component
	 * @see Aspect
	 */
	public function enableAspects( $disable = false )
	{
		foreach ( $this->_aspects as $_aspect )
		{
			$_aspect->enable( $disable );
		}

		return $this;
	}

	/**
	 * Enables a single aspect by name
	 * @param string $aspectName
	 * @param bool $disable
	 * @return \Kisma\Components\Component
	 */
	public function enableAspect( $aspectName, $disable = false )
	{
		$_aspectName = K::standardizeName( $aspectName );

		if ( isset( $this->_aspects[$_aspectName] ) )
		{
			$this->_aspects[$_aspectName]->enable( $disable );
		}

		return $this;
	}

	//*************************************************************************
	//* Private Methods 
	//*************************************************************************

	/**
	 * Loads an array into properties if they exist.
	 * @param array $options
	 * @param bool $noMerge If true, this object's options will be cleared first
	 * @return $this
	 */
	protected function _loadConfiguration( $options = array(), $noMerge = false )
	{
		$_options = array();

		//	Loop through, set...
		foreach ( $options as $_key => $_value )
		{
			try
			{
				K::__property( $this, $_key, \Kisma\AccessorMode::Set, $_value );
			}
			catch ( \Kisma\UndefinedPropertyException $_ex )
			{
				//	Undefined, add to options...
				$_options[$_key] = $_value;
			}
		}

		if ( $noMerge )
		{
			//	Overwrite the options...
			$this->_options = $_options;
		}
		else
		{
			//	Merge the options...
			$this->_options = array_merge(
				$this->_options,
				$_options
			);
		}
	}

	//*************************************************************************
	//* Interface Methods
	//*************************************************************************

	/**
	 * Required by Countable interface
	 * @return int
	 */
	public function count()
	{
		return $this->_count;
	}

	/**
	 * Required by Iterator interface
	 * @return mixed
	 */
	public function current()
	{
		$this->_skipNext = false;
		return current( $this->_options );
	}

	/**
	 * Required by Iterator interface
	 * @return mixed
	 */
	public function key()
	{
		return key( $this->_options );
	}

	/**
	 * Required by Iterator interface
	 */
	public function next()
	{
		if ( $this->_skipNext )
		{
			$this->_skipNext = false;
			return;
		}

		next( $this->_options );

		$this->_index++;
	}

	/**
	 * Required by Iterator interface
	 */
	public function rewind()
	{
		$this->_skipNext = false;
		reset( $this->_options );
		$this->_index = 0;
	}

	/**
	 * Required by Iterator interface
	 * @return boolean
	 */
	public function valid()
	{
		return ( $this->_index < $this->_count );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param $aspects
	 * @return \Kisma\Components\Component
	 */
	public function setAspects( $aspects )
	{
		$this->_aspects = $aspects;
		return $this;
	}

	/**
	 * @return array|\Kisma\Components\Aspect[]
	 */
	public function getAspects()
	{
		return $this->_aspects;
	}

	/**
	 * @param $errors
	 * @return \Kisma\Components\Component
	 */
	public function setErrors( $errors )
	{
		$this->_errors = $errors;
		return $this;
	}

	/**
	 * @return array|\Kisma\Components\Exception[]
	 */
	public function getErrors()
	{
		return $this->_errors;
	}

	/**
	 * @param $events
	 * @return \Kisma\Components\Component
	 */
	public function setEvents( $events )
	{
		$this->_events = $events;
		return $this;
	}

	/**
	 * @return array|\Kisma\Components\Event[]
	 */
	public function getEvents()
	{
		return $this->_events;
	}

	/**
	 * @param int $index
	 * @return \Kisma\Components\Component $this
	 */
	public function setIndex( $index )
	{
		$this->_index = $index;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getIndex()
	{
		return $this->_index;
	}

	/**
	 * @param $logging
	 *
	 * @internal param bool|int $loggingFlag
	 * @return \Kisma\Components\Component $this
	 */
	public function setLogging( $logging )
	{
		$this->_logging = $logging;
		return $this;
	}

	/**
	 * @return bool|int
	 */
	public function getLogging()
	{
		return $this->_logging;
	}

	/**
	 * @param int $count
	 * @return \Kisma\Components\Component $this
	 */
	protected function _setCount( $count )
	{
		$this->_count = $count;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->_count;
	}

	/**
	 * sets all options at once
	 * @param array $options
	 * @return \Kisma\Components\Component $this
	 */
	public function setOptions( $options = array() )
	{
		//	Check each property
		foreach ( $options as $_key => $_value )
		{
			$this->setOption( $_key, $_value );
		}

		return $this;
	}

	/**
	 * @param string $name
	 * @param mixed|null $value
	 * @return mixed
	 */
	public function setOption( $name, $value = null )
	{
		$this->_options[$name] = $value;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 * @param string $name
	 * @param mixed|null $defaultValue
	 * @param bool $deleteAfter If true, key is removed from the option list after it is read.
	 * @return mixed
	 */
	public function getOption( $name, $defaultValue = null, $deleteAfter = false )
	{
		return K::o( $this->_options, $name, $defaultValue, $deleteAfter );
	}

	/**
	 * @param boolean $readOnly
	 * @return \Kisma\Components\Component $this
	 */
	public function setReadOnly( $readOnly )
	{
		$this->_readOnly = $readOnly;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getReadOnly()
	{
		return $this->_readOnly;
	}

	/**
	 * @param boolean $skipNext
	 * @return \Kisma\Components\Component $this
	 */
	public function setSkipNext( $skipNext )
	{
		$this->_skipNext = $skipNext;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getSkipNext()
	{
		return $this->_skipNext;
	}

	/**
	 * @param string $eventHandlerSignature
	 * @return \Kisma\Components\Component $this
	 */
	public function setEventHandlerSignature( $eventHandlerSignature )
	{
		$this->_eventHandlerSignature = $eventHandlerSignature;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEventHandlerSignature()
	{
		return $this->_eventHandlerSignature;
	}

}
