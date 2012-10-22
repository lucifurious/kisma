<?php
/**
 * ProviderLike.php
 */
namespace Kisma\Core\Interfaces;
/**
 * ProviderLike
 * Provides something as a service
 */
interface ProviderLike
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Provides the service requested.
	 *
	 * @param mixed $request
	 *
	 * @return mixed
	 */
	public function provide( $request );

}
