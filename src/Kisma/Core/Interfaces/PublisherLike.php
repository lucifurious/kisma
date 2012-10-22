<?php
/**
 * PublisherLike.php
 */
namespace Kisma\Core\Interfaces;
/**
 * PublisherLike
 * Defines an object as being a publisher of things
 */
interface PublisherLike extends \Kisma\Core\Interfaces\Events\PublisherLike
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The default event manager for an object
	 */
	const DefaultEventManager = '\\Kisma\\Core\\Utility\\EventManager';

}
