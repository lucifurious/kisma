<?php
/**
 * KeyValueStore.php
 */
namespace Kisma\Core\Interfaces\Services;
/**
 * KeyValueStore
 * Defines an interface for storage providers
 */
interface KeyValueStore extends \Kisma\Core\Interfaces\Service
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param string|array|null $key
	 * @param mixed             $defaultValue
	 * @param bool              $burnAfterReading If true, after retrieving a value from storage, it should be removed
	 *
	 * @return mixed
	 */
	public function get( $key = null, $defaultValue = null, $burnAfterReading = false );

	/**
	 * @param string|array      $key
	 * @param mixed             $value
	 * @param bool              $overwrite If true, any existing value will be overwritten
	 *
	 * @return KeyValueStore
	 */
	public function set( $key, $value = null, $overwrite = true );

	/**
	 * @param string|array|null $key
	 */
	public function remove( $key = null );

}
