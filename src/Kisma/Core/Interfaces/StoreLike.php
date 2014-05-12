<?php
/**
 * This file is part of the DreamFactory Services Platform(tm) SDK For PHP
 *
 * Copyright 2012-2014 DreamFactory Software, Inc. <support@dreamfactory.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Kisma\Core\Interfaces;

/**
 * StoreLike
 * An object tha acts like a data store.
 *
 * Add-on to the Doctrine Cache {@see Doctring\Common\Cache\Cache} interface. Adds methods
 * "get" and "set, plus the method missing from the Cache interface, "deleteAll".
 */
interface StoreLike
{
    //*************************************************************************
    //	Constants
    //*************************************************************************

    /**
     * @type int Nada = forever
     */
    const TTL_FOREVER = 0;
    /**
     * @type int One minute
     */
    const TTL_MINUTE = 60;
    /**
     * @type int One hour
     */
    const TTL_HOUR = 3600;
    /**
     * @type int One day
     */
    const TTL_DAY = 86400;

    //*************************************************************************
    //	Methods
    //*************************************************************************

    /**
     * Stores a value for $key
     *
     * @param string $key  The key to under which to store the data
     * @param mixed  $data The data to store
     * @param int    $ttl  The number of seconds for this value to live. Defaults to 0, meaning forever.
     *
     * @return bool True if the value was successfully stored
     */
    public function set( $key, $data, $ttl = self::TTL_FOREVER );

    /**
     * Gets an event from key "$key"
     *
     * @param string $key          The key to retrieve
     * @param mixed  $defaultValue The value to return if the $key is not found in the cache
     * @param bool   $remove       If true, remove the item after it has been retrieved
     * @param int    $ttl          The number of seconds for the default value to live if $remove is FALSE.
     *                             Defaults to 0, meaning forever.
     *
     * @return mixed The value stored under $key
     */
    public function get( $key, $defaultValue = null, $remove = false, $ttl = self::TTL_FOREVER );

    /**
     * Deletes all items from the store
     *
     * @return bool
     */
    public function deleteAll();
}
