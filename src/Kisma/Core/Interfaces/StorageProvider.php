<?php
/**
 * StorageProvider.php
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
 * StorageProvider
 * Defines an interface for storage providers
 */
interface StorageProvider
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param string|array|null $key
	 * @param mixed             $defaultValue
	 * @param bool              $burnAfterReading If true, after retrieving a value from storage, it should be removed
	 *
	 * @return mixed
	 */
	public function get( $key = null, $defaultValue = null, $burnAfterReading = false );

	/**
	 * @param string|array      $key
	 * @param mixed             $value
	 * @param bool              $overwrite If true, any existing value will be overwritten
	 *
	 * @return StorageProvider
	 */
	public function set( $key, $value = null, $overwrite = true );

}
