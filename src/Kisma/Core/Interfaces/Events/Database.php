<?php
/**
 * Database.php
 */
namespace Kisma\Core\Interfaces\Events;
/**
 * Database
 * Defines an interface for database service events
 */
interface Database
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const BeforeFind = 'kisma.core.database.before_find';
	/**
	 * @var string
	 */
	const AfterFind = 'kisma.core.database.after_find';
	/**
	 * @var string
	 */
	const BeforeSave = 'kisma.core.database.before_save';
	/**
	 * @var string
	 */
	const AfterSave = 'kisma.core.database.after_save';
	/**
	 * @var string
	 */
	const BeforeDelete = 'kisma.core.database.before_delete';
	/**
	 * @var string
	 */
	const AfterDelete = 'kisma.core.database.after_delete';
}
