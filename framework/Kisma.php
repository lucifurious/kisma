<?php
/**
 * Kisma
 * The mother of all Kisma(tm) objects
 * @property IKisma $creator
 * @property array|object $eventData
 * @property bool $handled
 */
namespace Kisma;
/**
 * Library Files
 */
require_once __DIR__ . '/enums.php';
require_once __DIR__ . '/exceptions.php';
require_once __DIR__ . '/interfaces.php';
/**
 * Kisma
 * The mother of all Kisma classes
 *
 * Contains a few core functions implemented statically to
 * be lightweight and single instance.
 *
 * @property App $app
 * @property Request $request
 * @property Response $response
 * @property User $user
 * @property AppController $appController
 * @property int $uniqueIdCounter
 * @property array $appParameters
 */
class Kisma
{
	//********************************************************************************
	//* Private Members
	//********************************************************************************

	/**
	 * @var App The current application
	 * @static
	 */
	protected static $_app = null;
	/**
	 * @var AppController The current controller
	 * @static
	 */
	protected static $_appController = null;
	/**
	 * @var array The current application parameters
	 * @static
	 */
	protected static $_appParameters = null;
	/**
	 * @var AppUser The current user
	 * @static
	 */
	protected static $_user = null;
	/**
	 * @var int A static ID counter for generating unique names
	 * @static
	 */
	protected static $_uniqueIdCounter = 1000;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Initialize our private statics
	 * @param array $options
	 * @return bool
	 */
	public static function initialize( $options = array() )
	{
		//	Namespace aliases do not work in CLI mode, so create "K" for all components
		if ( self::isCli() )
		{
			//	Make an alias of Kisma if this is CLI
			class_alias( '\Kisma\Kisma', '\Kisma\Components\K' );
		}

		return true;
	}

	/**
	 * Given a property name, clean it up to a standard, camel-cased, format.
	 * @param string $name
	 * @return string
	 */
	public static function standardizeName( $name )
	{
		return str_replace( ' ', null, ucwords( trim( str_replace( '_', ' ', $name ) ) ) );
	}

	//*************************************************************************
	//* The FauxFactory
	//*************************************************************************

	/**
	 * Constructs a unique name based on component, hashes by default
	 * @param IComponent $component
	 * @param boolean $humanReadable If true, names returned will not be hashed
	 * @return string
	 */
	public static function createUniqueName( IComponent $component, $humanReadable = false )
	{
		$_name = get_class( $component ) . '.' . self::$_uniqueIdCounter++;
		return 'kisma.' . ( $humanReadable ? $_name : \Kisma\Helpers\Hash::hash( $_name ) );
	}

	/**
	 * @static
	 * @param \Kisma\Components\Component $component
	 * @param string $aspectClass
	 * @param array $options
	 * @return \Kisma\Components\Aspect
	 */
	public static function createAspect( $component, $aspectClass, $options = array() )
	{
		/** @var \Kisma\Components\Aspect $_aspect */
		$_aspect = new $aspectClass( $options );
		$_aspect->link( $component );

		return $_aspect;
	}

	/**
	 * Takes a list of things and returns them in an array as the values. Keys are maintained.
	 * @param mixed $_ [optional]
	 * @return array
	 */
	public static function createArray( &$_ = null )
	{
		$_array = array();

		foreach ( func_get_args() as $_key => $_argument )
		{
			$_array[$_key] = $_argument;
		}

		//	Return the fresh array...
		return $_array;
	}

	//*************************************************************************
	//* Utility Methods
	//*************************************************************************

	/**
	 * NVL = Null VaLue. Copycat function from PL/SQL. Pass in a list of arguments and the first non-null
	 * item is returned. Good for setting default values, etc. Last non-null value in list becomes the
	 * new "default value".
	 * NOTE: Since PHP evaluates the arguments before calling a function, this is NOT a short-circuit method.
	 * @param mixed $_ [optional]
	 * @return mixed
	 */
	public static function nvl( &$_ = null )
	{
		$_defaultValue = null;

		foreach ( func_get_args() as $_argument )
		{
			if ( null === $_argument )
			{
				return $_argument;
			}

			$_defaultValue = $_argument;
		}

		return $_defaultValue;
	}

	/**
	 * Convenience "in_array" method. Takes variable args.
	 * The first argument is the needle, the rest are considered in the haystack. For example:
	 * Kisma::in( 'x', 'x', 'y', 'z' ) returns true
	 * Kisma::in( 'a', 'x', 'y', 'z' ) returns false
	 * @param mixed $_ [optional]
	 * @return boolean
	 */
	public static function in( &$_ = null )
	{
		//	Clever or dumb? Dunno...
		return in_array( array_shift( func_get_args() ), func_get_args() );
	}

