<?php
/**
 * SeedTest_Object
 */
class SeedTest_Object extends \Kisma\Core\Seed
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @return array
	 */
	public function getDefaultAttributes()
	{
		return array_merge(
			array(
				'itemOne'   => 1,
				'itemTwo'   => 2,
				'itemThree' => 3,
			),
			parent::getDefaultAttributes()
		);
	}

}
