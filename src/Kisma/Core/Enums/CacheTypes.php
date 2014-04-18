<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
 *
 * Kisma(tm) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kisma(tm) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kisma(tm).  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Kisma\Core\Enums;

/**
 * CacheTypes
 * Types of caches supported by Doctrine/Cache
 */
class CacheTypes extends SeedEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var string
     */
    const APC = 'Apc';
    /**
     * @var string
     */
    const ARRAY_CACHE = 'Array';
    /**
     * @var string
     */
    const FILE = 'File';
    /**
     * @var string
     */
    const FILE_SYSTEM = 'Filesystem';
    /**
     * @var string
     */
    const MEMCACHE = 'Memcache';
    /**
     * @var string
     */
    const MEMCACHED = 'Memcached';
    /**
     * @var string
     */
    const PHP_FILE = 'PhpFile';
    /**
     * @var string
     */
    const REDIS = 'Redis';
    /**
     * @var string
     */
    const WIN_CACHE = 'WinCache';
    /**
     * @var string
     */
    const XCACHE = 'Xcache';
    /**
     * @var string
     */
    const ZEND_DATA = 'ZendData';
}
