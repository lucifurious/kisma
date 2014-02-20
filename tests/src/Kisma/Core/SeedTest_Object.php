<?php
namespace Kisma\Core;

use Kisma\Core\Interfaces\SubscriberLike;

/**
 * SeedTest_Object
 */
class SeedTest_Object extends Seed implements SubscriberLike
{
	//*************************************************************************
	//* Public Members
	//*************************************************************************

	/**
	 * @var bool
	 */
	public $constructEvent = false;
	/**
	 * @var bool
	 */
	public $destructEvent = false;
	/**
	 * @var \Kisma\Core\SeedTest
	 */
	public $tester = null;
	/**
	 * @var int
	 */
	public static $counter = 0;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function onAfterConstruct( $event = null, $event, $dispatcher )
	{
		return $this->constructEvent = true;
	}

	/**
	 * {@InheritDoc}
	 */
	public function onBeforeDestruct( $event = null )
	{
		$this->tester->destructorEventFired( 1 );

		return true;
	}
}