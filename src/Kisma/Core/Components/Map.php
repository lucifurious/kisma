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
namespace Kisma\Core\Components;

use Kisma\Core\Interfaces\MapLike;
use Kisma\Core\Seed;
use Kisma\Core\Utility\Option;

/**
 * Map.php
 * A simple mapper with key hashing option
 */
class Map extends Seed implements MapLike
{
	//*************************************************************************
	//	Constants
	//*************************************************************************

	/**
	 * @var array Our map
	 */
	protected $_map = array();
	/**
	 * @var callable User-defined callback to hash the key, if paranoid.
	 */
	protected $_keyHashCallback = null;

	//*************************************************************************
	//	Methods
	//*************************************************************************

	/**
	 * @param array $mappings Initial mappings, if any
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( array $mappings = array() )
	{
		parent::__construct();

		$this->_map = $mappings;
	}

	/**
	 * @param string|int $key
	 *
	 * @return string
	 */
	protected function _hashKey( $key )
	{
		if ( null !== $this->_keyHashCallback && is_callable( $this->_keyHashCallback ) )
		{
			$_arguments = func_get_args();

			return call_user_func_array( $this->_keyHashCallback, $_arguments );
		}

		return $key;
	}

	/**
	 * @param string|int $key
	 * @param bool       $returnIfFound If true, the map value is returned if found. Otherwise TRUE is returned.
	 *
	 * @return bool|string True/False or the map value if $returnIfFound is TRUE
	 */
	public function contains( $key, $returnIfFound = true )
	{
		if ( null !== ( $_value = $this->get( $key ) ) )
		{
			if ( true !== $returnIfFound )
			{
				$_value = true;
			}

			return $_value;
		}

		return false;
	}

	/**
	 * Adds a mapping
	 *
	 * @param string|int $key
	 * @param mixed      $value
	 *
	 * @return $this
	 */
	public function map( $key, $value = null )
	{
		Option::set( $this->_map, $this->_hashKey( $key ), $value );

		return $this;
	}

	/**
	 * Removes a mapping
	 *
	 * @param string|int $key
	 *
	 * @return $this
	 */
	public function unmap( $key )
	{
		Option::remove( $this->_map, $this->_hashKey( $key ) );

		return $this;
	}

	/**
	 * Retrieves a value at the given key location, or the default value if key isn't found.
	 * Setting $burnAfterReading to true will remove the key-value pair from the bag after it
	 * is retrieved. Call with no arguments to get back a KVP array of contents
	 *
	 * @param string $key
	 * @param mixed  $defaultValue
	 * @param bool   $burnAfterReading
	 *
	 * @return mixed
	 */
	public function get( $key = null, $defaultValue = null, $burnAfterReading = false )
	{
		return Option::get( $this->_map, $this->_hashKey( $key ), $defaultValue, $burnAfterReading );
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param bool   $overwrite
	 *
	 * @return $this
	 */
	public function set( $key, $value, $overwrite = true )
	{
		Option::set( $this->_map, $this->_hashKey( $key ), $value, $overwrite );

		return $this;
	}

	/**
	 * @param callable $keyHashCallback
	 *
	 * @return Map
	 */
	public function setKeyHashCallback( $keyHashCallback )
	{
		$this->_keyHashCallback = $keyHashCallback;

		return $this;
	}

	/**
	 * @return callable
	 */
	public function getKeyHashCallback()
	{
		return $this->_keyHashCallback;
	}

	/**
	 * @param array $map
	 *
	 * @return Map
	 */
	public function setMap( $map )
	{
		$this->_map = $map;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getMap()
	{
		return $this->_map;
	}
}
