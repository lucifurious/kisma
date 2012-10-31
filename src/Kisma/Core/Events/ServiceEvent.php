<?php
/**
 * ServiceEvent.php
 */
namespace Kisma\Core\Events;
/**
 * ServiceEvent
 * An event that is consumed by a service. Merely enforces the $request argument
 *
 * @method \Kisma\Core\Interfaces\ConsumerLike getSource()
 */
class ServiceEvent extends SeedEvent
{
	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * Enforces types...
	 *
	 * @param \Kisma\Core\Interfaces\ConsumerLike    $source
	 * @param \Kisma\Core\Interfaces\RequestLike     $request
	 */
	public function __construct( \Kisma\Core\Interfaces\ConsumerLike $source, \Kisma\Core\Interfaces\RequestLike $request = null )
	{
		parent::__construct( $source, $request );
	}

}
