<?php
/**
 * Seed.php
 */
namespace Kisma\Core\Interfaces;
/**
 * Seed
 * All seeds have this
 *
 * @deprecated Please use SeedLike instead
 */
interface Seed extends \Kisma\Core\Interfaces\Events\Seed
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @return string
	 */
	public function getId();

	/**
	 * @return string
	 */
	public function getTag();

	/**
	 * @return string
	 */
	public function getName();
}
