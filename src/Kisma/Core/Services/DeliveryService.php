<?php
/**
 * DeliveryService.php
 */
namespace Kisma\Core\Services;
/**
 * DeliveryService
 * Base for classes that deliver things
 */
abstract class DeliveryService extends SeedService
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @return mixed|void
	 */
	public function perform()
	{
		$_result = $this->deliver();

		$this->publish( ( $_result->success() ? static::Success : static::Failure ), $_result );

		//	Call parent implementation to raise complete event
		parent::perform();
	}

	/**
	 * @return \Kisma\Core\Interfaces\ResponseLike
	 */
	abstract public function deliver();
}
