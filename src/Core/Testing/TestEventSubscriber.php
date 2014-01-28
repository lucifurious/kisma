<?php
namespace Kisma\Core\Testing;

use Kisma\Core\Interfaces\SubscriberLike;
use Kisma\Core\Seed;

/**
 * TestEventSubscriber
 */
class TestEventSubscriber extends Seed implements SubscriberLike
{
	//*************************************************************************
	//	Members
	//*************************************************************************

	/**
	 * @var BaseTestCase
	 */
	protected $_testCase = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Testing\BaseTestCase $testCase
	 * @param array                            $settings
	 */
	public function __construct( BaseTestCase $testCase, $settings = array() )
	{
		$this->_testCase = $testCase;
		parent::__construct( $settings );
	}

	/**
	 * {@InheritDoc}
	 */
	public function onAfterConstruct( $event = null )
	{
		return $this->_testCase->eventFired( 'after_construct' );
	}

	/**
	 * @param Events $event
	 *
	 * @return $this
	 */
	public function onBeforeDestruct( $event )
	{
		return $this->_testCase->eventFired( 'before_destruct' );
	}

	/**
	 * @param BaseTestCase $testCase
	 *
	 * @return SeedTestSubscriber
	 */
	public function setTestCase( $testCase )
	{
		$this->_testCase = $testCase;

		return $this;
	}

	/**
	 * @return BaseTestCase
	 */
	public function getTestCase()
	{
		return $this->_testCase;
	}

}