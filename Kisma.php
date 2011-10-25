<?php
/**
 * Kisma.php
 * The Kisma(tm) Framework bootstrap loader
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license			http://github.com/Pogostick/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @category		Kisma
 * @package			kisma
 * @since			v1.0.0
 * @filesource
 */
/**
 * The global namespace
 */
namespace
{
	/**************************************************************************
	 ** Requirements
	 **************************************************************************/

	/**
	 * Enums
	 */
	require_once 'Kisma/enums.php';
	/**
	 * Exceptions
	 */
	require_once 'Kisma/exceptions.php';
}
/**
 * The Kisma namespace
 */
namespace Kisma
{	/** Starts Namespace \Kisma */

	//*************************************************************************
	//* Aliases
	//*************************************************************************

	use \Kisma\Utility as Utility;

	/**
	 * Kisma
	 * The mother of all Kisma classes
	 *
	 * Contains a few core functions implemented statically to
	 * be lightweight and single instance.
	 *
	 * @property int $uniqueIdCounter
	 * @property int $debugLevel
	 * @proeprty array $settings
	 */
	class Kisma implements IGlobalDebuggable, ISettings
	{
		//********************************************************************************
		//* Private Members
		//********************************************************************************

		/**
		 * @var int A static ID counter for generating unique names
		 * @static
		 */
		protected static $_uniqueIdCounter = 1000;
		/**
		 * @var DebugLevel|int The debug level
		 * @static
		 */
		protected static $_debugLevel = DebugLevel::Normal;
		/** @var array Any settings you may want to store for later */
		protected static $_settings = array();
		/**
		 * @var array Our modules
		 */
		protected static $_modules = array();

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * Called when this class loads. Does nothing but return false allowing
		 * you, my humble geeks, to extend this class and have a built-in
		 * constructor of sorts.
		 *
		 * @param array|null $settings
		 * @return bool
		 */
		public static function initialize( $settings = array() )
		{
			if ( null === $settings )
			{
				\Kisma\Utility\Log::debug( 'cwd: ' . getcwd() );
				\Kisma\Utility\Log::debug( '__DIR__: ' . __DIR__ );
			}

			//	Save passed in options...
			self::$_settings = self::cleanOptions( $settings );

			//	Get our base path and save...
			self::so( self::$_settings, \KismaSettings::BasePath, __DIR__ );

			if ( self::getDebugLevel( \Kisma\DebugLevel::Verbose ) )
			{
				self::logDebug( '\Kisma\Kisma object initialized.' );
			}

			return true;
		}

		/**
		 * Given a Kisma identifier, return it to neutral format (lowercase, period and underscores)
		 *
		 * Examples:
		 *	 Class Name:		\Kisma\Components\ComponentEvent becomes "kisma.components.component_event"
		 *
		 * @param string $tag
		 * @return string
		 */
		public static function untag( $tag )
		{
			$_parts = explode( '\\', $tag );

			array_walk( $_parts, function( &$part ) {
				//	Replace
				$part = strtolower( preg_replace( '/(?<=\\w)([A-Z])/', '_\\1', $part ) );
			});

			return implode( '.', $_parts );
		}

		/**
		 * Given a simple name, clean it up to a Kisma standard, camel-cased, format.
		 *
		 * Periods should be used to separate namespaces.
		 * Underscores should be used to separate identifier words
		 *
		 * Examples:
		 *	 Class Name:		kisma.aspects.event_handling => \Kisma\Aspects\EventHandling
		 *	 Array Key:		my_event => MyEvent
		 *
		 * @param string $tag
		 * @param bool $isKey If true, the $tag will be converted to a format suitable for use as an array key
		 * @param bool $baseNameOnly If true, only the final, base of the tag will be returned.
		 * @param array $keyParts
		 * @return string
		 */
		public static function tag( $tag, $isKey = false, $baseNameOnly = false, &$keyParts = array() )
		{
			//	If we're dotted, clean up
			if ( false !== strpos( $tag, '.' ) )
			{
				//	Convert dots to spaces, then spaces to namespace separators.
				$tag = str_replace( ' ', '\\', ucwords( trim( str_replace( '.', ' ', $tag ) ) ) );
			}

			//	Convert underscores to spaces, then remove spaces
			$_tag = str_replace( ' ', null, ucwords( trim( str_replace( '_', ' ', $tag ) ) ) );

			if ( false !== $isKey )
			{
				//	Convert namespace separators to dots
				$_tag = lcfirst( str_replace( '\\', '.', $_tag ) );
				$keyParts = explode( '.', $_tag );
			}

			//	Only the base?
			if ( false !== $baseNameOnly )
			{
				//	If this is a key, just get the last part
				$_tag = end( explode( '\\', $_tag ) );
			}

			if ( self::getDebugLevel( \Kisma\DebugLevel::Nutty ) )
			{
				self::logDebug( 'tag: ' . $tag . ' => ' . $_tag . ( false !== $isKey ? ' (as key)' : null ) );
			}

			return $_tag;
		}

