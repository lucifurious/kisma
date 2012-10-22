<?php
/**
 * BagLike.php
 */
namespace Kisma\Core\Interfaces;
/**
 * BagLike
 * Something that can contain KVPs. Ya know, like a bag?
 */
interface BagLike extends \Kisma\Core\Interfaces\Events\SeedLike
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Retrieves a value at the given key location, or the default value if key isn't found.
	 * Setting $burnAfterReading to true will remove the key-value pair from the bag after it
	 * is retrieved. Call with no arguments to get back a KVP array of contents
	 *
	 * @param string $key
	 * @param mixed  $defaultValue
	 * @param bool   $burnAfterReading
	 *
	 * @throws \Kisma\Core\Exceptions\BagException
	 * @return mixed
	 */
	public function get( $key = null, $defaultValue = null, $burnAfterReading = false );

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param bool   $overwrite
	 *
	 * @throws \Kisma\Core\Exceptions\BagException
	 * @return SeedBag
	 */
	public function set( $key, $value, $overwrite = true );

}
