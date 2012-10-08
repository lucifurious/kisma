<?php
namespace Kisma\Core;

require_once __DIR__ . '/SeedTest_Object.php';

/**
 */
class SeedTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \SeedTest_Object
	 */
	protected $_object;
	/**
	 * @var int
	 */
	protected $_destructorEventFired = 0;

	/**
	 * @param int $how Used to capture the destructor event in the fixture
	 */
	public function destructorEventFired( $how = 0 )
	{
		$this->_destructorEventFired += ( empty( $how ) ? 0 : 1 );
	}

	/**
	 * Creates our test object
	 */
	protected function setUp()
	{
		if ( null !== $this->_object )
		{
			unset( $this->_object );
		}

		$this->_object = new \SeedTest_Object(
			array(
				'tester' => $this
			)
		);
	}

	protected function tearDown()
	{
		$this->_object = null;

//		foreach ( \Kisma\Core\Utility\EventManager::getEventMap() as $_eventTag => $_subscribers )
//		{
//			\Kisma\Core\Utility\Log::debug( 'Event "' . $_eventTag . '" listener dump:' );
//
//			foreach ( $_subscribers as $_listeners )
//			{
//				/** @var $_listener \Closure */
//				foreach ( $_listeners as $_subscriberId => $_closures )
//				{
//					foreach ( $_closures as $_closure )
//					{
//						\Kisma\Core\Utility\Log::debug( '-- "' . $_subscriberId . '" of ' . ( is_object( $_closure ) ? get_class( $_closure ) :
//							gettype( $_closure ) ) );
//					}
//				}
//			}
//		}

//		\Kisma\Core\Utility\Log::debug( 'Eventmap dump:' . json_encode( \Kisma\Core\Utility\EventManager::getEventMap() ) );
	}

	/**
	 * @covers Kisma\Core\Seed::__destruct
	 */
	public function testOnBeforeDestruct()
	{
		$this->_destructorEventFired = 0;
		$this->_object->__destruct();
		$this->assertTrue( $this->_destructorEventFired > 0, 'Destructor event was not fired.' );
	}

	/**
	 * @covers Kisma\Core\Seed::__wakeup
	 * @covers Kisma\Core\Seed::__construct
	 * @covers Kisma\Core\Seed::publish
	 */
	public function testOnAfterConstruct()
	{
		$this->assertTrue( false !== $this->_object->constructEvent );
	}

	/**
	 * @covers Kisma\Core\Seed::getId
	 */
	public function testGetId()
	{
		$this->assertNotEmpty(
			$_id = $this->_object->getId(),
			'The object ID has not been set properly.'
		);
	}

	/**
	 * @covers Kisma\Core\Seed::getTag
	 * @covers Kisma\Core\Seed::setTag
	 */
	public function testGetTag()
	{
		$this->assertTrue( is_string( $this->_object->getTag() ) );
		$this->_object->setTag( 'new_tag' );
		$this->assertTrue( 'new_tag' == $this->_object->getTag() );
	}

	/**
	 * @covers Kisma\Core\Seed::getName
	 * @covers Kisma\Core\Seed::setName
	 */
	public function testGetName()
	{
		$this->assertTrue( is_string( $this->_object->getName() ) );
		$this->_object->setName( 'new_name' );
		$this->assertTrue( 'new_name' == $this->_object->getName() );
	}

	/**
	 * @covers Kisma\Core\Seed::setEventManager
	 * @covers Kisma\Core\Seed::getEventManager
	 */
	public function testGetEventManager()
	{
		$this->assertTrue( is_string( $this->_object->getEventManager() ) );
		$this->_object->setEventManager( false );
		$this->assertTrue( false === $this->_object->getEventManager() );
	}

	/**
	 * @covers Kisma\Core\Seed::getDiscoverEvents
	 * @covers Kisma\Core\Seed::setDiscoverEvents
	 */
	public function testDiscoverEvents()
	{
		$this->assertTrue( $this->_object->getDiscoverEvents() );
		$this->_object->setDiscoverEvents( false );
		$this->assertTrue( false === $this->_object->getDiscoverEvents() );
	}

	/**
	 * @covers Kisma\Core\Seed::subscribe
	 * @covers Kisma\Core\Seed::unsubscribe
	 */
	public function testUnsubscribe()
	{
		$_eventFired = false;

		//	Subscribe and publish to set flag
		$this->_object->subscribe(
			'crazy.event',
			function ( $event ) use ( &$_eventFired )
			{
				$_eventFired = true;
			}
		);
		$this->_object->publish( 'crazy.event' );
		$this->assertTrue( $_eventFired );

		//	Clear, unsub, and publish. Flag should not be set...
		$_eventFired = false;
		$this->_object->unsubscribe( 'crazy.event' );
		$this->_object->publish( 'crazy.event' );

		$this->assertTrue( false === $_eventFired );
	}
}