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
namespace Kisma\Core;

/**
 * SeedBagIterator
 * A dead simple iterator for our bags
 */
class SeedBagIterator implements \Iterator
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var array
	 */
	protected $_bag;
	/**
	 * @var string
	 */
	protected $_current;
	/**
	 * @var array
	 */
	protected $_keys;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param array|\Traversable $source
	 */
	public function __construct( &$source )
	{
		$this->_bag = $source;
		$this->_keys = array_keys( $source );
		$this->_current = reset( $this->_keys );
	}

	/**
	 * {@InheritDoc}
	 */
	public function rewind()
	{
		$this->_current = reset( $this->_keys );
	}

	/**
	 * {@InheritDoc}
	 */
	public function key()
	{
		return $this->_current;
	}

	/**
	 * {@InheritDoc}
	 */
	public function current()
	{
		return $this->_bag[$this->_current];
	}

	/**
	 * {@InheritDoc}
	 */
	public function next()
	{
		$this->_current = next( $this->_keys );
	}

	/**
	 * {@InheritDoc}
	 */
	public function valid()
	{
		return ( false !== $this->_current );
	}
}
