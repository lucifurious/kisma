<?php
/**
 * Dispatcher.php
 */
namespace Kisma\Core\Interfaces\Events;
/**
 * Dispatcher
 * Events for dispatchers
 */
interface Dispatcher extends \Kisma\Core\Interfaces\Dispatcher
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const NewRequest = 'kisma.core.dispatcher.new_request';
	/**
	 * @var string
	 */
	const AfterDispatch = 'kisma.core.dispatcher.after_dispatch';

}
