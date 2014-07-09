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
 * Default application-level settings defined by Kisma in ENUM format
 */
class CoreSettings extends SeedEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var string
     */
    const SESSION_KEY = 'kisma.options';
    /**
     * @var string Set to non-empty to enable debug logging
     */
    const DEBUG = 'app.debug';
    /**
     * @var string The base path of the Kisma library
     */
    const BASE_PATH = 'app.base_path';
    /**
     * @var string The Composer autoloader object
     */
    const AUTO_LOADER = 'app.auto_loader';
    /**
     * @var string Set to TRUE once Kisma is initialized
     */
    const CONCEPTION = 'app.conception';
    /**
     * @var string The version of Kisma
     */
    const VERSION = 'app.version';
    /**
     * @var string The name of the app
     */
    const NAME = 'app.name';
    /**
     * @var string The navbar items, if any
     */
    const NAV_BAR = 'app.nav_bar';
    /**
     * @var string The detected framework, if any
     */
    const FRAMEWORK = 'app.framework';
    /**
     * @var string The value to prepend to any keys at the top level
     */
    const OPTION_KEY_PREFIX = 'app.';
    /**
     * @type string
     */
    const CACHE_KEY = 'kisma.cache';
}