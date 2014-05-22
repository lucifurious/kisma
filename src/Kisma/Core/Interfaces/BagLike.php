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
namespace Kisma\Core\Interfaces;

use Kisma\Core\Interfaces\Events\SeedLike;
use Kisma\Core\SeedBag;

/**
 * BagLike
 * Something that can contain KVPs. Ya know, like a bag?
 */
interface BagLike extends SeedLike
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Retrieves a value at the given key location, or the default value if key isn't found.
	 * Setting $burnAfterReading to true will remove the key-value pair from the bag after it
	 * is retrieved. Call with no arguments to get back a KVP array of contents
	 *
	 * @param string $key
	 * @param mixed  $defaultValue
	 * @param bool   $burnAfterReading
	 *
	 * @throws \Kisma\Core\Exceptions\BagException
	 * @return mixed
	 */
	public function get( $key = null, $defaultValue = null, $burnAfterReading = false );

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param bool   $overwrite
	 *
	 * @throws \Kisma\Core\Exceptions\BagException
	 * @return SeedBag
	 */
	public function set( $key, $value, $overwrite = true );
}