	/**
	 * Shortcut for str(i)pos
	 * @static
	 * @param string $haystack
	 * @param string $needle
	 * @param bool $caseSensitive If true, search will be case-sensitive
	 * @param int $offset
	 * @return bool
	 */
	public static function within( $haystack, $needle, $caseSensitive = false, $offset = 0 )
	{
		if ( false === $caseSensitive )
		{
			//	Case-insensitive
			return false !== stripos( $haystack, $needle, $offset );
		}

		//	Case-sensitive
		return false !== strpos( $haystack, $needle, $offset );
	}

	/**
	 * Takes the arguments and makes a file path out of them. No leading or trailing
	 * separator is added.
	 * @param mixed $_ [optional]
	 * @return string
	 */
	public static function makePath( &$_ = null )
	{
		return implode( DIRECTORY_SEPARATOR, func_get_args() );
	}

	/**
	 * Determine if PHP is running CLI mode or not
	 * @return boolean True if currently running in CLI
	 */
	public static function isCli()
	{
		return ( 'cli' == php_sapi_name() && empty( $_SERVER['REMOTE_ADDR'] ) );
	}

	//*************************************************************************
	//* Array/Option Methods
	//*************************************************************************

	/**
	 * Alias for {@link \Kisma\Kisma::o)
	 * @param array $options
	 * @param string $key
	 * @param mixed|null $defaultValue
	 * @param boolean $unsetValue
	 * @return mixed
	 */
	public static function getOption( array &$options = array(), $key, $defaultValue = null, $unsetValue = false )
	{
		return self::o( $options, $key, $defaultValue, $unsetValue );
	}

	/**
	 * Retrieves an option from the given array. $defaultValue is set and returned if $_key is not 'set'.
	 * Optionally will unset option in array.
	 *
	 * @param array $options
	 * @param string $key
	 * @param mixed|null $defaultValue
	 * @param boolean $unsetValue
	 * @return mixed
	 * @see \Kisma\Kisma::getOption
	 */
	public static function o( array &$options = array(), $key, $defaultValue = null, $unsetValue = false )
	{
		$_newValue = $defaultValue;

		if ( is_array( $options ) )
		{
			if ( !array_key_exists( $key, $options ) )
			{
				//	Ignore case and look...
				$_newKey = trim( strtolower( $key ) );

				foreach ( $options as $_key => $_value )
				{
					if ( trim( strtolower( $_key ) ) == $_newKey )
					{
						//	Set correct key and break
						$key = $_key;
						break;
					}
				}
			}

			if ( isset( $options[$key] ) )
			{
				$_newValue = $options[$key];

				if ( $unsetValue )
				{
					unset( $options[$key] );
				}
			}

			//	Set it in the array if not an unsetter...
			if ( !$unsetValue )
			{
				$options[$key] = $_newValue;
			}
		}
		else
		{
			if ( is_object( $options ) && property_exists( $options, $key ) )
			{
				if ( isset( $options->$key ) )
				{
					$_newValue = $options->$key;

					if ( $unsetValue )
					{
						unset( $options->$key );
					}
				}

				if ( !$unsetValue )
				{
					$options->$key = $_newValue;
				}
			}
		}

		//	Return...
		return $_newValue;
	}

	/**
	 * Similar to {@link \Kisma\Kisma::o} except it will pull a value from a nested array.
	 * @param array $options
	 * @param string $key
	 * @param string $subKey
	 * @param mixed|null $defaultValue
	 * @param boolean $unsetValue Only applies to target value
	 * @param mixed $defaultValue Only applies to target value
	 * @return mixed
	 */
	public static function oo( array &$options = array(), $key, $subKey, $defaultValue = null, $unsetValue = false )
	{
		return self::o(
			self::o(
				$options,
				$key,
				array()
			),
			$subKey,
			$defaultValue,
			$unsetValue
		);
	}

	/**
	 * Alias for {@link \Kisma\Kisma::so}
	 * @param array $options
	 * @param string $key
	 * @param mixed $value
	 * @return mixed The new value of the key
	 */
	public static function setOption( array &$options = array(), $key, $value = null )
	{
		return self::so( $options, $key, $value );
	}

	/**
	 * Sets an option in the given array.
	 * @param array $options
	 * @param string $key
	 * @param mixed $defaultValue Defaults to null
	 * @internal param string $_key
	 * @return mixed The new value of the key
	 */
	public static function so( array &$options = array(), $key, $defaultValue = null )
	{
		return $options[$key] = $defaultValue;
	}

