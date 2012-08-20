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
	const BeforePublish = 'kisma.core.publisher.before_publish';
	/**
	 * @var string
	 */
	const AfterPublish = 'kisma.core.publisher.after_publish';

}