		//*************************************************************************
		//* Logging
		//*************************************************************************

		/**
		 * @static
		 * @param $logEntry
		 * @param string $tag
		 * @return bool
		 */
		public static function logDebug( $logEntry, $tag = null )
		{
			return Utility\Log::debug( $logEntry, $tag );
		}

		/**
		 * @static
		 * @param $logEntry
		 * @param string $tag
		 * @return bool
		 */
		public static function logError( $logEntry, $tag = null )
		{
			return Utility\Log::error( $logEntry, $tag );
		}

		/**
		 * @static
		 * @param string $logEntry
		 * @param string $tag
		 * @param int|\Kisma\LogLevel $level
		 * @param bool $returnEntry
		 * @return bool|string
		 */
		public static function log( $logEntry, $tag, $level = LogLevel::Debug, $returnEntry = false )
		{
			static $_logFile = null;

			$_name = null;

			if ( null === $_logFile )
			{
				$_path = self::getSetting( 'log.path' );
				$_name = self::getSetting( 'log.file_name' );

				//	Make sure the directory is writable
				if ( false === @mkdir( $_path, 0750, true ) )
				{
					if ( !is_dir( $_path ) || !is_writable( $_path ) )
					{
						throw new \RuntimeException( 'The configured log path "' . $_path . '" is not writable.' );
					}
				}
				
				$_logFile = rtrim( $_path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . trim( $_name, DIRECTORY_SEPARATOR );
			}

			if ( empty( $_logFile ) )
			{
				echo 'no log name!';
				//	No logging configured, just bail...
				return false;
			}

			$_entry =  sprintf( '%s %d:[%-30.30s] %s',
				date( 'M d H:i:s' ),
				$level,
				$tag,
				$logEntry
			);

			if ( false !== $returnEntry )
			{
				//	Return string if requested
				return $_entry;
			}

			//	Otherwise write to log
			return error_log( $_entry . PHP_EOL, 3, $_logFile );
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
		public static function createUniqueName( \Kisma\IComponent $component, $humanReadable = false )
		{
			$_tag = self::tag( get_class( $component ) . '.' . self::$_uniqueIdCounter++ );
			return 'kisma.' . ( $humanReadable ? $_tag : Utility\Hash::hash( $_tag ) );
		}

		/**
		 * @static
		 * @param string $className
		 * @param array $options
		 * @return \Kisma\Components\Component
		 */
		public static function createComponent( $className, $options = array() )
		{
			$_className = self::tag( $className );
			return new $_className( $options );
		}

		/**
		 * @static
		 * @param string $moduleName
		 * @param array $options
		 * @return string
		 */
		public static function createModule( $moduleName, $options = array() )
		{
			$_modulePath = self::getSetting( 'module_path', getcwd() . DIRECTORY_SEPARATOR . 'modules' );
			$_class = self::tag( $moduleName, false, false, $_parts );

			try
			{
				/** @noinspection PhpIncludeInspection */
				require_once $_modulePath . DIRECTORY_SEPARATOR . $_class . '.php';
			}
			catch ( \Exception $_ex )
			{
				\Kisma\Utility\Log::error( 'Module "' . $_moduleName . '" not found. Check your paths.' );
			}
//			return self::$_modules[self::tag( $moduleName, true )] = new $_class( $options );
		}

		/**
		 * Retrieve a module
		 * @static
		 * @param string $moduleName
		 * @return Components\Component|false
		 */
		public static function getModule( $moduleName )
		{
			return self::hasComponent( self::$_modules, $moduleName, true );
		}

		/**
		 * Takes a list of things and returns them in an array as the values. Keys are maintained.
		 * @param mixed [optional]
		 * @return array
		 */
		public static function createArray()
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
		 * @param mixed [optional]
		 * @return mixed
		 */
		public static function nvl()
		{
			$_defaultValue = null;

			foreach ( func_get_args() as $_argument )
			{
				if ( null === $_argument )
				{
					continue;
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
		 * @param mixed [optional]
		 * @return boolean
		 */
		public static function in()
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
		public static function within( $haystack, $needle, $offset = 0, $caseSensitive = false )
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
		 * Takes the arguments and concatenates them with $separator in between.
		 * @param string $separator
		 * @return string
		 */
		public static function glue( $separator )
		{
			return implode( $separator, func_get_args() );
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
		}

		/**
		 * Translates an alias into a file path.
		 * Note, this method does not ensure the existence of the resulting file path.
		 * It only checks if the root alias is valid or not.
		 *
		 * @param string $alias
		 * @return string|false file path corresponding to the alias, false if the alias is invalid.
		 */
		public static function unalias( $alias )
		{
			return false;
		}

		/**
		 * @static
		 * @param string|array $libraryPath Relative path under library_path
		 */
		public static function importLibrary( $libraryPath )
		{
			$_libraries = !is_array( $libraryPath ) ? array( $libraryPath ) : $libraryPath;

			foreach ( $_libraries as $_libraryPath )
			{
				$_realPath = str_replace(
					'\\',
					DIRECTORY_SEPARATOR,
					self::tag( rtrim( \K::getSetting( 'library_path' ), DIRECTORY_SEPARATOR ) )
				);

				$_realPath .= DIRECTORY_SEPARATOR . rtrim( $_libraryPath, DIRECTORY_SEPARATOR );
				\set_include_path( \get_include_path() . PATH_SEPARATOR . $_realPath );
			}
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
		 * Sets a value within an array only if the value is not set (SetIfNotSet=SINS).
		 * You can pass in an array of key value pairs and do many at once.
		 *
		 * @param \Kisma\Components\SubComponent|\stdClass|array $options
		 * @param string $key
		 * @param mixed $value
		 * @return bool
		 */
		public static function sins( &$options = array(), $key, $value = null )
		{
			if ( !is_array( $key ) )
			{
				$key = array( $key => $value );
			}

			foreach ( $key as $_key => $_value )
			{
				//	If the key is set, we bail...
				if ( is_array( $options ) && !isset( $options[$_key] ) )
				{
					$options[$_key] = $_value;
					return true;
				}

				if ( is_object( $options ) && !isset( $options->{$_key} ) )
				{
					$options->{$_key} = $_value;
					return true;
				}

				//	Not set or goofy object
			}

			//	Sorry charlie...
			return false;
		}

		/**
		 * Given a tag, make the namespace available to the app but don't trigger auto loading
		 *
		 * @param string $kismaTag
		 * @param bool $loadClass
		 * @return bool
		 */
		public static function uses( $kismaTag, $loadClass = false )
		{
			//	Get the namespace out of the way
			$_root = str_replace( __NAMESPACE__ . '\\', null, self::tag( $kismaTag ) );

			//	Is it a wildcard?
			if ( '*' == end( explode( '\\', $_root ) ) )
			{
				//	Strip off the star and the slashes
				$_root = rtrim( rtrim( $_root, DIRECTORY_SEPARATOR ), '*' );

				//	Should be a directory now
				if ( is_dir( $_root ) )
				{
					//	Add to include path
					ini_set( 'include_path', ini_get( 'include_path' ) . ':' . $_root );
					eval( 'use ' . $_root );
					return true;
				}
			}

			$_class =
				__DIR__ .
				DIRECTORY_SEPARATOR .
				trim(
					str_replace( '\\', DIRECTORY_SEPARATOR, Kisma::tag( $_root ) ),
					DIRECTORY_SEPARATOR
				);

			//	Do we have it already?
			if ( class_exists( $_class . '.php', false ) || interface_exists( $_class . '.php', false ) )
			{
				//	Already loaded!
				return true;
			}

			//	Does it exist?
			if ( file_exists( $_class . '.php' ) )
			{
				//	Do they want it loaded?
				if ( false !== $loadClass )
				{
					/** @noinspection PhpIncludeInspection */
					return include_once( $_root );
				}
			}
			else
			{
				//	Sorry charlie...
				return false;
			}


			//	No go
			return false;
		}

		//*************************************************************************
		//* Array/Option Methods
		//*************************************************************************

		/**
		 * Takes a non-standardized array of options and cleans them up
		 *
		 * @param array $options
		 * @param bool $recursive If true, and an array value is an array, it too will be cleaned.
		 * @return array The rebuilt array
		 */
		public static function cleanOptions( array $options = array(), $recursive = true )
		{
			$_options = array();

			foreach ( $options as $_key => $_value )
			{
				if ( false !== $recursive && is_array( $_value ) )
				{
					$_value = self::cleanOptions( $_value, $recursive );
				}

				$_options[self::tag( $_key, true, false )] = $_value;
			}

			return $_options;
		}

		/**
		 * Retrieves an value from the system settings
		 *
		 * @param string $key
		 * @param mixed|null $defaultValue
		 * @return mixed
		 */
		public static function getSetting( $key, $defaultValue = null )
		{
			$_key = self::tag( $key, true, false, $_parts );

			if ( isset( self::$_settings[$_key] ) )
			{
				return self::$_settings[$_key];
			}

			$_base = self::$_settings;
			$_count = count( $_parts );

			foreach ( $_parts as $_part )
			{
				$_part = self::tag( $_part, true );

				//	Is this leaf there?
				if ( !isset( $_base[$_part] ) )
				{
					$_base = null;
					break;
				}

				$_base = $_base[$_part];
			}

			return $_base ?: $defaultValue;
		}

		/**
		 * Alias for {@link \Kisma\Kisma::o)
		 * @param array $options
		 * @param string $key
		 * @param mixed|null $defaultValue
		 * @param boolean $unsetValue
		 * @return mixed
		 */
		public static function getOption( $options = array(), $key, $defaultValue = null, $unsetValue = false )
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
		public static function o( $options = array(), $key, $defaultValue = null, $unsetValue = false )
		{
			//	Standardize the key
			$key = self::tag( $key, true, false );

			//	Set the default value
			$_newValue = $defaultValue;

			//	Get array value if it exists
			if ( is_array( $options ) )
			{
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
			//	Also now handle accessible object properties
			else if ( is_object( $options ) && property_exists( $options, $key ) )
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
		public static function oo( $options = array(), $key, $subKey, $defaultValue = null, $unsetValue = false )
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
		 * @return mixed
		 */
		public static function setOption( &$options = array(), $key, $value = null )
		{
			return self::so( $options, $key, $value );
		}

		/**
		 * Sets an value in the given array at key.
		 *
		 * @param array|object $options
		 * @param string $key
		 * @param mixed|null $value
		 * @return mixed The new value of the key
		 */
		public static function so( &$options = array(), $key, $value = null )
		{
			if ( is_array( $options ) )
			{
				return $options[self::tag( $key, true, false )] = $value;
			}
			else if ( is_object( $options ) )
			{
				return $options->$key = $value;
			}

			return null;
		}

		/**
		 * Alias of {@link \Kisma\Kisma::unsetOption}
		 * @param array $options
		 * @param string $key
		 * @return mixed The last value of the key
		 */
		public static function unsetOption( &$options = array(), $key )
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
		public static function uo( &$options = array(), $key )
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

		/**************************************************************************
		 ** Property Methods
		 **************************************************************************/

		/**
		 * @static
		 * @param \Kisma\Components\SubComponent $object
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

				/** @var $object \Kisma\IAspectable */
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
		 * @param Components\SubComponent $object
		 * @param string $propertyName
		 * @param int $access
		 * @param mixed|null $valueOrDefault
		 * @return bool
		 */
		public static function __property( &$object, $propertyName, $access = \Kisma\AccessorMode::Get, $valueOrDefault = null )
		{
			$_propertyName = self::tag( $propertyName );

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
		 * A generic property error handler to go with our generic property system.
		 * @param string $name
		 * @param int $type
		 * @throws UndefinedPropertyException|ReadOnlyPropertyException|WriteOnlyPropertyException
		 */
		public static function __propertyError( $name, $type = \Kisma\AccessorMode::Undefined )
		{
			$_name = self::tag( $name );

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

		/**
		 * Determines if this component is within the map. If not, the return value is false.
		 * If the component is in the map, the return value is the key name of the component.
		 * If $returnObject is set to true, the component will be returned
		 *
		 * @static
		 * @param array $map The hash of names to components
		 * @param string $name
		 * @param bool $returnObject If true, the component will be returned instead of the name
		 * @return false|string|\Kisma\Components\Component
		 */
		public static function hasComponent( $map, $name, $returnObject = false )
		{
			$_key = self::tag( $name, true );

			return
				is_array( $map ) && isset( $map[$_key] )
					?
					(
						false !== $returnObject
							?
							//	Return the component
							$map[$_key]
							:
							//	Return the key
							$_key
					)
					:
					//	Not found
					false;
		}

		/**
		 * Prepares and births your objects...
		 * (read: smart namespace autoloader)
		 *
		 * @param string $className
		 * @return mixed
		 */
		public static function gestate( $className )
		{
			static $_extensions, $_paths;

			if ( null === $_extensions )
			{
				$_extensions = array_map(
					'trim',
					explode( ',', spl_autoload_extensions() )
				);
			}

			if ( null === $_paths )
			{
				$_paths = explode( PATH_SEPARATOR, get_include_path() );
			}

			//	Build the class name
			$_class = trim(
				str_replace( '\\', DIRECTORY_SEPARATOR, self::tag( $className ) ),
				DIRECTORY_SEPARATOR
			);

			//	Are we aware of this class?
			if ( class_exists( $_class, false ) || interface_exists( $_class, false ) )
			{
				return true;
			}

			//	Look for the class
			foreach ( $_paths as $_path )
			{
				$_path .= str_replace( '//', null, ( DIRECTORY_SEPARATOR !== $_path[strlen( $_path ) - 1] ) ? DIRECTORY_SEPARATOR : '' );

				foreach ( $_extensions as $_extension )
				{
					$_file = $_path . $_class . $_extension;

					if ( file_exists( $_file ) )
					{
						/** @noinspection PhpIncludeInspection */
						return require_once $_file;
					}
				}
			}

			throw new \Exception( 'Class "' . $_class . '" not found.' );
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

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

		/**
		 * @param DebugLevel|int $debugLevel
		 */
		public static function setDebugLevel( $debugLevel )
		{
			self::$_debugLevel = $debugLevel;
		}

		/**
		 * @param \Kisma\DebugLevel|int|null $debugLevel
		 * @return boolean|\Kisma\DebugLevel
		 */
		public static function getDebugLevel( $debugLevel = null )
		{
			if ( null === $debugLevel )
			{
				return self::$_debugLevel;
			}

			//	If our level is what was requested, return true
			return ( self::$_debugLevel === $debugLevel );
		}

		/**
		 * @param array $settings
		 */
		public static function setSettings( $settings = array() )
		{
			self::$_settings = $settings;
		}

		/**
		 * @return array
		 */
		public static function getSettings()
		{
			return self::$_settings;
		}

	};

} /** Ends Namespace \Kisma */

/**
 * A global alias of Kisma called "K"
 */
namespace
{
	//*************************************************************************
	//* Classes
	//*************************************************************************

	/**
	 * K
	 * An alias to the Kisma base
	 */
	class K extends \Kisma\Kisma implements \KismaSettings
	{
	}

	/**
	 * KismaBootstrap
	 * The bootstrap class!
	 */
	class KismaBootstrap
	{
		/**
		 * @static
		 * @param array $configuration
		 */
		public static function loadModules( $configuration = array() )
		{
			//	Initialize Kisma
			\K::initialize( $configuration );

			//	Add the library path to the includes
			if ( null !== ( $_libPath = \K::getSetting( 'library_path' ) ) )
			{
				\set_include_path( get_include_path() . PATH_SEPARATOR . $_libPath );
			}

			$_modules = \K::o( $configuration, 'modules' );

			foreach ( $_modules as $_moduleName => $_config )
			{
				\K::createModule( $_moduleName, $_config );
			}
		}
	}

	//*************************************************************************
	//* Initialize the framework
	//*************************************************************************

	/**
	 * Set up the autoloader
	 */
	\set_include_path( get_include_path() . PATH_SEPARATOR . __DIR__ );
	\spl_autoload_extensions( '.php' );
	\spl_autoload_register();
	\spl_autoload_register( '\\Kisma\\Kisma::gestate', true, true );
}
