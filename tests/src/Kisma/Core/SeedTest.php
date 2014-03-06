<?php
namespace Kisma\Core;

require_once __DIR__ . '/SeedTest_Object.php';

/**
 */
class SeedTest extends \PHPUnit_Framework_TestCase
{
	//*************************************************************************
	//	Members
	//*************************************************************************

	/**
	 * @var SeedTest_Object
	 */
	protected $_object;
	protected $_objectSettings = array(
		'tester'             => null,
		'enable_life_events' => true
	);
	/**
	 * @var int
	 */
	protected $_destructorEventFired = 0;

	//*************************************************************************
	//	Methods
	//*************************************************************************

	protected function setUp()
	{
		parent::setUp();

		$this->_objectSettings['tester'] = $this;
		$this->_object = new SeedTest_Object( $this->_objectSettings );
	}

	protected function tearDown()
	{
		unset( $this->_object );

		parent::tearDown();
	}

	/**
	 * @param int $how Used to capture the destructor event in the fixture
	 */
	public function destructorEventFired( $how = 0 )
	{
		$this->_destructorEventFired += ( empty( $how ) ? 0 : 1 );
	}

	/**
	 * @covers Kisma\Core\Seed::__destruct
	 */
	public function testOnBeforeDestruct()
	{
		//	Test destruct method
		$this->_destructorEventFired = 0;
		$_object = new SeedTest_Object( $this->_objectSettings );
		$_object->__destruct();
		$this->assertTrue( $this->_destructorEventFired > 0, 'Destructor (__destruct) event was not fired.' );

		//	Test unsetting
		$this->_destructorEventFired = 0;
		$_object = new SeedTest_Object( $this->_objectSettings );
		unset( $_object );
		$this->assertTrue( $this->_destructorEventFired > 0, 'Destructor (unset) event was not fired.' );
	}

	/**
	 * @covers Kisma\Core\Seed::__wakeup
	 * @covers Kisma\Core\Seed::__construct
	 */
	public function testOnAfterConstruct()
	{
		$_object = new SeedTest_Object( $this->_objectSettings );
		$this->assertTrue( false !== $_object->constructEvent );
		unset( $_object );
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

}
