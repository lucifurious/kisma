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
 * @throws CException|xlEventException|xlInvalidOptionException
 */
class Component extends Kisma implements IKisma, IStreamable, IOptions, \Countable, \Iterator
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/***
	 * @var array This object's options
	 */
	protected $_options = array();
	/**
	 * @var Event[] This component's events
	 */
	protected $_events = array();
	/**
	 * @var Aspect[] This object's mixins
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

	//*************************************************************************
	//* Public Methods
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
		$this->bindEvents( k::o( $options, 'eventOptions' ) );

		//	Trigger our afterConstruct event
		$this->triggerEvent( 'afterConstruct' );

		//	Set our count...
		$this->_optionCount = count( $this->_options );
	}

	/**
	 * @param $eventName
	 * @param null $eventData
	 * @return bool
	 */
	public function triggerEvent( $eventName, $eventData = null )
	{
		foreach ( $this->_events as $_event )
		{
			if ( strtolower( $eventName ) == $_event->getEventName() )
			{
				if ( false === $_event->trigger( $eventData ) )
				{
					return false;
				}
			}
		}
	}

	/**
	 * Given a property name, clean it up to a standard, camel-cased, format.
	 *
	 * @param string $propertyName
	 * @return string
	 */
	public static function cleanPropertyName( $propertyName )
	{
		return str_replace( ' ', null, ucwords( trim( str_replace( '_', ' ', $propertyName ) ) ) );
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * @static
	 * @throws PropertyException
	 * @param $object
	 * @param $propertyName
	 * @param int|\Kisma\AccessorMode $access
	 * @param null $value
	 * @return void
	 */
	protected static function _aspectProperty( $object, $propertyName, \Kisma\AccessorMode $access = \Kisma\AccessorMode::Get, $value = null )
	{
		foreach ( $object->getAspects() as $_aspect )
		{
			try
			{
				return self::_checkProperty( $object, $propertyName, $access, $value );
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
	 * @param $object
	 * @param $propertyName
	 * @param int|\Kisma\AccessorMode $access
	 * @param null $value
	 * @return bool
	 */
	protected static function _property( $object, $propertyName, \Kisma\AccessorMode $access = \Kisma\AccessorMode::Get, $value = null )
	{
		$propertyName = self::cleanPropertyName( $propertyName );

		$_getter = 'get' . $propertyName;
		$_setter = 'set' . $propertyName;

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
					throw new WriteOnlyPropertyException( 'The property "' . $propertyName . '" is write-only.', \Kisma\AccessorMode::WriteOnly );
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
					throw new ReadOnlyPropertyException( 'The property "' . $propertyName . '" is read-only.', \Kisma\AccessorMode::ReadOnly );
				}
				break;
		}

		//	Everything falls through to undefined
		throw new UndefinedPropertyException( 'The property "' . $propertyName . '" is undefined.', \Kisma\AccessorMode::Undefined );
	}

	//********************************************************************************
	//* Interface Requirements
	//********************************************************************************

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
	//* Magic Overrides 
	//*************************************************************************

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
	 * @param $method
	 * @param $arguments
	 * @return mixed
	 */
	public function __call( $method, $arguments )
	{
		//	Call behavior methods if they exist
		foreach ( $this->_aspects as $_aspect )
		{
			if ( method_exists( $_aspect, $method ) )
			{
				return call_user_func_array( array( $_aspect, $method ), $arguments );
			}
		}

		if ( is_callable( array( $this, $method ) ) )
		{
			return call_user_func_array( $this->{$method}, $arguments );
		}

		throw \BadMethodCallException( get_called_class() . '::' . $method . ' is undefined.' );
	}

	/**
	 * Attaches a list of behaviors to the component. Each behavior is indexed by its name and should be an instance of {@link xlIBehavior},
	 * @param array $behaviors list of behaviors to be attached to the component
	 * @return \Options
	 */
	public function addAspects( $aspects = array() )
	{
		foreach ( $aspects as $_name => $_aspect )
		{
			$this->attachBehavior( $_name, $_aspect );
		}
		
		return $this;
	}

	/**
	 * Detaches all behaviors from this component.
	 * @return \Options
	 */
	public function detachBehaviors()
	{
		if ( ! empty( $this->_aspects ) )
		{
			foreach ( $this->_aspects as $_name => $_aspect )
			{
				$this->detachBehavior( $_name );
			}

			unset( $this->_aspects );

			$this->_aspects = array();
		}

		return $this;
	}

	/**
	 * Attaches a behavior to this component.
	 * @param string $propertyName the behavior's name. It should uniquely identify this behavior.
	 * @param xlIBehavior|array $behaviorConfig
	 * @return xlIBehavior the behavior object
	 */
	public function attachBehavior( $propertyName, $behaviorConfig )
	{
		/** @var $_aspect xlIBehavior */
		$_aspect = $behaviorConfig;

		if ( is_array( $behaviorConfig ) )
		{
			$_aspect = xl::createComponent( $behaviorConfig );
		}

		$_aspect->setEnabled( true );
		$_aspect->attach( $this );

		return $this->_aspects[$propertyName] = $_aspect;
	}

	/**
	 * Detaches a behavior from the component.
	 * The behavior's {@link xlIBehavior::detach} method will be invoked.
	 * @param string $propertyName the behavior's name. It uniquely identifies the behavior.
	 * @return xlIBehavior the detached behavior. Null if the behavior does not exist.
	 */
	public function detachBehavior( $propertyName )
	{
		/** @var $_aspect xlIBehavior */
		if ( null !== ( $_aspect = xl::o( $this->_aspects, $propertyName ) ) )
		{
			$_aspect->detach( $this );
			unset( $this->_aspects[$propertyName] );
		}

		return $_aspect;
	}

	/**
	 * Enables all behaviors attached to this component.
	 */
	public function enableBehaviors()
	{
		if ( $this->_aspects !== null )
		{
			foreach ( $this->_aspects as $behavior )
			{
				$behavior->setEnabled( true );
			}
		}
	}

	/**
	 * Disables all behaviors attached to this component.
	 */
	public function disableBehaviors()
	{
		if ( ! empty( $this->_aspects ) )
		{
			foreach ( $this->_aspects as $_aspect )
			{
				$_aspect->setEnabled( false );
			}
		}
	}

	/**
	 * Enables a single behavior
	 * @param string $propertyName
	 */
	public function enableBehavior( $propertyName )
	{
		if ( isset( $this->_aspects[$propertyName] ) )
		{
			$this->_aspects[$propertyName]->setEnabled( true );
		}
	}

	/**
	 * Disables an attached behavior.
	 * @param string $propertyName
	 */
	public function disableBehavior( $propertyName )
	{
		if ( isset( $this->_aspects[$propertyName] ) )
		{
			$this->_aspects[$propertyName]->setEnabled( false );
		}
	}

	/**
	 * Determines whether a property is defined.
	 * A property is defined if there is an accessible getter or setter method
	 * defined in the class.
	 * Note: property names are case-insensitive.
	 * @param string $propertyName the property name
	 * @return boolean whether the property is defined
	 */
	public function hasProperty( $propertyName )
	{
		return $this->canGetProperty( $propertyName ) || $this->canSetProperty( $propertyName );
	}

	/**
	 * Determines whether a property can be read.
	 * A property can be read if the class has a getter method
	 * for the property name. Note, property name is case-insensitive.
	 * @param string $propertyName the property name
	 * @return boolean whether the property can be read
	 * @see canSetProperty
	 */
	public function canGetProperty( $propertyName )
	{
		return method_exists( $this, 'get' . $propertyName );
	}

	/**
	 * Determines whether a property can be set.
	 * A property can be written if the class has a setter method
	 * for the property name. Note, property name is case-insensitive.
	 * @param string $propertyName the property name
	 * @return boolean whether the property can be written
	 * @see canGetProperty
	 */
	public function canSetProperty( $propertyName )
	{
		return method_exists( $this, 'set' . $propertyName );
	}

	/**
	 * Determines whether an event is defined.
	 * Note, event name is case-insensitive.
	 * @param string $propertyName the event name
	 * @return boolean whether an event is defined
	 */
	public function hasEvent( $propertyName )
	{
		return !strncasecmp( $propertyName, 'on', 2 ) && method_exists( $this, $propertyName );
	}

	/**
	 * Checks whether the named event has attached handlers.
	 * @param string $propertyName the event name
	 * @return boolean whether an event has been attached one or several handlers
	 */
	public function hasEventHandler( $propertyName )
	{
		$propertyName = trim( strtolower( $propertyName ) );
		return isset( $this->_events[$propertyName] ) && ! empty( $this->_events[$propertyName] );
	}

	/**
	 * Returns the list of attached event handlers for an event.
	 * @param string $propertyName the event name
	 * @return ArrayObject list of attached event handlers for the event
	 * @throws CException if the event is not defined
	 */
	public function &getEventHandlers( $propertyName )
	{
		$propertyName = trim( strtolower( $propertyName ) );

		if ( $this->hasEvent( $propertyName ) )
		{
			if ( ! isset( $this->_events[$propertyName] ) )
			{
				$this->_events[$propertyName] = new ArrayObject();
			}

			return $this->_events[$propertyName];
		}

		throw new xlEventException( 'Event "' . get_called_class( $this ) . '.' . $propertyName . '" is not defined.' );
	}

	/**
	 * Attaches an event handler to an existing event.
	 * An event handler must be a valid PHP callback, i.e., is_callable returns true.
	 * @param string $eventName
	 * @param callback|Closure $eventHandler
	 * @throws xlEventException if the event is not defined
	 * @see detachEventHandler
	 */
	public function attachEventHandler( $eventName, $eventHandler )
	{
		$this->getEventHandlers( $eventName )->append( $eventHandler );
	}

	/**
	 * Detaches an existing event handler.
	 * This method is the opposite of {@link attachEventHandler}.
	 * @param $eventName
	 * @param $eventHandler
	 *
	 * @internal param string $propertyName event name
	 *
	 * @internal param callback $handler the event handler to be removed
	 * @return boolean if the detachment process is successful
	 * @see attachEventHandler
	 */
	public function detachEventHandler( $eventName, $eventHandler )
	{
		$_handlers = $this->getEventHandlers( $eventName );

		if ( $_handlers->offsetExists( $eventHandler ) )
		{
			$_handlers->offsetUnset( $eventHandler );
			return true;
		}

		return false;
	}

	/**
	 * Raises an event.
	 * @param $eventName
	 * @param xlEvent $event the event parameter
	 * @return bool
	 * @throws xlEventException if the event is undefined or an event handler is invalid.
	 */
	public function raiseEvent( $eventName, $event = null )
	{
		$eventName = trim( strtolower( $eventName ) );

		if ( isset( $this->_events[$eventName] ) )
		{
			if ( null === $event )
			{
				$event = new xlEvent( $this );
			}

			foreach ( $this->_events[$eventName] as $_handler )
			{
				if ( is_callable( $_handler ) )
				{
					call_user_func( $_handler, $event );

					if ( $event instanceof xlEvent && false !== $event->getHandled() )
					{
						return true;
					}

					continue;
				}

				throw new xlEventException(
					sprintf(
						'Event "%s.%s" event handler is not callable.',
						get_class( $this ),
						$eventName
					)
				);
			}
		}
		else if ( ! $this->hasEvent( $eventName ) )
		{
			throw new xlEventException(
				sprintf(
					'Event "%s.%s" is undefined.',
					get_class( $this ),
					$eventName
				)
			);
		}

		return false;
	}

	/**
	 * Evaluates a PHP expression or callback under the context of this component.
	 *
	 * Valid PHP callback can be class method name in the form of
	 * array(ClassName/Object, MethodName), or anonymous function (only available in PHP 5.3.0 or above).
	 *
	 * If a PHP callback is used, the corresponding function/method signature should be
	 * <pre>
	 * function foo($param1, $param2, ..., $component) { ... }
	 * </pre>
	 * where the array elements in the second parameter to this method will be passed
	 * to the callback as $param1, $param2, ...; and the last parameter will be the component itself.
	 *
	 * If a PHP expression is used, the second parameter will be "extracted" into PHP variables
	 * that can be directly accessed in the expression. See {@link http://us.php.net/manual/en/function.extract.php PHP extract}
	 * for more details. In the expression, the component object can be accessed using $this.
	 *
	 * @param mixed $expression_ a PHP expression or PHP callback to be evaluated.
	 * @param array $expressionData_ additional parameters to be passed to the above expression/callback.
	 * @return mixed the expression result
	 * @since 1.1.0
	 */
	public function evaluateExpression( $expression_, $expressionData_ = array() )
	{
		if ( is_string( $expression_ ) )
		{
			extract( $expressionData_ );
			return eval( 'return ' . $expression_ . ';' );
		}

		$expressionData_[] = $this;
		return call_user_func_array( $expression_, $expressionData_ );
	}

	//********************************************************************************
	//* Private Methods
	//********************************************************************************

	/**
	 * If any methods exist in this class that appear to be event handlers, they will be
	 * automatically attached to said events.
	 * @return void
	 */
	protected function _autoAttachEventHandlers()
	{
		$_this = new ReflectionClass( get_called_class( $this ) );

		if ( null !== $_this )
		{
			//	Only public/protected methods are checked...
			$_methodList = $_this->getMethods( ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED );

			/** @var $_method ReflectionMethod */
			foreach ( $_methodList as $_method )
			{
				$_name = $_method->name;

				//	Event handler?
				if ( 0 === strncasecmp( $_name, 'on', 2 ) && method_exists( $this, substr( $_name, 2 ) ) )
				{
					$this->attachEventHandler( $_name, array( $this, substr( $_name, 2 ) ) );
				}
			}
		}
	}

	/**
	 * Loads an array into properties if they exist.
	 * @param array $options
	 */
	protected function _loadConfiguration( $options = array() )
	{
		//	Make a copy for posterity
		$this->_options = $options;

		foreach ( $options as $_option => $_value )
		{
			try
			{
				$_setter = 'set' . $_option;

				if ( method_exists( $this, $_setter ) )
				{
					$this->{$_setter}( $_value );
				}
				else
				{
					throw new xlInvalidOptionException( 'Option "' . $_option . '" is either read-only or non-existent.' );
				}
			}
			catch ( Exception $_ex )
			{
				$this->pushException( $_ex );
			}
		}
	}

	/**
	 * Down and dirty logger
	 * @param string $message
	 * @param string $destination The file name to output to
	 */
	protected function _rawLog( $message, $destination = null )
	{
		if ( $this->_enableLogging )
		{
			if ( null === $destination )
			{
				$destination = './' . strtolower( get_class() ) . '.php.raw.log';
			}

			error_log( date( 'Y-m-d H:i:s' ) . ' :: ' . $message . PHP_EOL, 3, $destination );
		}
	}

	/**
	 * Retrieves the next exception off the stack
	 * @return Exception
	 */
	public function popException()
	{
		if ( $this->_errors instanceof SplStack )
		{
			return $this->_errors->pop();
		}

		return array_pop( $this->_errors );
	}

	/**
	 * @param Exception $exception
	 * @param bool $logToError
	 * @return \Options $this
	 */
	public function pushException( $exception, $logToError = false )
	{
		if ( $this->_errors instanceof SplStack )
		{
			$this->_errors->push( $exception );
		}
		else
		{
			array_push( $this->_errors, $exception );
		}

		if ( $logToError )
		{
			xlLog::error( 'Exception: ' . $exception->getMessage() );
		}

		return $this;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param array $events
	 * @return \Options
	 */
	protected function _setEvents( $events = array() )
	{
		$this->_events = new ArrayObject( $events );
		return $this;
	}

	/**
	 * @return array
	 */
	public function getEvents()
	{
		return $this->_events;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 * Retrieves named value from the option array
	 * @param string|integer $which The key of the value you want to retrieve
	 * @param mixed $defaultValue The default value to return if the option does not exist. Defaults to null
	 * @param boolean $unsetValue If true, the value at $which will be unset
	 * @return mixed The value at $which or the $defaultValue specified
	 */
	public function getOption( $which, $defaultValue = null, $unsetValue = false )
	{
		return xl::o( $this->_options, $which, $defaultValue, $unsetValue );
	}

	/**
	 * Similar to {@link SPComponent::getOption} except it will pull a value from a nested array.
	 * @param integer|string $key
	 * @param integer|string $subKey
	 * @param mixed $defaultValue
	 * @param boolean $unsetValue
	 * @return mixed
	 */
	public function getSubOption( $key = null, $subKey = null, $defaultValue = null, $unsetValue = false )
	{
		return xl::oo( $this->_options, $key, $subKey, $defaultValue, $unsetValue );
	}

	/**
	 * @return bool
	 */
	public function getAutoAttachEvents()
	{
		return $this->_autoAttachEvents;
	}

	/**
	 * @param $value
	 * @return \Options
	 */
	public function setAutoAttachEvents( $value )
	{
		$this->_autoAttachEvents = $value;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function getEnableLogging()
	{
		return $this->_enableLogging;
	}

	/**
	 * @param $value
	 * @return \Options
	 */
	public function setEnableLogging( $value )
	{
		$this->_enableLogging = $value;
		return $this;
	}

	/**
	 * @return array|\SplStack
	 */
	public function getExceptionStack()
	{
		return $this->_errors;
	}

	/**
	 * @param $triggers
	 * @return \Options
	 */
	protected function _setTriggers( $triggers )
	{
		$this->_triggers = $triggers;
		return $this;
	}

	/**
	 * @return array|\Closure[]|null
	 */
	public function getTriggers()
	{
		return $this->_triggers;
	}

	public function setAspects( $aspects )
	{
		$this->_aspects = $aspects;
		return $this;
	}

	public function getAspects()
	{
		return $this->_aspects;
	}

	public function setErrors( $errors )
	{
		$this->_errors = $errors;
		return $this;
	}

	public function getErrors()
	{
		return $this->_errors;
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
}


/**
 *
 */
class kCOptions extends \kCObject implements \kIStreamable, \Countable, \Iterator
{
	//********************************************************************************
	//* Constants
	//********************************************************************************
	
	//********************************************************************************
	//* Member Variables
	//********************************************************************************

	//********************************************************************************
	//* Public Methods
	//********************************************************************************

	/**
	 * Constructor
	 * @param array $options
	 * @param boolean $readOnly
	 */
	public function __construct( array $options = array(), $readOnly = true )
	{
		//	Do not pass $options to parent, we are hijacking...
		parent::__construct();

		$this->_readOnly = $readOnly;
		$this->_iterationIndex = 0;

		//	Now process...
		foreach ( $this->_options as $_key => $_value )
		{
			if ( is_array( $_value ) )
				$this->_options[$_key] = new self( $_value, $readOnly );
			else
				$this->_options[$_key] = $_value;
		}

		//	Set our count...
		$this->_optionCount = count( $this->_options );
	}

	/**
	 * Retrieve a value from the options collection
	 * @param string $key
	 * @param mixed $defaultValue
	 * @param bool $unsetAfter If true, option will be removed after it is returned.
	 * @return mixed
	 */
	public function getOption( $key, $defaultValue = null, $unsetAfter = false )
	{
		return XL::o( $this->_options, $key, $defaultValue, $unsetAfter );
	}

	/**
	 * Return an associative array of all settings
	 * @return array
	 */
	public function toArray()
	{
		/** @var kOptions $_value */
		$_options = array();
		
		foreach ( $this->_options as $_key => $_value )
		{
			if ( $_value instanceof kOptions )
				$_options[$_key] = $_value->toArray();
			else
				$_options[$_key] = $_value;
		}
		
		return $_options;
	}

	/**
	 * Merge another kOptions with this one. The items in $mergeOptions will
	 * override the same named items in the current collection.
	 * @param kOptions $mergeConfig
	 * @return kOptions
	 */
	public function merge( kOptions $mergeConfig )
	{
		foreach ( $mergeConfig as $_key => $_option )
		{
			if ( array_key_exists( $_key, $this->_options ) )
			{
				if ( $_option instanceof kOptions && $this->{$_key} instanceof kOptions )
					$this->{$_key} = $this->{$_key}->merge( new kOptions( $_option->toArray(), $this->_readOnly ) );
				else
					$this->{$_key} = $_option;
			}
			else
			{
				/** @var kOptions $_option */
				if ( $_option instanceof kOptions )
					$this->$_key = new kOptions( $_option->toArray(), $this->_readOnly );
				else
					$this->$_key = $_option;
			}
		}

		return $this;
	}

	/**
	 * Returns the JSON representation of a value
	 * @link http://php.net/manual/en/function.json-encode.php
	 * @param int $jsonOptions Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS,
	 * JSON_FORCE_OBJECT.
	 * @return string a JSON encoded string on success.
	 */
	public function jsonEncode( $jsonOptions = null )
	{
		return json_encode( $this->toArray(), $jsonOptions );
	}
	
	/**
	 * Decodes a JSON string
	 * @link http://php.net/manual/en/function.json-decode.php
	 * @param string $json <p>
	 * The json string being decoded.
	 * </p>
	 * @param bool $assoc [optional] <p>
	 * When true, returned objects will be converted into
	 * associative arrays.
	 * </p>
	 * @param int $depth [optional] <p>
	 * User specified recursion depth.
	 * </p>
	 * @return mixed the value encoded in json in appropriate
	 * PHP type. Values true, false and
	 * null (case-insensitive) are returned as true, false
	 * and &null; respectively. &null; is returned if the
	 * json cannot be decoded or if the encoded
	 * data is deeper than the recursion limit.
	 */
	public function jsonDecode( $json, $assoc = null, $depth = null )
	{
		return json_decode( $json, $assoc, $depth );
	}
	
	//********************************************************************************
	//* Magic Method Override
	//********************************************************************************

	/**
	 * Check our options before we pass the buck
	 * @param string $key
	 * @return mixed
	 */
	public function __get( $key )
	{
		if ( array_key_exists( $key, $this->_options ) )
			return $this->getOption( $key );
	
		return parent::__get( $key );
	}

	/**
	 * Only allow setting of a property if not read-only.
	 * @param string $key
	 * @param mixed $value
	 * @throws xlReadOnlyException
	 * @return kOptions
	 */
	public function __set( $key, $value )
	{
		if ( ! array_key_exists( $key, $this->_options ) )
			return parent::__set( $key, $value );
		
		if ( $this->_readOnly )
		{
			throw new xlReadOnlyException(
				XL::t(
					self::CLASS_LOG_TAG,
					'Property "{class}.{property}" is read only.',
					array(
						'{class}' => get_class( $this ),
						'{property}' => $key,
					)
				)
			);
		}

		if ( is_array( $value ) )
			$this->_options[$key] = new self( $value, $this->_readOnly );
		else
			$this->_options[$key] = $value;

		$this->_optionCount = count( $this->_options );
		
		return $this;
	}

	/**
	 * Deep copy of this collection.
	 * @return void
	 */
	public function __clone()
	{
		$_options = array();
		
		foreach ( $this->_options as $_key => $_value )
		{
			if ( $_value instanceof kOptions )
				$_options[$_key] = clone $_value;
			else
				$_options[$_key] = $_value;
		}

		$this->_options = $_options;
	}

	/**
	 * @param string $key
	 * @return boolean
	 */
	public function __isset( $key )
	{
		if ( ! array_key_exists( $key, $this->_options ) )
			return parent::__isset( $key );
		
		return isset( $this->_options[$key] );
	}

	/**
	 * @param  string $key
	 * @throws xlReadOnlyException
	 * @return kOptions
	 */
	public function __unset( $key )
	{
		if ( $this->_readOnly )
		{
			throw new xlReadOnlyException(
				XL::t(
					self::CLASS_LOG_TAG,
					'Property "{class}.{property}" is read only.',
					array(
						'{class}' => get_class( $this ),
						'{property}' => $key,
					)
				)
			);
		}
			
		XL::uo( $this->_options, $key );
		$this->_optionCount = count( $this->_options );
		$this->_skipNext = true;
		
		//	Never break the chain
		return $this;
	}

	//********************************************************************************
	//* Property Accessors
	//********************************************************************************

	/**
	 * Set the read-only flag
	 * @param bool $value
	 * @return kOptions
	 */
	public function setReadOnly( $value = true )
	{
		$this->_readOnly = $value;

		foreach ( $this->_options as $_key => $_value )
		{
			/** @var kOptions $_value */
			if ( $_value instanceof kOptions )
				$_value->setReadOnly( $value );
		}
		
		return $this;
	}

}