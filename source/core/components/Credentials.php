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
namespace Kisma\Components;
/**
 *
 */
class Component extends \Kisma\Kisma implements \Kisma\IKisma, \Kisma\IAspectable, \Kisma\IOptions, \Countable, \Iterator
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
	protected $_iterationIndex = 0;
	/**
	 * @var integer Holds the number of settings we have
	 */
	protected $_optionCount = 0;
	/**
	 * @var boolean Used when unsetting values during iteration to ensure we do not skip the next element
	 */
	protected $_skipNext = false;
	/**
	 * @var boolean If true, configuration settings cannot be changed once loaded
	 */
	protected $_readOnly = true;
	/**
	 * @var bool|int The logging flags for this object
	 */
	protected $_loggingFlag = true;
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
	 */
	public function __construct( $options = array() )
	{
		//	Create our arrays
		$this->_errors = array();
		$this->_options = array();
		$this->_events = array();
		$this->_aspects = array();

		//	If an option list is specified, try to assign values to properties automatically
		if ( !empty( $options ) )
		{
			$this->setOptions( $options );
		}

		//	Auto-bind events
		$this->bindEvents( K::o( $options, 'eventOptions' ) );

		//	Trigger our afterConstruct event
		$this->trigger( 'after_constructor' );

		//	Set our count...
		$this->_optionCount = count( $this->_options );
	}

	/**
	 * Returns the value of a property via its "getter". If a "getter" does not
	 * exist for a property, an exception will be thrown.
	 *
	 * A property may exist in this object or in any aspect assigned to this
	 * object as well.
	 *
	 * @param string $propertyName The property name
	 * @return mixed The property value
	 * @throws UndefinedPropertyException If the property is not defined
	 */
	public function __get( $propertyName )
	{
		return self::_checkProperty( $this, $propertyName );
	}

	/**
	 * Sets the value of a property via its "setter". If a "setter" does not
	 * exist for a property, an exception will be thrown.
	 *
	 * A property may exist in this object or in any aspect assigned to this
	 * object as well.
	 *
	 * @param string $propertyName The property name
	 * @param mixed $value The property value
	 * @return mixed
	 */
	public function __set( $propertyName, $value )
	{
		return self::_checkProperty( $this, $propertyName, \Kisma\AccessorMode::Set, $value );
	}

	/**
	 * Checks if a property value is null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using isset() to detect if a component property is set or not.
	 * @param string $propertyName the property name or the event name
	 * @return bool
	 * @since 1.0.1
	 */
	public function __isset( $propertyName )
	{
		return self::_checkProperty( $this, $propertyName, \Kisma\AccessorMode::Has );
	}

	/**
	 * Set a property to null
	 * @param string $propertyName the property name or the event name
	 * @return void
	 */
	public function __unset( $propertyName )
	{
		self::__set( $propertyName, null );
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
		if ( is_callable( array( $this, $method ) ) )
		{
			return call_user_func_array( array( $this, $method ), $arguments );
		}

		//	Call aspect methods if they exist
		foreach ( $this->_aspects as $_aspect )
		{
			if ( method_exists( $_aspect, $method ) )
			{
				return call_user_func_array( array( $_aspect, $method ), $arguments );
			}
		}

		throw new \BadMethodCallException( get_called_class() . '::' . $method . ' is undefined.' );
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
			$ignore = self::standardizeName( $ignore );
		});

		foreach ( $_this->getMethods() as $_method )
		{
			$_methodName = $_method['name'];

			if ( $this->_eventHandlerSignature == strtolower( substr( $_methodName, 0, strlen( $this->_eventHandlerSignature ) ) ) )
			{
				$_eventName = self::standardizeName( substr( $_method['name'], strlen( $this->_eventHandlerSignature ) ) );

				if ( !in_array( $_eventName, $_ignores ) )
				{
					$this->bind(
						$_eventName,
						array(
							'autoBound' => true,
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
	public function bind( $name, callback $callback = null )
	{
		//	Make $data optional
		if ( !is_callable( $callback ) )
		{
			throw new InvalidArgumentException( 'The specified event handler is not callable.' );
		}

		//	Standardize the event name
		$_eventName = self::standardizeName( $name );

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
		$_eventName = self::standardizeName( $name );

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
		$_eventName = self::standardizeName( $name );

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
		$_aspectName = self::standardizeName( $name );

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
	public function applyAspects( $aspects = array() )
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
		$_aspectName = self::standardizeName( $name );

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
		$_aspectName = self::standardizeName( $aspectName );

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
	 * Attempts to set a property that lives within an aspect
	 * @static
	 * @throws PropertyException
	 * @param \Kisma\IAspectable $object
	 * @param string $propertyName
	 * @param int|\Kisma\AccessorMode $access
	 * @param mixed|null $value
	 * @return void
	 */
	protected static function _aspectProperty( $object, $propertyName, \Kisma\AccessorMode $access = \Kisma\AccessorMode::Get, $value = null )
	{
		foreach ( $object->getAspects() as $_aspect )
		{
			try
			{
				return self::_checkProperty( $_aspect, $propertyName, $access, $value );
			}
			catch ( UndefinedPropertyException $_ex )
			{
				//	Ignore...
			}
		}
	}

	/**
	 * @static
	 * @throws
	 * @param $object
	 * @param $propertyName
	 * @param int|\Kisma\AccessorMode $access
	 * @param null $value
	 * @return mixed|void
	 */
	protected static function _checkProperty( $object, $propertyName, \Kisma\AccessorMode $access = \Kisma\AccessorMode::Get, $value = null )
	{
		try
		{
			//	Try object first. Squelch undefineds...
			return self::_property( $object, $propertyName, $access, $value );
		}
		//	Ignore undefined properties. Another aspect may have it
		catch ( UndefinedPropertyException $_ex )
		{
			//	Ignored
		}
		catch ( PropertyException $_ex )
		{
			//	Rethrow other property exceptions
			throw $_ex;
		}

		//	Check aspects...
		if ( $object instanceof \Kisma\IAspectable )
		{
			return self::_aspectProperty( $object, $propertyName, $access, $value );
		}

		//	No clue what they're talking about
		throw new UndefinedPropertyException( 'The property "' . $propertyName . '" is undefined.', \Kisma\AccessorMode::Undefined );
	}

	/**
	 * An all-purpose property accessor.
	 *
	 * @static
	 * @throws PropertyException
	 * @param \Kisma\IKisma $object
	 * @param string $propertyName
	 * @param int|\Kisma\AccessorMode $access
	 * @param null $value
	 * @return \Kisma\Components\Component|bool|mixed
	 */
	protected static function _property( \Kisma\IKisma $object, $propertyName, \Kisma\AccessorMode $access = \Kisma\AccessorMode::Get, $value = null )
	{
		$_propertyName = self::standardizeName( $propertyName );

		$_getter = 'get' . $_propertyName;
		$_setter = 'set' . $_propertyName;

		switch ( $access )
		{
			case \Kisma\AccessorMode::Has:
				//	Is it accessible
				return method_exists( $object, $_getter ) || method_exists( $object, $_setter );

			case \Kisma\AccessorMode::Get:
				//	Does a setter exist?
				if ( method_exists( $object, $_getter ) )
				{
					return $object->{$_getter}();
				}

				//	Is it write only?
				if ( method_exists( $object, $_setter ) )
				{
					self::_propertyError( $_propertyName, \Kisma\AccessorMode::WriteOnly );
				}
				break;

			case \Kisma\AccessorMode::Set:
				//	Does a setter exist?
				if ( method_exists( $object, $_setter ) )
				{
					return $object->{$_setter}( $value );
				}

				//	Is it read only?
				if ( !method_exists( $object, $_setter ) && method_exists( $object, $_getter ) )
				{
					self::_propertyError( $_propertyName, \Kisma\AccessorMode::ReadOnly );
				}
				break;
		}

		//	Everything falls through to undefined
		self::_propertyError( $_propertyName, \Kisma\AccessorMode::Undefined );
	}

	/**
	 * A generic property error handler
	 *
	 * @throws UndefinedPropertyException|ReadOnlyPropertyException|WriteOnlyPropertyException
	 * @param string $name
	 * @param int|\Kisma\AccessorMode $type
	 * @return void
	 */
	protected static function _propertyError( $name, \Kisma\AccessorMode $type = \Kisma\AccessorMode::Undefined )
	{
		$_name = self::standardizeName( $name );

		switch ( $type )
		{
			case \Kisma\AccessorMode::ReadOnly:
				$_class = 'ReadOnlyPropertyException';
				$_reason = 'read-only';
				break;

			case \Kisma\AccessorMode::WriteOnly:
				$_class = 'WriteOnlyPropertyException';
				$_reason = 'write-only';
				break;

			default:
				$_class = 'UndefinedPropertyException';
				$_reason = 'undefined';
				break;
		}

		throw new $_class( 'Property "' . get_called_class() . '"."' . $_name . '" is ' . $_reason . '.', $type );
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
		return $this->_optionCount;
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

		$this->_iterationIndex++;
	}

	/**
	 * Required by Iterator interface
	 */
	public function rewind()
	{
		$this->_skipNext = false;
		reset( $this->_options );
		$this->_iterationIndex = 0;
	}

	/**
	 * Required by Iterator interface
	 * @return boolean
	 */
	public function valid()
	{
		return ( $this->_iterationIndex < $this->_optionCount );
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
	 * @param int $iterationIndex
	 * @return \Kisma\Components\Component $this
	 */
	public function setIterationIndex( $iterationIndex )
	{
		$this->_iterationIndex = $iterationIndex;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getIterationIndex()
	{
		return $this->_iterationIndex;
	}

	/**
	 * @param bool|int $loggingFlag
	 * @return \Kisma\Components\Component $this
	 */
	public function setLoggingFlag( $loggingFlag )
	{
		$this->_loggingFlag = $loggingFlag;
		return $this;
	}

	/**
	 * @return bool|int
	 */
	public function getLoggingFlag()
	{
		return $this->_loggingFlag;
	}

	/**
	 * @param int $optionCount
	 * @return \Kisma\Components\Component $this
	 */
	public function setOptionCount( $optionCount )
	{
		$this->_optionCount = $optionCount;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOptionCount()
	{
		return $this->_optionCount;
	}

	/**
	 * @param array $options
	 * @return \Kisma\Components\Component $this
	 */
	public function setOptions( $options )
	{
		$this->_options = $options;
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
