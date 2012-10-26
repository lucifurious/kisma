<?php
/**
 * Ssh.php
 */
namespace Kisma\Core\Services\Network;
/**
 * Ssh
 */
class Ssh extends \Kisma\Core\Services\DeliveryService
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function deliver( $payload, $consumer )
	{
		throw new \Kisma\Core\Exceptions\NotImplementedException();
	}
}