	/**
	 * Alias of {@link \Kisma\Kisma::unsetOption}
	 * @param array $options
	 * @param string $key
	 * @return mixed The last value of the key
	 */
	public static function unsetOption( array &$options = array(), $key )
	{
		return self::uo( $options, $key );
	}

	/**
	 * Unsets an option in the given array
	 *
	 * @param array $options
	 * @param string $key
	 * @return mixed The new value of the key
	 */
	public static function uo( array &$options, $key )
	{
		return self::o( $options, $key, null, true );
	}

	/**
	 * Generic super-easy/lazy way to convert lots of different things (like SimpleXmlElement) to an array
	 * @param object $object
	 * @return array|false
	 */
	public static function toArray( $object )
	{
		if ( is_object( $object ) )
		{
			$object = (array)$object;
		}

		if ( !is_array( $object ) )
		{
			return $object;
		}

		$_result = array();

		foreach ( $object as $_key => $_value )
		{
			$_result[preg_replace( "/^\\0(.*)\\0/", "", $_key )] = self::toArray( $_value );
		}

		return $_result;
	}

	/**
	 * Create a path alias.
	 * Note, this method neither checks the existence of the path nor normalizes the path.
	 * @param string $alias alias to the path
	 * @param string $path the path corresponding to the alias. If this is null, the corresponding
	 * path alias will be removed.
	 */
	public static function alias( $alias, $path )
	{
	}

	/**
	 * Translates an alias into a file path.
	 * Note, this method does not ensure the existence of the resulting file path.
	 * It only checks if the root alias is valid or not.
	 * @param string $alias alias (e.g. system.web.CController)
	 * @param string $url Additional url combine with alias
	 * @return string|false file path corresponding to the alias, false if the alias is invalid.
	 */
	public static function unalias( $alias )
	{
	}

	/**
	 * Tests if a value needs unserialization
	 * @param string $value
	 * @return boolean
	 */
	public static function isSerialized( $value )
	{
		$_result = @unserialize( $value );
		return !( false === $_result && $value != serialize( false ) );
	}

	/**
	 * @static
	 * @param \Kisma\Components\Component $object
	 * @param string $propertyName
	 * @param \Kisma\AccessorMode|int $access
	 * @param mixed|null $valueOrDefault The "Set" value or default value for "Get"
	 * @return mixed
	 */
	public static function __checkProperty( &$object, $propertyName, $access = AccessorMode::Get, $valueOrDefault = null )
	{
		/**
		 * @var bool Keeps track of recursive encounters.
		 */
		static $_aspectCheck = false;

		try
		{
			//	Try object first. Squelch undefineds...
			return self::__property( $object, $propertyName, $access, $valueOrDefault );
		}
		//	Ignore undefined properties. Another aspect may have it
		catch ( \Kisma\UndefinedPropertyException $_ex )
		{
			//	Ignored
		}
		catch ( PropertyException $_ex )
		{
			//	Rethrow other property exceptions
			throw $_ex;
		}

		//	Check aspects...
		if ( false === $_aspectCheck && $object instanceof IAspectable )
		{
			$_aspectCheck = true;

			foreach ( $object->getAspects() as $_aspect )
			{
				try
				{
					return self::__checkProperty( $_aspect, $propertyName, $access, $valueOrDefault );
				}
				catch ( \Kisma\UndefinedPropertyException $_ex )
				{
					//	Ignore
				}
			}

			$_aspectCheck = false;
		}

		//	No clue what they're talking about
		throw new \Kisma\UndefinedPropertyException( 'The property "' . $propertyName . '" is undefined.', \Kisma\AccessorMode::Undefined );
	}

