<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link		  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license	   http://github.com/Pogostick/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @category	  Kisma|Components
 * @package	   kisma.components
 * @namespace	 \Kisma\Components
 * @since		 v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Components Kisma components
 */
namespace Kisma\Components
{
	/**
	 * SubComponent
	 * The seed within...
	 *
	 * Basics
	 * ======
	 * SubComponent is the base class of all Kisma classes. It's used mainly
	 * to distinguish its more capable son, Component, from his sister
	 * Aspect.
	 *
	 * Features
	 * ========
	 *   o  Property and option management
	 *
	 * Properties: Always exist, and always have a default value.
	 * ===========================================================================
	 * @property array $options
	 * @property \Exception[] $errors
	 * @property int $index
	 * @property-read int $count
	 * @property boolean $skipNext
	 * @property boolean $readOnly
	 * @property boolean $logging
	 */
	abstract class SubComponent implements \Kisma\IKisma, \Kisma\IConfigurable, \Countable, \Iterator
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/***
		 * @var array This object's options
		 */
		protected $_options = array();
		/**
		 * @var \Exception[]
		 */
		protected $_errors = array();
		/**
		 * @var integer Iteration index
		 */
		protected $_index = 0;
		/**
		 * @var integer Holds the number of settings we have
		 */
		protected $_count = 0;
		/**
		 * @var boolean Used when un-setting values during iteration to ensure we do not skip the next element
		 */
		protected $_skipNext = false;
		/**
		 * @var boolean If true, configuration settings cannot be changed once loaded
		 */
		protected $_readOnly = true;
		/**
		 * @var bool|int The logging flags for this object
		 */
		protected $_logging = true;
		/**
		 * @var string My unique object ID
		 */
		protected $_objectId = null;

		//*************************************************************************
		//* Default/Magic Methods
		//*************************************************************************

		/**
		 * The base component constructor
		 *
		 * @param array $options
		 * @return \Kisma\Components\SubComponent
		 */
		public function __construct( $options = array() )
		{
			//	Create our hash...
			$this->_objectId = spl_object_hash( $this );

			//	Configure our properties
			$this->_loadConfiguration( $options, true );
		}

		//*************************************************************************
		//* Private Methods
		//*************************************************************************

		/**
		 * Loads an array into properties if they exist.
		 * @param array $options
		 * @param bool $mergeOptions
		 * @return void
		 */
		protected function _loadConfiguration( $options = array(), $mergeOptions = true )
		{
			//	Catch null input...
			if ( null === $options || !is_array( $options ) || empty( $options ) )
			{
				$options = array();
			}
			else
			{
				$options = \K::cleanOptions( $options );
			}

			//	Set our own options and work from there
			if ( true !== $mergeOptions )
			{
				//	Overwrite the options...
				$this->_options = $options;
			}
			else
			{
				//	Merge the options...
				$this->_options = array_merge(
					$this->_options,
					$options
				);
			}

			//	Loop through, set...
			foreach ( $this->_options as $_key => $_value )
			{
				try
				{
					\K::__property( $this, $_key, \Kisma\AccessorMode::Set, $_value );
				}
				catch ( \Kisma\UndefinedPropertyException $_ex )
				{
					//	Undefined, add to options...
//					$_options[$_key] = $_value;
				}
			}

			//	Set our count...
			$this->_count = count( $this->_options );
		}

		//*************************************************************************
		//* Interface Methods
		//*************************************************************************

		/**
		 * Required by Countable interface
		 * @return int
		 */
		public function count()
		{
			return $this->_count;
		}

		/**
		 * Required by Iterator interface
		 * @return mixed
		 */
		public function current()
		{
			$this->_skipNext = false;
			return current( $this->_options );
		}

		/**
		 * Required by Iterator interface
		 * @return mixed
		 */
		public function key()
		{
			return key( $this->_options );
		}

		/**
		 * Required by Iterator interface
		 */
		public function next()
		{
			if ( $this->_skipNext )
			{
				$this->_skipNext = false;
				return;
			}

			next( $this->_options );

			$this->_index++;
		}

		/**
		 * Required by Iterator interface
		 */
		public function rewind()
		{
			$this->_skipNext = false;
			reset( $this->_options );
			$this->_index = 0;
		}

		/**
		 * Required by Iterator interface
		 * @return boolean
		 */
		public function valid()
		{
			return ( $this->_index < $this->_count );
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param array $errors
		 * @return \Kisma\Components\Component
		 */
		public function setErrors( $errors = array() )
		{
			$this->_errors = $errors;
			return $this;
		}

		/**
		 * @return \Kisma\Components\Exception[]
		 */
		public function getErrors()
		{
			return $this->_errors;
		}

		/**
		 * @param int $index
		 * @return \Kisma\Components\Component $this
		 */
		public function setIndex( $index )
		{
			$this->_index = $index;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getIndex()
		{
			return $this->_index;
		}

		/**
		 * @param bool $logging
		 * @return \Kisma\Components\Component
		 */
		public function setLogging( $logging = false )
		{
			$this->_logging = $logging;
			return $this;
		}

		/**
		 * @return bool|int
		 */
		public function getLogging()
		{
			return $this->_logging;
		}

		/**
		 * @param int $count
		 * @return \Kisma\Components\Component $this
		 */
		protected function _setCount( $count )
		{
			$this->_count = $count;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getCount()
		{
			return $this->_count;
		}

		/**
		 * sets all options at once
		 * @param array $options
		 * @return \Kisma\Components\Component $this
		 */
		public function setOptions( $options = array() )
		{
			//	Bulk set all options
			foreach ( $options as $_key => $_value )
			{
				$this->_options[$_key] = $_value;
			}

			return $this;
		}

		/**
		 * @param string	 $name
		 * @param mixed|null $value
		 * @return mixed
		 */
		public function setOption( $name, $value = null )
		{
			$this->_options[$name] = $value;
			return $this;
		}

		/**
		 * @return array
		 */
		public function &getOptions()
		{
			return $this->_options;
		}

		/**
		 * @param string $name
		 * @param mixed|null $defaultValue
		 * @param bool	   $deleteAfter If true, key is removed from the option list after it is read.
		 * @return mixed
		 */
		public function getOption( $name, $defaultValue = null, $deleteAfter = false )
		{
			return \K::o( $this->_options, $name, $defaultValue, $deleteAfter );
		}

		/**
		 * @param boolean $readOnly
		 * @return \Kisma\Components\Component $this
		 */
		public function setReadOnly( $readOnly = true )
		{
			$this->_readOnly = $readOnly;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getReadOnly()
		{
			return $this->_readOnly;
		}

		/**
		 * @param boolean $skipNext
		 * @return \Kisma\Components\Component
		 */
		public function setSkipNext( $skipNext )
		{
			$this->_skipNext = $skipNext;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getSkipNext()
		{
			return $this->_skipNext;
		}

		/**
		 * @param string $objectId
		 * @return $this
		 */
		public function setObjectId( $objectId = null )
		{
			$this->_objectId = $objectId;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getObjectId()
		{
			return $this->_objectId;
		}

	}
}