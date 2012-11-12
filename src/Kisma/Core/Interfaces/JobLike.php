<?php
/**
 * JobLike.php
 */
namespace Kisma\Core\Interfaces;
/**
 * JobLike
 */
interface JobLike extends ServiceLike
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Performs the job
	 *
	 * @return mixed
	 */
	public function perform();
}
