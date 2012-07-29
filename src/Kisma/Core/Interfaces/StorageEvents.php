<?php
/**
 * StorageEvents.php
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 *         Copyright (c) 2012 Silverpop Systems, Inc.
 *         http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 * @filesource
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
	const BeforeFind = 'cis.services.storage.before_find';
	/**
	 * @var string
	 */
	const AfterFind = 'cis.services.storage.after_find';
	/**
	 * @var string
	 */
	const BeforeSave = 'cis.services.storage.before_save';
	/**
	 * @var string
	 */
	const AfterSave = 'cis.services.storage.after_save';
	/**
	 * @var string
	 */
	const BeforeDelete = 'cis.services.storage.before_delete';
	/**
	 * @var string
	 */
	const AfterDelete = 'cis.services.storage.after_delete';

}