	/**
	 * An all-purpose property accessor.
	 * @static
	 * @throws PropertyException

	 * @param \Kisma\Components\Component $object
	 * @param string $propertyName
	 * @param \Kisma\AccessorMode $access
	 * @param mixed|null $valueOrDefault The "Set" value or default value for "Get"
	 * @return \Kisma\Components\Component|bool|mixed
	 */
	public static function __property( &$object, $propertyName, $access = \Kisma\AccessorMode::Get, $valueOrDefault = null )
	{
		$_propertyName = self::standardizeName( $propertyName );

		$_getter = 'get' . $_propertyName;
		$_setter = 'set' . $_propertyName;

		switch ( $access )
		{
			case \Kisma\AccessorMode::Has:
				//	Is it accessible
				if ( method_exists( $object, $_getter ) || method_exists( $object, $_setter ) )
				{
					return true;
				}
				break;

			case \Kisma\AccessorMode::Get:
				//	Does a setter exist?
				if ( method_exists( $object, $_getter ) )
				{
					return $object->{$_getter}();
				}

				//	Is it write only?
				if ( method_exists( $object, $_setter ) )
				{
					self::__propertyError( $_propertyName, \Kisma\AccessorMode::WriteOnly );
				}
				break;

			case \Kisma\AccessorMode::Set:
				//	Does a setter exist?
				if ( method_exists( $object, $_setter ) )
				{
					return $object->{$_setter}( $valueOrDefault );
				}

				//	Is it read only?
				if ( !method_exists( $object, $_setter ) && method_exists( $object, $_getter ) )
				{
					self::__propertyError( $_propertyName, \Kisma\AccessorMode::ReadOnly );
				}
				break;
		}

		//	Everything falls through to undefined
		self::__propertyError( $propertyName, \Kisma\AccessorMode::Undefined );
	}

	/**
	 * A generic property error handler
	 *
	 * @throws \Kisma\UndefinedPropertyException|\Kisma\ReadOnlyPropertyException|\Kisma\WriteOnlyPropertyException

	 * @param string $name
	 * @param \Kisma\AccessorMode|int $type
	 * @return void
	 */

	/**
	 * @static
	 * @param string $name
	 * @param \Kisma\AccessorMode $type
	 */
	public static function __propertyError( $name, $type = \Kisma\AccessorMode::Undefined )
	{
		$_name = self::standardizeName( $name );

		switch ( $type )
		{
			case \Kisma\AccessorMode::ReadOnly:
				$_class = '\Kisma\ReadOnlyPropertyException';
				$_reason = 'read-only';
				break;

			case \Kisma\AccessorMode::WriteOnly:
				$_class = '\Kisma\WriteOnlyPropertyException';
				$_reason = 'write-only';
				break;

			default:
				$_class = '\Kisma\UndefinedPropertyException';
				$_reason = 'undefined';
				break;
		}

		throw new $_class( 'Property "' . get_called_class() . '"."' . $_name . '" is ' . $_reason . '.', $type );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @return \Controller
	 */
	public static function getAppController()
	{
		return self::$_appController;
	}

	/**
	 * @return \CWebUser
	 */
	public static function getUser()
	{
		return self::$_user;
	}

	/**
	 * @return \Application
	 */
	public static function getApp()
	{
		return self::$_app;
	}

	/**
	 * @return array
	 */
	public static function getAppParameters()
	{
		return self::$_appParameters;
	}

	/**
	 * @param string $parameter
	 * @param mixed|null $defaultValue
	 * @return mixed
	 */
	public static function getParameter( $parameter, $defaultValue = null )
	{
		return K::o( self::$_appParameters, $parameter, $defaultValue );
	}

	/**
	 * @param int $uniqueIdCounter
	 * @return int
	 */
	public static function setUniqueIdCounter( $uniqueIdCounter )
	{
		return self::$_uniqueIdCounter = $uniqueIdCounter;
	}

	/**
	 * @return int
	 */
	public static function getUniqueIdCounter()
	{
		return self::$_uniqueIdCounter;
	}

//	/**
//	 * @param $validLogLevels
//	 * @param array $validLogLevels
//	 */
//	public static function setValidLogLevels( $validLogLevels )
//	{
//		self::$_validLogLevels = $validLogLevels;
//	}
//
//	/**
//	 * @return array
//	 */
//	public static function getValidLogLevels()
//	{
//		return self::$_validLogLevels;
//	}

//	/**
//	 * Bind a callback to an event
//	 * @param string $eventName
//	 * @param callback|null $callback
//	 * @return boolean
//	 */
//	public function bind( $eventName, $callback = null )
//	{
//		// TODO: Implement bind() method.
//		return false;
//	}
//
//	/**
//	 * Unbind from an event
//	 * @param string $eventName
//	 * @return boolean
//	 */
//	public function unbind( $eventName )
//	{
//		// TODO: Implement unbind() method.
//		return false;
//	}

//	/**
//	 * Gets the debug level
//	 * @return \Kisma\LogLevel
//	 */
//	public function getLogging()
//	{
//		// TODO: Implement getLogging() method.
//	}
//
//	/**
//	 * Sets the debug level
//	 * @param \Kisma\LogLevel $logging
//	 * @return integer The previous value
//	 */
//	public function setLogging( $logging )
//	{
//		// TODO: Implement setLogging() method.
//	}
}

//	Initialize our base...
Kisma::initialize();
