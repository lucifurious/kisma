<?php
namespace Kisma\Core\Tests;

use Kisma\Core\StaticSeedTest_Object;

require_once __DIR__ . '/StaticSeedTest_Object.php';

/**
 * StaticSeedTest
 */
class StaticSeedTest extends \PHPUnit_Framework_TestCase
{
    public $calledMethods = array();

    /**
     * @covers \Kisma\Core\StaticSeed::_construct
     * @covers \Kisma\Core\StaticSeed::_destruct
     * @covers \Kisma\Core\StaticSeed::_wakeup
     * @covers \Kisma\Core\StaticSeed::_sleep
     * @covers \Kisma\Core\StaticSeed::__construct
     * @covers \Kisma\Core\StaticSeed::__destruct
     * @covers \Kisma\Core\StaticSeed::__wakeup
     * @covers \Kisma\Core\StaticSeed::__sleep
     * @covers \Kisma\Core\StaticSeed::destroy
     * @covers \Kisma\Core\StaticSeed::getInstance
     * @covers \Kisma\Core\StaticSeed::initialize
     */
    public function testStaticMagicMethods()
    {
//        $_object = new StaticSeedTest_Object();

        $this->assertTrue( 18 === StaticSeedTest_Object::getInstance()->add( 5, 6, 7 ) );

        //  Kill object
        $_counts = StaticSeedTest_Object::destroy();

        $this->assertTrue( 1 === $_counts['_construct'] );
        $this->assertTrue( 1 === $_counts['_destruct'] );
        $this->assertTrue( 1 === $_counts['_wakeup'] );
        $this->assertTrue( 1 === $_counts['_sleep'] );
    }

}
