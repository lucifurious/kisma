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
 */
class Kisma implements IKisma, IBroadcaster, IObservable
{
	//********************************************************************************
	//* Private Members
	//********************************************************************************

	/**
	 * Cache the current app for speed
	 * @static
	 * @var CWebApplication
	 */
	protected static $_app = null;
	/**
	 * Cache the current request
	 * @static
	 * @var CHttpRequest
	 */
	protected static $_request = null;
	/**
	 * Cache the client script object for speed
	 * @static
	 * @var CClientScript
	 */
	protected static $_clientScript = null;
	/**
	 * Cache the user object for speed
	 * @static
	 * @var CWebUser
	 */
	protected static $_user = null;
	/**
	 * Cache the current controller for speed
	 * @static
	 * @var CController
	 */
	protected static $_controller = null;
	/**
	 * Our valid log levels based on interface definition
	 * @static
	 * @var array
	 */
	protected static $_validLogLevels;
	/**
	 * A static ID counter for generating unique names
	 * @static
	 * @var integer
	 */
	protected static $_uniqueIdCounter = 1000;
	/**
	 * Cache the application parameters for speed
	 * @static
	 * @var CAttributeCollection
	 */
	protected static $_appParameters = null;
	/**
	 * An array of class names to search in for missing static methods.
	 * This is a quick an dirty little polymorpher.
	 * @var array
	 * @static
	 */
	protected static $_classPath = array(
		'CHtml',
	);

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Given a property name, clean it up to a standard, camel-cased, format.
	 * @param string $name
	 * @return string
	 */
	public static function standardizeName( $name )
	{
		return str_replace( ' ', null, ucwords( trim( str_replace( '_', ' ', $name ) ) ) );
	}

	/**
	 * Initialize our private statics
	 * @param array $options
	 * @return bool
	 */
	public static function initialize( $options = array() )
	{
		return true;
	}

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
	 * Alias for {@link xlHelperBase::o)
	 * @param array $options
	 * @param $key
	 * @param mixed $defaultValue
	 * @param boolean $unsetValue
	 *
	 * @internal param string $_key
	 * @return mixed
	 */
	public static function getOption( &$options = array(), $key, $defaultValue = null, $unsetValue = false )
	{
		return self::o( $options, $key, $defaultValue, $unsetValue );
	}

	/**
	 * Retrieves an option from the given array. $defaultValue is set and returned if $_key is not 'set'.
	 * Optionally will unset option in array.
	 *
	 * @param array $options
	 * @param $key
	 * @param mixed $defaultValue
	 * @param boolean $unsetValue
	 *
	 * @internal param int|string $_key
	 * @return mixed
	 * @see xlHelperBase::getOption
	 */
	public static function o( &$options = array(), $key, $defaultValue = null, $unsetValue = false )
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
	 * Similar to {@link YiiXLBase::o} except it will pull a value from a nested array.
	 * @param array $options
	 * @param $key
	 * @param integer|string $subKey
	 * @param mixed $defaultValue Only applies to target value
	 * @param boolean $unsetValue Only applies to target value
	 *
	 * @internal param int|string $_key
	 * @return mixed
	 */
	public static function oo( &$options = array(), $key, $subKey, $defaultValue = null, $unsetValue = false )
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
	 * Alias for {@link xlHelperBase::so}
	 * @param array $options
	 * @param $key
	 * @param mixed $value
	 *
	 * @internal param string $_key
	 * @return mixed The new value of the key
	 */
	public static function setOption( array &$options, $key, $value = null )
	{
		return self::so( $options, $key, $value );
	}

	/**
	 * Sets an option in the given array.
	 * @param array $options
	 * @param $key
	 * @param mixed $value Defaults to null
	 *
	 * @internal param string $_key
	 * @return mixed The new value of the key
	 */
	public static function so( array &$options, $key, $value = null )
	{
		return $options[$key] = $value;
	}

	/**
	 * Alias of {@link xlHelperBase::unsetOption}
	 * @param array $options
	 * @param $key
	 *
	 * @internal param string $_key
	 * @return mixed The last value of the key
	 */
	public static function unsetOption( array &$options, $key )
	{
		return self::uo( $options, $key );
	}

