<?php
/**
 * Email.php
 */
namespace Kisma\Services\Network;
/**
 * Email
 */
class Email extends \Kisma\Core\Services\DeliveryService
{
	//**************************************************************************
	//* Methods
	//**************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function deliver( $payload, $consumer )
	{
		throw new \Kisma\Core\Exceptions\NotImplementedException();
	}
}
