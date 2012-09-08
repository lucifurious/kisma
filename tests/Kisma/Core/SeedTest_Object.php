<?php
/**
 * SeedTest_Object
 */
class SeedTest_Object extends \Kisma\Core\Seed implements \Kisma\Core\Interfaces\Subscriber
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

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function onAfterConstruct( $event = null )
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