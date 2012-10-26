<?php
/**
 * Ftp.php
 */
namespace Kisma\Core\Services\Network;
/**
 * Ftp
 */
class Ftp extends \Kisma\Core\Services\DeliveryService
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
