<?php
/**
 * WebDav.php
 */
namespace Kisma\Core\Services\Network;

/**
 * WebDav
 */
class WebDav extends \Kisma\Core\Services\DeliveryService
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
