<?php
/**
 * Publisher.php
 */
namespace Kisma\Core\Interfaces\Events;
/**
 * Publisher
 * Events for event publishers
 */
interface Publisher extends \Kisma\Core\Interfaces\Publisher
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const BeforePublish = 'kisma.route.before_publish';
	/**
	 * @var string
	 */
	const AfterPublish = 'kisma.route.after_publish';

}
