<?php
/**
 * SeedBagIterator.php
 */
namespace Kisma\Core;
/**
 * SeedBagIterator
 * A dead simple iterator for our bags
 */
class SeedBagIterator implements \Iterator
{
	//*************************************************************************
	//* Private Members
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
	//* Public Methods
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