	/**
	 * Unsets an option in the given array
	 *
	 * @param array $options
	 * @param $key
	 *
	 * @internal param string $_key
	 * @return mixed The last value of the key
	 */
	public static function uo( array &$options, $key )
	{
		return self::o( $options, $key, null, true );
	}

	/**
	 * Takes a list of things and returns them in an array as the values. Keys are maintained.
	 * @param mixed $_ [optional]
	 * @return array
	 */
	public static function makeArray( &$_ = null )
	{
		$_newArray = array();

		foreach ( func_get_args() as $_key => $_argument )
		{
			$_newArray[$_key] = $_argument;
		}

		//	Return the fresh array...
		return $_newArray;
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
	 * Determine if PHP is running CLI mode or not
	 * @return boolean True if currently running in CLI
	 */
	public static function isCli()
	{
		return ( 'cli' == php_sapi_name() && empty( $_SERVER['REMOTE_ADDR'] ) );
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
		Yii::setPathOfAlias( $alias, $path );
	}

	/**
	 * Translates an alias into a file path.
	 * Note, this method does not ensure the existence of the resulting file path.
	 * It only checks if the root alias is valid or not.
	 * @param string $alias alias (e.g. system.web.CController)
	 * @param string $url Additional url combine with alias
	 * @return string|false file path corresponding to the alias, false if the alias is invalid.
	 */
	public static function unalias( $alias, $url = null )
	{
		$_path = Yii::getPathOfAlias( $alias );

		if ( false !== $_path && null !== $url )
		{
			$_path = str_replace( $_SERVER['DOCUMENT_ROOT'], '', $_path ) . $url;
		}

		//	Append
		if ( !file_exists( $_path ) && file_exists( $_path . '.php' ) )
		{
			$_path .= '.php';
		}

		xlLog::trace( 'Unalias in: ' . $alias . ' out: ' . $_path );

		return $_path;
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
	 *
	 * @static
	 * @throws PropertyException
	 * @param \Kisma\Components\Component $object
	 * @param string $propertyName
	 * @param int|\Kisma\AccessorMode $access
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
	 * @param int|\Kisma\AccessorMode $type
	 * @return void
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
	 * @return \Controller
	 */
	public static function getController()
	{
		return self::$_controller;
	}

	/**
	/**
	 * @param $uniqueIdCounter
	 * @param int $uniqueIdCounter
	 */
	public static function setUniqueIdCounter( $uniqueIdCounter )
	{
		self::$_uniqueIdCounter = $uniqueIdCounter;
	}

	/**

	 * @return int
	 *
	 * @internal param int $
	 */
	public static function getUniqueIdCounter()
	{
		return self::$_uniqueIdCounter;
	}

	/**
	 * @return \CWebUser
	 */
	public static function getUser()
	{
		return self::$_user;
	}

	/**
	 * @param $validLogLevels
	 * @param array $validLogLevels
	 */
	public static function setValidLogLevels( $validLogLevels )
	{
		self::$_validLogLevels = $validLogLevels;
	}

	/**
	 * @return array
	 */
	public static function getValidLogLevels()
	{
		return self::$_validLogLevels;
	}

	/**
	 * Bind a callback to an event
	 * @param string $eventName
	 * @param callback|null $callback
	 * @return boolean
	 */
	public function bind( $eventName, $callback = null )
	{
		// TODO: Implement bind() method.
		return false;
	}

	/**
	 * Unbind from an event
	 * @param string $eventName
	 * @return boolean
	 */
	public function unbind( $eventName )
	{
		// TODO: Implement unbind() method.
		return false;
	}

	/**
	 * Gets the debug level
	 * @return \Kisma\LogLevel
	 */
	public function getLogging()
	{
		// TODO: Implement getLogging() method.
	}

	/**
	 * Sets the debug level
	 * @param \Kisma\LogLevel $logging
	 * @return integer The previous value
	 */
	public function setLogging( $logging )
	{
		// TODO: Implement setLogging() method.
	}
}

//	Initialize our base...
Kisma::initialize();

//	Namespace aliases do not work in CLI mode, so create "K" for all components
if ( 'cli' == php_sapi_name() )
{
	//	Make an alias of Kisma if this is CLI
	class_alias( '\Kisma\Kisma', '\Kisma\Components\K' );
}

