<?php
/**
 * PublisherLike.php
 */
namespace Kisma\Core\Interfaces\Events;
/**
 * PublisherLike
 * Events for event publishers
 */
interface PublisherLike
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
