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
	 * @param \Kisma\Core\Interfaces\Consumer  $source
	 * @param \Kisma\Core\Services\SeedRequest $request
	 */
	public function __construct( $source, \Kisma\Core\Services\SeedRequest $request )
	{
		parent::__construct( $source, $request );
	}

}
