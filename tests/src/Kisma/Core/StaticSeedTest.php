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

    public function testConstruct()
    {
        $_object = new StaticSeedTest_Object();

        $this->assertTrue( 11 === StaticSeedTest_Object::add( 5, 6 ) );

        //  Kill object
        $_counts = StaticSeedTest_Object::destroy();

        $this->assertTrue( $_counts['_construct'] > 0 );
        $this->assertTrue( $_counts['_destruct'] > 0 );
        $this->assertTrue( $_counts['_wakeup'] > 0 );
        $this->assertTrue( $_counts['_sleep'] > 0 );
    }

}
