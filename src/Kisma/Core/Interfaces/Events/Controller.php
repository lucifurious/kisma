<?php
/**
 * Controller.php
 */
namespace Kisma\Core\Interfaces\Events;
/**
 * Controller
 * Events for Controllers
 */
interface Controller
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const NewRequest = 'kisma.core.Controller.new_request';
	/**
	 * @var string
	 */
	const AfterDispatch = 'kisma.core.Controller.after_dispatch';

}
