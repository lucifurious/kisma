<?php
/**
 * SeedLike.php
 */
namespace Kisma\Core\Interfaces;
/**
 * SeedLike
 * All seeds have this
 */
interface SeedLike extends \Kisma\Core\Interfaces\Events\SeedLike
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
