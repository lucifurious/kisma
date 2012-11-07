<?php
/**
 * SeedResponse.php
 */
namespace Kisma\Core\Services;
/**
 * SeedResponse
 * A response for a service request
 */
class SeedResponse extends \Kisma\Core\SeedBag implements \Kisma\Core\Interfaces\ResponseLike
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var int The service status
	 */
	protected $_status = null;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Returns true if the service call was successful
	 *
	 * @return bool
	 */
	public function success()
	{
		return $this->_status == static::Success;
	}
}
