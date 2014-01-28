<?php
namespace Kisma\Core;

use Kisma\Core\Testing\BaseTestCase;
use Kisma\Core\Testing\TestEventSubscriber;
use Kisma\Core\Utility\EventManager;
use Kisma\Core\Utility\Log;

/**
 */
class SeedTest extends BaseTestCase
{
	//*************************************************************************
	//	Members
	//*************************************************************************

	/**
	 * @var TestEventSubscriber
	 */
	protected $_object;

	//*************************************************************************
	//	Methods
	//*************************************************************************

	/**
	 * Creates our test object
	 */
	protected function setUp()
	{
		$this->_object = new TestEventSubscriber( $this );
	}

	protected function tearDown()
	{
		$this->_object = null;

		foreach ( EventManager::getEventMap() as $_eventTag => $_subscribers )
		{
			Log::debug( 'Event "' . $_eventTag . '" listener dump:' );

			foreach ( $_subscribers as $_listeners )
			{
				/** @var $_listener \Closure */
				foreach ( $_listeners as $_subscriberId => $_closures )
				{
					foreach ( $_closures as $_closure )
					{
						Log::debug(
							'-- "' . $_subscriberId . '" of ' . ( is_object( $_closure ) ? get_class( $_closure ) : gettype( $_closure ) )
						);
					}
				}
			}
		}

		Log::debug( '$eventMap dump:' . json_encode( EventManager::getEventMap() ) );
	}

	/**
	 * @covers Kisma\Core\Seed::__destruct
	 */
	public function testOnBeforeDestruct()
	{
		unset( $this->_object );

		$this->assertNotEquals( false, $this->wasFired( 'before_destruct' ) );
	}

	/**
	 * @covers Kisma\Core\Seed::__wakeup
	 * @covers Kisma\Core\Seed::__construct
	 * @covers Kisma\Core\Seed::publish
	 */
	public function testOnAfterConstruct()
	{
		$this->assertNotEquals( false, $this->wasFired( 'after_construct' ) );
	}

	/**
	 * @covers Kisma\Core\Seed::getId
	 */
	public function testGetId()
	{
		$this->assertNotEmpty(
			$this->_object->getId(),
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
	 * @covers Kisma\Core\Seed::publish
	 */
	public function testUnsubscribe()
	{
		$_eventFired = false;

		//	Subscribe and publish to set flag
		$this->_object->on(
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
		$this->_object->on( 'crazy.event', null );
		$this->_object->publish( 'crazy.event' );

		$this->assertTrue( false === $_eventFired );
	}
}
