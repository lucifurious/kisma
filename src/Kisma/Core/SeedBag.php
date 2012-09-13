<?php
/**
 * SeedBag.php
 */
namespace Kisma\Core;
/**
 * SeedBag
 * A generic collection class
 */
class SeedBag extends Seed implements \ArrayAccess, \Countable, \IteratorAggregate
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var array The contents
	 */
	protected $_bag = array();
	/**
	 * @var array A map of key hashes
	 */
	protected $_keyMap = array();

	//*************************************************************************
	//* Public Methods
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
		return array_values( $this->_keyMap );
	}

	/**
	 * @return array
	 */
	public function values()
	{
		return array_values( $this->_bag );
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
		return \Kisma\Core\Utility\Option::get( $this->_bag, $this->hashKey( $key ), $defaultValue, $burnAfterReading );
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param bool   $overwrite
	 *
	 * @throws \Kisma\Core\Exceptions\DuplicateKeyException
	 * @return SeedBag
	 */
	public function set( $key, $value, $overwrite = true )
	{
		if ( false === $overwrite && !$this->contains( $key ) )
		{
			throw new \Kisma\Core\Exceptions\DuplicateKeyException( 'The key "' . $key . '" is already in the bag.' );
		}

		$_hash = $this->hashKey( $key );

		$this->_keyMap[$_hash] = $key;
		\Kisma\Core\Utility\Option::set( $this->_bag, $_hash, $value );

		return $this;
	}

	/**
	 * @param string $key
	 *
	 * @return SeedBag
	 */
	public function remove( $key )
	{
		$_hash = $this->hashKey( $key );

		if ( $this->contains( $key ) )
		{
			unset( $this->_keyMap[$_hash] );
			\Kisma\Core\Utility\Option::remove( $this->_bag, $_hash );
		}

		return $this;
	}

	/**
	 * @return SeedBag
	 */
	public function clear()
	{
		unset( $this->_bag, $this->_keyMap );

		return $this;
	}

	/**
	 * @param array|\Traversable $source
	 *
	 * @return SeedBag
	 * @throws \InvalidArgumentException
	 */
	public function merge( $source )
	{
		if ( !is_array( $source ) && !( $source instanceof \Traversable ) )
		{
			throw new \InvalidArgumentException( 'The source must be an array or an object.' );
		}

		//	Hash the keys
		$_data = array();

		foreach ( $source as $_key => $_value )
		{
			$_data[$this->hashKey( $_key )] = $_value;
		}

		\Kisma\Core\Utility\Option::merge( $this->_bag, $_data );

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
		$_hash = $this->hashKey( $key );

		return in_array( $_hash, array_keys( $this->_keyMap ) );
	}

	/**
	 * Called before methods to allow for key munging downstream.
	 * Default implementation doesn't do anything to the key
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function hashKey( $key )
	{
		if ( null === $key )
		{
			return array_values( $this->_keyMap );
		}

		return $key;
	}

	//*************************************************************************
	//* Interface Methods
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function offsetExists( $offset )
	{
		return $this->contains( $offset );
	}

	/**
	 * {@InheritDoc}
	 */
	public function offsetGet( $offset )
	{
		return $this->get( $offset );
	}

	/**
	 * {@InheritDoc}
	 */
	public function offsetSet( $offset, $item )
	{
		$this->set( $offset, $item );
	}

	/**
	 * {@InheritDoc}
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
