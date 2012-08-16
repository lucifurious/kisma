<?php
/**
 * StorageService.php
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
 * StorageService
 * Defines an interface for storage services
 */
interface StorageService
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * When a service is constructed, this method is called by default
	 * Base initialization of storage. Child classes can override to use a database or document store
	 *
	 * @param array $options Any options you want passed to the service
	 *
	 * @return array|StorageProvider
	 */
	public function initializeStorage( $options = array() );

}
