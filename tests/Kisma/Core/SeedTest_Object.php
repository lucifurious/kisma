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
	 * @param array                $settings
	 * @param \Kisma\Core\SeedTest $tester
	 *
	 * @return \SeedTest_Object
	 */
	public function __construct( $settings = array(), $tester = null )
	{
		parent::__construct(
			array(
				'itemOne'   => 1,
				'itemTwo'   => 2,
				'itemThree' => 3,
			)
		);

		$this->tester = $tester;
	}

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
