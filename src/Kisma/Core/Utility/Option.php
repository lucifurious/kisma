<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
 *
 * Kisma(tm) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kisma(tm) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kisma(tm).  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Kisma\Core\Utility;

/**
 * Option
 * Super kick-ass class to manipulate array and object properties in a uniform manner
 */
class Option
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type bool If true, all keys will be neutralized before accessed.
     *            Neutralization will convert camel-cased or dashed keys to lowercase
     *            underscore separation.
     */
    protected static $_neutralizeKeys = false;
    /**
     * @type bool If true, string values retrieved that are empty() return null instead of an empty string
     */
    protected static $_emptyStringEqualsNull = false;

    //*************************************************************************
    //* Get Methods
    //*************************************************************************

    /**
     * Retrieves an option from the given array.
     *
     * $defaultValue is returned if $key is not found.
     * Can optionally delete $key from $options.
     *
     * @param array|\ArrayAccess|object $options           An array or object from which to get $key's value
     * @param string|array              $key               The array index or property to retrieve from $options
     * @param mixed                     $defaultValue      The value to return if $key is not found
     * @param boolean                   $unsetValue        If true, the $key will be removed from $options after
     *                                                     retrieval
     * @param bool                      $emptyStringIsNull If true, empty() values will always return as NULL
     *
     * @return mixed
     */
    public static function get( &$options = array(), $key, $defaultValue = null, $unsetValue = false, $emptyStringIsNull = false )
    {
        //	Get many?
        if ( is_array( $key ) || $key instanceof \Traversable )
        {
            return static::getMany( $options, $key, $defaultValue, $unsetValue, $emptyStringIsNull );
        }

        switch ( gettype( $options ) )
        {
            case $options instanceof \ArrayAccess:
            case 'array':
                return static::_arrayGet( $options, $key, $defaultValue, $unsetValue, $emptyStringIsNull );

            case 'object':
                return static::_objectGet( $options, $key, $defaultValue, $unsetValue, $emptyStringIsNull );
        }

        return static::_emptified( $defaultValue, $emptyStringIsNull );
    }

    /**
     * @param array              $options
     * @param array|\Traversable $keys              Array of keys to get
     * @param mixed              $defaultValue
     * @param boolean            $unsetValue        If true, the $key will be removed from $options after retrieval
     * @param bool               $emptyStringIsNull If true, empty() values will always return as NULL
     *
     * @return mixed[]
     */
    public static function getMany( &$options = array(), $keys, $defaultValue = null, $unsetValue = false, $emptyStringIsNull = false )
    {
        $_results = array();

        foreach ( $keys as $_key )
        {
            $_results[$_key] = static::get( $options, $_key, $defaultValue, $unsetValue, $emptyStringIsNull );
        }

        return $_results;
    }

    /**
     * @param array|\ArrayAccess|object $options
     * @param string                    $key
     * @param string                    $subKey
     * @param mixed                     $defaultValue      Only applies to target value
     * @param boolean                   $unsetValue        If true, the $key will be removed from $options after
     *                                                     retrieval
     * @param bool                      $emptyStringIsNull If true, empty() values will always return as NULL
     *
     * @return mixed
     */
    public static function getDeep( &$options = array(), $key, $subKey, $defaultValue = null, $unsetValue = false, $emptyStringIsNull = false )
    {
        $_value =
            static::get( $options, $key, $defaultValue, $unsetValue, $emptyStringIsNull );

        return static::get( $_value, $subKey, $defaultValue, $unsetValue, $emptyStringIsNull );
    }

    /**
     * Retrieves a boolean option from the given array. $defaultValue is set and returned if $_key is not 'set'.
     * Optionally will unset option in array.
     *
     * Returns TRUE for "1", "true", "on", "yes" and "y". Returns FALSE otherwise.
     *
     * @param array|\ArrayAccess|object $options
     * @param string                    $key
     * @param boolean                   $defaultValue Defaults to false
     * @param boolean                   $unsetValue   If true, the $key will be removed from $options after retrieval
     *
     * @return bool Guaranteed boolean true or false
     */
    public static function getBool( &$options = array(), $key, $defaultValue = false, $unsetValue = false )
    {
        return Scalar::boolval( static::get( $options, $key, $defaultValue, $unsetValue ) );
    }

    /**
     * Retrieves a value from an array. $defaultValue is returned if $key is not found. Can optionally delete $key from
     * $options.
     *
     * @param array|\ArrayAccess $options           An array from which to get $key's value
     * @param string             $key               The array key to retrieve from $options
     * @param mixed              $defaultValue      The value to return if $key is not found
     * @param boolean            $unsetValue        If true, the $key will be removed from $options after retrieval
     * @param bool               $emptyStringIsNull If true, empty() values will always return as NULL
     * @param bool               $cleanedKey        Set to true if you're passing in a cleaned key
     *
     * @return mixed
     */
    protected static function _arrayGet( &$options = array(), $key, $defaultValue = null, $unsetValue = false, $emptyStringIsNull = false, $cleanedKey = false )
    {
        $_key = $cleanedKey ? $key : static::_cleanKey( $key );
        $_value = $defaultValue;

        if ( array_key_exists( $_key, $options ) )
        {
            $_value = $options[$_key];
            $unsetValue && static::remove( $options, $_key, true );
        }

        return static::_emptified( $_value, $emptyStringIsNull );
    }

    /**
     * @param object  $options           An object from which to get $key's value
     * @param string  $key               The array index or property to retrieve from $options
     * @param mixed   $defaultValue      The value to return if $key is not found
     * @param boolean $unsetValue        If true, the $key will be removed from $options after retrieval
     * @param bool    $emptyStringIsNull If true, empty() values will always return as NULL
     * @param bool    $cleanedKey        Set to true if you're passing in a cleaned key
     *
     * @return mixed
     */
    protected static function _objectGet( &$options, $key, $defaultValue = null, $unsetValue = false, $emptyStringIsNull = false, $cleanedKey = false )
    {
        $_key = $cleanedKey ? $key : static::_cleanKey( $key );

        if ( property_exists( $options, $_key ) )
        {
            try
            {
                $_value = $options->{$_key};
                $unsetValue && static::_objectRemove( $options, $_key, true );

                return static::_emptified( $_value, $emptyStringIsNull );
            }
            catch ( \Exception $_ex )
            {
                //  Ignored
            }
        }

        //  If we didn't have direct access, try and use a getter/setter
        if ( method_exists( $options, 'get' . $_key ) || method_exists( $options, 'is' . $_key ) )
        {
            $_type = method_exists( $options, 'is' . $_key ) ? 'is' : 'get';

            try
            {
                $_value = $options->{$_type . $_key}();
                $unsetValue && static::_objectRemove( $options, $_key, true, true );

                return static::_emptified( $_value, $emptyStringIsNull );
            }
            catch ( \Exception $_ex )
            {
                //  Ignored
            }
        }

        return static::_emptified( $defaultValue );
    }

    //******************************************************************************
    //* Set Methods
    //******************************************************************************

    /**
     * Sets a single $key to $value
     *
     * @param array|\ArrayAccess|object $options           The target array/object of the operation
     * @param string|array              $key               The array key or property name to set
     * @param mixed                     $value             The value to set
     * @param bool                      $emptyStringIsNull If true, empty() values will always be set as NULL.
     *
     * @return bool[]|bool False if the value could not be set. True otherwise. If $key is an array, array of bool is
     *                     returned indexed by $key.
     */
    public static function set( &$options = array(), $key, $value = null, $emptyStringIsNull = false )
    {
        //	Get many?
        if ( is_array( $key ) || $key instanceof \Traversable )
        {
            return static::setMany( $options, $key, $emptyStringIsNull );
        }

        switch ( gettype( $options ) )
        {
            case $options instanceof \ArrayAccess:
            case 'array':
                return static::_arraySet( $options, $key, $value, $emptyStringIsNull );

            case 'object':
                return static::_objectSet( $options, $key, $value, $emptyStringIsNull );
        }

        return false;
    }

    /**
     * @param array|object|\ArrayAccess $target            Target in which to set $data
     * @param array |\Traversable       $data              Array of $key => $value pairs to set
     * @param bool                      $emptyStringIsNull If true, empty() values will always return as NULL
     *
     * @return bool[]
     */
    public static function setMany( &$target, $data, $emptyStringIsNull = false )
    {
        $_results = array();

        foreach ( $data as $_key => $_value )
        {
            $_results[$_key] = static::set( $target, $_key, $_value, $emptyStringIsNull );
        }

        return $_results;
    }

    /**
     * Sets an value in the given array at key.
     *
     * @param array|\ArrayAccess $target            The target array of set
     * @param string|array       $key               The key to set
     * @param mixed              $value             The value to set
     * @param bool               $emptyStringIsNull If true, empty() values will always be set as NULL.
     * @param bool               $cleanedKey        Set to true if you're passing in a cleaned key
     *
     * @return bool True if key existed before operation, false otherwise
     */
    protected static function _arraySet( &$target = array(), $key, $value = null, $emptyStringIsNull = false, $cleanedKey = false )
    {
        $_key = $cleanedKey ? $key : static::_cleanKey( $key );
        $_exists = array_key_exists( $_key, $target );

        $target[$_key] = static::_emptified( $value, $emptyStringIsNull );

        return $_exists;
    }

    /**
     * Sets property in an object directly or via setter
     *
     * @param array|object $options           The target object
     * @param string       $key               The array index or property to set
     * @param mixed        $value             The value to set
     * @param bool         $emptyStringIsNull If true, empty() values will always be set as NULL.
     * @param bool         $cleanedKey        Set to true if you're passing in a cleaned key
     *
     * @return bool True if property was able to be set, false otherwise
     */
    protected static function _objectSet( &$options = array(), $key, $value = null, $emptyStringIsNull = false, $cleanedKey = false )
    {
        $_key = $cleanedKey ? $key : static::_cleanKey( $key );

        if ( property_exists( $options, $_key ) )
        {
            try
            {
                $options->{$_key} = static::_emptified( $value, $emptyStringIsNull );

                return true;
            }
            catch ( \Exception $_ex )
            {
                //  Ignored
            }
        }

        //  If we didn't have direct access, try and use a getter/setter
        if ( method_exists( $options, 'set' . $_key ) )
        {
            try
            {
                call_user_func(
                    array($options, 'set' . $_key),
                    static::_emptified( $value, $emptyStringIsNull )
                );

                return true;
            }
            catch ( \Exception $_ex )
            {
                //  Ignored
            }
        }

        return false;
    }

    //******************************************************************************
    //* Remove Methods
    //******************************************************************************

    /**
     * Unsets an option in the given array/object
     *
     * @param array|object $options
     * @param string       $key
     * @param bool         $cleanedKey Set to true if you're passing in a cleaned key
     *
     * @return bool True if $key was found and removed.
     */
    public static function remove( &$options = array(), $key, $cleanedKey = false )
    {
        //	Get many?
        if ( is_array( $key ) || $key instanceof \Traversable )
        {
            return static::removeMany( $options, $key );
        }

        $_key = $cleanedKey ? $key : static::_cleanKey( $key );

        switch ( gettype( $options ) )
        {
            case $options instanceof \ArrayAccess:
            case 'array':
                return static::_arrayRemove( $options, $_key, true );

            case 'object':
                return static::_objectRemove( $options, $key, true );
        }

        return false;
    }

    /**
     * @param array|object|\ArrayAccess $target Target from which to remove keys
     * @param array|\Traversable        $keys   Array of keys to remove
     *
     * @return bool[]
     */
    public static function removeMany( &$target, $keys )
    {
        $_results = array();

        foreach ( $keys as $_key )
        {
            $_results[$_key] = static::remove( $target, $_key );
        }

        return $_results;
    }

    /**
     * @param array|\ArrayAccess $array
     * @param string             $key
     * @param bool               $cleanedKey Set to true if you're passing in a cleaned key
     *
     * @return bool
     */
    protected static function _arrayRemove( &$array, $key, $cleanedKey = false )
    {
        $_key = $cleanedKey ? $key : static::_cleanKey( $key );

        if ( array_key_exists( $_key, $array ) )
        {
            unset( $array[$_key] );

            return true;
        }

        return false;
    }

    /**
     * @param object $object
     * @param string $key
     * @param bool   $cleanedKey Set to true if you're passing in a cleaned key
     * @param bool   $viaSetter  If true, assume a setter exists
     *
     * @return bool
     */
    protected static function _objectRemove( &$object, $key, $cleanedKey = false, $viaSetter = false )
    {
        $_key = $cleanedKey ? $key : static::_cleanKey( $key );

        if ( $viaSetter || method_exists( $object, 'set' . $_key ) )
        {
            try
            {
                //  Hide set failures with @
                @call_user_func( array($object, 'set' . $_key), null );

                return true;
            }
            catch ( \Exception $_ex )
            {
                //  Ignored
            }
        }

        try
        {
            //  Try and unset the value directly
            @$_result = function ( &$object, $_key )
            {
                unset( $object->{$_key} );
            };

            return true;
        }
        catch ( \Exception $_ex )
        {
            //  set to null if possible
            try
            {
                $object->{$_key} = null;
            }
            catch ( \Exception $_ex )
            {
                //  Ignored
            }
        }

        return false;
    }

    //******************************************************************************
    //* Utility Methods
    //******************************************************************************

    /**
     * @param array  $options
     * @param string $key
     * @param bool   $cleanedKey Set to true if you're passing in a cleaned key
     *
     * @return bool
     */
    public static function contains( $options = array(), $key, $cleanedKey = false )
    {
        $_key = $cleanedKey ? $key : static::_cleanKey( $key );

        return
            ( is_array( $options ) && array_key_exists( $_key, $options ) ) ||
            ( is_object( $options ) && property_exists( $options, $_key ) );
    }

    /**
     * Ensures the argument passed in is actually an array with optional iteration callback
     *
     * @param array             $array
     * @param callable|\Closure $callback
     *
     * @return array
     */
    public static function clean( $array = null, $callback = null )
    {
        $_result = ( empty( $array ) ? array() : ( !is_array( $array ) ? array($array) : $array ) );

        if ( !is_callable( $callback ) )
        {
            return $_result;
        }

        $_response = array();

        foreach ( $_result as $_item )
        {
            $_response[] = call_user_func( $callback, $_item );
        }

        return $_response;
    }

    /**
     * If $key is NOT an array, converts the arguments $key and $value to array($key => $value).
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return array
     */
    public static function collapse( $key, $value = null )
    {
        return
            ( is_array( $key ) && null === $value )
                ? $key
                : array($key => $value);
    }

    /**
     * Merge one or more arrays but ensures each is an array. Basically an idiot-proof array_merge
     *
     * @param array $target The destination array
     *
     * @return array The resulting array
     * @return array
     */
    public static function merge( $target )
    {
        $_arrays = static::clean( func_get_args() );
        $_target = static::clean( array_shift( $_arrays ) );

        foreach ( $_arrays as $_array )
        {
            $_target = array_merge(
                $_target,
                static::clean( $_array )
            );

            unset( $_array );
        }

        unset( $_arrays );

        return $_target;
    }

    /**
     * Wrapper for a static::get on $_SERVER
     *
     * @param string $key
     * @param string $defaultValue
     * @param bool   $unsetValue
     *
     * @return mixed
     */
    public static function server( $key, $defaultValue = null, $unsetValue = false )
    {
        return isset( $_SERVER ) ? static::get( $_SERVER, $key, $defaultValue, $unsetValue ) : $defaultValue;
    }

    /**
     * Wrapper for a static::get on $_REQUEST
     *
     * @param string $key
     * @param string $defaultValue
     * @param bool   $unsetValue
     *
     * @return mixed
     */
    public static function request( $key, $defaultValue = null, $unsetValue = false )
    {
        return isset( $_REQUEST ) ? static::get( $_REQUEST, $key, $defaultValue, $unsetValue ) : $defaultValue;
    }

    /**
     * Sets a value within an object or array, only if the value is not set (SetIfNotSet=SINS).
     * You may pass in an array of key value pairs to do many at once.
     *
     * @param object|array $options
     * @param string|array $key
     * @param mixed        $value
     *
     * @return bool[]|bool
     */
    public static function sins( &$options, $key, $value = null )
    {
        $_singleton = false;

        //	Accept an array as input or single KVP
        if ( !is_array( $key ) )
        {
            $key = array($key => $value);
            $_singleton = true;
        }

        $_results = array();

        foreach ( $key as $_key => $_value )
        {
            if ( !static::contains( $options, $_key ) )
            {
                $_results[$_key] = static::set( $options, $_key, $_value );
            }
        }

        return $_singleton ? current( $_results ) : $_results;
    }

    /**
     * Spins through an array and prefixes the keys with a string
     *
     * @param string $prefix
     * @param array  $data
     *
     * @return mixed
     */
    public static function prefixKeys( $prefix, array $data = array() )
    {
        foreach ( static::clean( $data ) as $_key => $_value )
        {
            if ( is_numeric( $_key ) )
            {
                continue;
            }

            if ( is_array( $_value ) )
            {
                $_value = static::prefixKeys( $prefix, $_value );
            }

            $data[$prefix . $_key] = $_value;
            unset( $data[$_key] );
        }

        return $data;
    }

    //******************************************************************************
    //* Private Methods
    //******************************************************************************

    /**
     * Converts key to a neutral format if not already...
     *
     * @param string $key
     * @param bool   $opposite If true, the key is switched back to it's neutral or non-neutral format
     *
     * @return string
     */
    protected static function _cleanKey( $key, $opposite = true )
    {
        if ( !static::$_neutralizeKeys )
        {
            return $key;
        }

        if ( $key == ( $_cleaned = Inflector::neutralize( $key ) ) )
        {
            if ( false !== $opposite )
            {
                return Inflector::deneutralize( $key, true );
            }
        }

        return $_cleaned;
    }

    /**
     * @param mixed $value
     * @param bool  $emptyStringIsNull If true, empty() values will always return as NULL
     *
     * @return mixed
     */
    protected static function _emptified( $value, $emptyStringIsNull = false )
    {
        return
            ( ( $emptyStringIsNull || static::$_emptyStringEqualsNull ) && is_string( $value ) && empty( $value ) )
                ? null
                : $value;
    }

    //******************************************************************************
    //* Static Getters/Setters
    //******************************************************************************

    /**
     * @return boolean
     */
    public static function getEmptyStringEqualsNull()
    {
        return static::$_emptyStringEqualsNull;
    }

    /**
     * @param boolean $emptyStringEqualsNull
     */
    public static function setEmptyStringEqualsNull( $emptyStringEqualsNull )
    {
        static::$_emptyStringEqualsNull = $emptyStringEqualsNull;
    }

    /**
     * @return boolean
     */
    public static function getNeutralizeKeys()
    {
        return static::$_neutralizeKeys;
    }

    /**
     * @param boolean $neutralizeKeys
     */
    public static function setNeutralizeKeys( $neutralizeKeys )
    {
        static::$_neutralizeKeys = $neutralizeKeys;
    }

}
