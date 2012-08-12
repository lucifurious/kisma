<?php
/**
 * StorageEvents.php
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2012 Jerry Ablan
 * @license   http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author    Jerry Ablan <get.kisma@gmail.com>
 */
namespace Kisma\Core\Interfaces;

/**
 * StorageEvents
 * Defines an interface for storage service events
 */
interface StorageEvents
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const BeforeFind = 'kisma.core.storage.before_find';
	/**
	 * @var string
	 */
	const AfterFind = 'kisma.core.storage.after_find';
	/**
	 * @var string
	 */
	const BeforeSave = 'kisma.core.storage.before_save';
	/**
	 * @var string
	 */
	const AfterSave = 'kisma.core.storage.after_save';
	/**
	 * @var string
	 */
	const BeforeDelete = 'kisma.core.storage.before_delete';
	/**
	 * @var string
	 */
	const AfterDelete = 'kisma.core.storage.after_delete';

}
