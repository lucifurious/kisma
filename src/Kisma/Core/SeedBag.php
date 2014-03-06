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
 * SeedBag
 * A generic collection class
 */
class SeedBag extends Seed implements \ArrayAccess, \Countable, \IteratorAggregate, Interfaces\BagLike
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var array The contents
	 */
	private $_bag = array();
	/**
	 * @var bool If true, no new keys will be allowed
	 */
	private $_fixedSize = false;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param array $contents
	 *
	 * @return \Kisma\Core\SeedBag
	 */
	public function __construct( $contents = array() )
	{
		parent::__construct( $contents );

		//	Anything left, goes in the bag
		if ( !empty( $contents ) && ( is_array( $contents ) || $contents instanceof \Traversable ) )
		{
			foreach ( $contents as $_key => $_value )
			{
				$this->set( $_key, $_value );
				unset( $contents[$_key] );
			}
		}
	}

	/**
	 * @return array
	 */
	public function keys()
	{
		return array_keys( $this->_bag );
	}

	/**
	 * @return array
	 */
	public function values()
	{
		return array_values( $this->_bag );
	}

	/**
	 * @return array The contents of the bag
	 */
	public function contents()
	{
		return $this->_bag;
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
	 * @throws Exceptions\BagException
	 * @return mixed
	 */
	public function get( $key = null, $defaultValue = null, $burnAfterReading = false )
	{
		$_exists = Utility\Option::contains( $this->_bag, $key );

		if ( false !== $this->_fixedSize && !$_exists )
		{
			throw new Exceptions\BagException( 'This class does not have a property named "' . $key . '"' );
		}

		$_value = $defaultValue;

		if ( $_exists )
		{
			$_value = Utility\Option::get( $this->_bag, $key, $defaultValue, $burnAfterReading );
		}

		return $_value;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param bool   $overwrite
	 *
	 * @throws Exceptions\BagException
	 * @return SeedBag
	 */
	public function set( $key, $value = null, $overwrite = true )
	{
		//	Passed in an array or object
		if ( null === $value )
		{
			if ( $key instanceof \Traversable )
			{
				foreach ( $key as $_key => $_value )
				{
					$this->set( $_key, $_value, $overwrite );
				}

				return $this;
			}
			else if ( $key instanceof Interfaces\SeedLike )
			{
				$value = $key;
				$key = $value->getId();
			}
		}

		$_exists = Utility\Option::contains( $this->_bag, $key );

		if ( false === $overwrite && $_exists && ( null !== ( $_oldValue = Utility\Option::get( $this->_bag, $key ) ) ) )
		{
			throw new Exceptions\BagException( 'The property "' . $key . '" is read-only.' );
		}

		if ( false !== $this->_fixedSize && !$_exists )
		{
			throw new Exceptions\BagException( 'This class does not have a property named "' . $key . '"' );
		}

		Utility\Option::set( $this->_bag, $key, $value );

		return $this;
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function remove( $key )
	{
		if ( $key instanceof Interfaces\SeedLike )
		{
			$key = $key->getId();
		}

		if ( !Utility\Option::contains( $this->_bag, $key ) )
		{
			return false;
		}

		Utility\Option::remove( $this->_bag, $key );

		return true;
	}

	/**
	 * @return SeedBag
	 */
	public function clear()
	{
		unset( $this->_bag );
		$this->_bag = array();

		return $this;
	}

	/**
	 * @param array|\Traversable $source
	 * @param bool               $noNullOverwrite If true (default), inbound NULL values will not replace NON-NULL values in the bag
	 *
	 * @throws \InvalidArgumentException
	 * @return SeedBag
	 */
	public function merge( $source, $noNullOverwrite = true )
	{
		if ( !is_array( $source ) && !( $source instanceof \Traversable ) )
		{
			throw new \InvalidArgumentException( 'The source must be an array or an object.' );
		}

		foreach ( $source as $_key => $_value )
		{
			if ( null === $_value && true === $noNullOverwrite && Utility\Option::contains( $this->_bag, $_key ) )
			{
				//	If there is a value in the bag, and inbound value is null, skip...
				if ( null !== Utility\Option::get( $this->_bag, $_key ) )
				{
					continue;
				}
			}

			Utility\Option::set( $this->_bag, $_key, $_value );
		}

		return $this;
	}

	/**
	 * Checks to see if a key is in the bag.
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function contains( $key )
	{
		if ( $key instanceof Interfaces\SeedLike )
		{
			$key = $key->getId();
		}

		return Utility\Option::contains( $this->_bag, $key );
	}

	/**
	 * @param boolean $fixedSize
	 *
	 * @return SeedBag
	 * @codeCoverageIgnore
	 */
	public function setFixedSize( $fixedSize )
	{
		$this->_fixedSize = $fixedSize;

		return $this;
	}

	/**
	 * @return boolean
	 * @codeCoverageIgnore
	 */
	public function getFixedSize()
	{
		return $this->_fixedSize;
	}

	/**
	 * {@InheritDoc}
	 *
	 * @codeCoverageIgnore
	 */
	public function offsetExists( $offset )
	{
		return $this->contains( $offset );
	}

	/**
	 * {@InheritDoc}
	 *
	 * @codeCoverageIgnore
	 */
	public function offsetGet( $offset )
	{
		return $this->get( $offset );
	}

	/**
	 * {@InheritDoc}
	 *
	 * @codeCoverageIgnore
	 */
	public function offsetSet( $offset, $item )
	{
		$this->set( $offset, $item );
	}

	/**
	 * {@InheritDoc}
	 *
	 * @codeCoverageIgnore
	 */
	public function offsetUnset( $offset )
	{
		$this->remove( $offset );
	}

	/**
	 * {@InheritDoc}
	 */
	public function getIterator()
	{
		return new SeedBagIterator( $this->_bag );
	}

	/**
	 * {@InheritDoc}
	 */
	public function count()
	{
		return sizeof( $this->_bag );
	}
}
