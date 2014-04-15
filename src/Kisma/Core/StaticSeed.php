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
namespace Kisma\Core;

/**
 * StaticSeed
 * A seed that can be used as a base class for your static classes.
 * It provides an automatic static constructor and destructor in
 * addition to standard seed event management.
 */
class StaticSeed extends Seed
{
    //********************************************************************************
    //* Variables
    //********************************************************************************

    /**
     * @var static
     */
    protected static $_instance = null;

    //********************************************************************************
    //* Methods
    //********************************************************************************

    /**
     * Initializes this static object. Consider it your static version of __construct()
     *
     * @param bool $returnNewObject If true, returns a new instance of this class
     *
     * @return \Kisma\Core\StaticSeed
     */
    public static function initialize( $returnNewObject = false )
    {
        //  Initialize your static stuff here
        if ( false !== $returnNewObject )
        {
            return new static();
        }
    }

    /**
     * @param StaticSeed $instance
     *
     * @return \Kisma\Core\StaticSeed
     */
    protected static function _construct( $instance )
    {
        if ( null !== static::$_instance && $instance === static::$_instance )
        {
            return static::$_instance;
        }

        static::_wakeup();

        return static::$_instance;
    }

    /**
     * Public destructor of static object
     */
    public static function destroy()
    {
        if ( null === static::$_instance )
        {
            return;
        }

        static::$_instance->__destruct();
        static::_destruct();
    }

    /**
     * Static __wakeup
     *
     * Code placed in your override of this method will be run after the instance's __wakeup method has been called.
     */
    protected static function _wakeup()
    {
        //  Shake yer booty
        if ( null === static::$_instance )
        {
            return;
        }
    }

    /**
     * Static destructor
     *
     * Code placed in your override of this method will be run at destruction.
     * Must call parent in there too.
     */
    protected static function _destruct()
    {
        //  Shake yer booty
        if ( null === static::$_instance )
        {
            return;
        }

        //  Snoozyland...
        static::_sleep();

        //  And remove the instance...
        static::$_instance = null;
    }

    /**
     * Static _sleep
     *
     * Called when the instance is destroyed.
     *
     * Returns an array representation of the object data for serialization/storage
     *
     * @return array
     */
    protected static function _sleep()
    {
        //  Take a nap
        return array();
    }

    //*************************************************************************
    //	The Guts
    //*************************************************************************

    /**
     * Relays any inbound undefined static method calls to the underlying instance object
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function __callStatic( $name, $arguments )
    {
        if ( null !== static::getInstance() && method_exists( static::$_instance, $name ) )
        {
            return call_user_func_array( array( static::$_instance, $name ), $arguments );
        }
    }

    /**
     * Returns the static instance
     *
     * @return StaticSeed
     */
    public static function getInstance()
    {
        if ( null === static::$_instance )
        {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * @param array $settings
     */
    public function __construct( $settings = array() )
    {
        //  Wire up a destructor to call when I die
        register_shutdown_function(
            function ()
            {
                $this->__destruct();
                static::_destruct();
            }
        );

        //  Phone home. Calls __wakeup() from base
        parent::__construct( $settings );

        //  Call static constructor and save our instance off
        static::_construct( $this );
        static::$_instance = $this;
    }

}

//  Call the constructor
StaticSeed::initialize();