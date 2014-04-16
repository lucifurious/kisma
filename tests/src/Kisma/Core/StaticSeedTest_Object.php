<?php
namespace Kisma\Core;

/**
 * StaticSeedTest_Object
 */
class StaticSeedTest_Object extends StaticSeed
{
    //*************************************************************************
    //* Public Members
    //*************************************************************************

    /**
     * @var StaticSeedTest
     */
    public static $tester = null;
    /**
     * @var array
     */
    protected static $_calledMethods = array();

    //*************************************************************************
    //* Public Methods
    //*************************************************************************

    /**
     * @return array
     */
    public static function destroy()
    {
        static::_incrementCounter( 'destroy' );

        parent::destroy();

        return static::$_calledMethods;
    }

    /**
     * The static constructor
     *
     * @param array $settings
     *
     * @return \Kisma\Core\StaticSeed
     */
    protected static function _construct( $settings = array() )
    {
        static::_incrementCounter( '_construct' );

        return parent::_construct( $settings );
    }

    public static function _destruct()
    {
        static::_incrementCounter( '_destruct' );

        parent::_destruct();
    }

    /**
     *
     */
    protected static function _wakeup()
    {
        static::_incrementCounter( '_wakeup' );

        parent::_wakeup();
    }

    /**
     * @return array
     */
    protected static function _sleep()
    {
        static::_incrementCounter( '_sleep' );

        return parent::_sleep();
    }

    /**
     * A dummy test function to trigger construction
     *
     * @return number
     * @throws \InvalidArgumentException
     */
    public static function add()
    {
        static::_incrementCounter( 'add' );

        $_values = func_get_args();

        if ( empty( $_values ) )
        {
            throw new \InvalidArgumentException( 'No values specified to add.' );
        }

        return array_sum( array_values( $_values ) );
    }

    /**
     * Increment the calledMethod counter for testing
     *
     * @param string $which
     * @param int    $howMany
     *
     * @return int
     */
    protected static function _incrementCounter( $which, $howMany = 1 )
    {
        if ( !isset( static::$_calledMethods[$which] ) )
        {
            static::$_calledMethods[$which] = 0;
        }

        return static::$_calledMethods[$which] += $howMany;
    }
}