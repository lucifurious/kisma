<?php
/**
 * ServiceEvent.php
 */
namespace Kisma\Core\Events;
/**
 * ServiceEvent
 * An event that is consumed by a service. Merely enforces the $request argument
 */
class ServiceEvent extends SeedEvent
{
	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * Enforces types...
	 *
	 * @param \Kisma\Core\Interfaces\ServiceLike    $source
	 * @param \Kisma\Core\Interfaces\RequestLike    $request
	 */
	public function __construct( \Kisma\Core\Interfaces\ServiceLike $source, \Kisma\Core\Interfaces\RequestLike $request )
	{
		parent::__construct( $source, $request );
	}

}
