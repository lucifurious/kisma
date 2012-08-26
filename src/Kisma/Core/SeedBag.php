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

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array $contents
	 */
	public function __construct( $contents = array() )
	{
		if ( empty( $contents ) )
		{
			$contents = array();
		}

		foreach ( $contents as $_key => $_value )
		{
			$this->add( $_key, $_value );
			unset( $contents[$_key] );
		}

		parent::__construct( $contents );
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
	 * Retrieves a value at the given key location, or the default value if key isn't found.
	 * Setting $burnAfterReading to true will remove the key-value pair from the bag after it
	 * is retrieved.
	 *
	 * @param string $key
	 * @param mixed  $defaultValue
	 * @param bool   $burnAfterReading
	 *
	 * @return mixed
	 */
	public function get( $key = null, $defaultValue = null, $burnAfterReading = false )
	{
		return \Kisma\Core\Utility\Option::get( $this->_bag, $key, $defaultValue, $burnAfterReading );
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return SeedBag
	 */
	public function add( $key, $value )
	{
		\Kisma\Core\Utility\Option::set( $this->_bag, $key, $value );

		return $this;
	}

	/**
	 * @param string $key
	 *
	 * @return SeedBag
	 */
	public function remove( $key )
	{
		\Kisma\Core\Utility\Option::remove( $this->_bag, $key );

		return $this;
	}

	/**
	 * @return SeedBag
	 */
	public function clear()
	{
		$this->_bag = array();

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

		\Kisma\Core\Utility\Option::merge( $this->_bag, $source );

		return $this;
	}

	/**
	 * Checks to see if a key is in the bag
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function contains( $key )
	{
		return in_array( $key, array_keys( $this->_bag ) );
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
		$this->add( $offset, $item );
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
