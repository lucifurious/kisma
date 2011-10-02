<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link	  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license   http://github.com/Pogostick/kisma/licensing/
 * @author	Jerry Ablan <kisma@pogostick.com>
 * @category  Kisma_Aspects
 * @package   kisma.aspects
 * @namespace \Kisma\Aspects
 * @since	 v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Aspects\Storage
 */
namespace Kisma\Aspects\Storage
{
	//*************************************************************************
	//* Aliases
	//*************************************************************************

	/**
	 * Kisma Aliases
	 */
	use Kisma\Components as Components;
	use Kisma\Aspects as Aspects;

	/**
	 * ObjectStorage
	 * An quick 'n easy / down 'n dirty aspect that provides object storage by
	 * leveraging an \SplObjectStorage object.
	 *
	 * You can, of course, create your own storage aspects and override the
	 * storage class in your components.
	 *
	 * Basics
	 * ======
	 *
	 * Tag Names & Data
	 * ================
	 *
	 * Properties
	 * ==========
	 * @property \SplObjectStorage $storageObject
	 *
	 * Methods
	 * =======
	 * @method void addAll( SplObjectStorage $storage )
	 * @method void attach( object $object, mixed $data = NULL )
	 * @method bool contains( object $object )
	 * @method int count()
	 * @method object current()
	 * @method void detach( object $object )
	 * @method string getHash( string $object )
	 * @method mixed getInfo()
	 * @method int key()
	 * @method void next()
	 * @method mixed offsetGet( object $object )
	 * @method void offsetSet( object $object, mixed $data = NULL )
	 * @method void offsetUnset( object $object )
	 * @method void removeAll( SplObjectStorage $storage )
	 * @method void removeAllExcept ( SplObjectStorage $storage )
	 * @method void rewind()
	 * @method void setInfo( mixed $data )
	 * @method bool valid()
	 */
	class ObjectStorage extends Components\Aspect implements \Countable, \Iterator, \Serializable, \ArrayAccess
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var \SplObjectStorage Our storage object
		 */
		protected $_storageObject = null;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * Pass-through for the SplObjectStorage object
		 *
		 * @param string $method
		 * @param array  $arguments
		 *
		 * @return mixed
		 */
		public function __call( $method, $arguments )
		{
			if ( null !== $this->_storageObject && method_exists( $this->_storageObject, $method ) )
			{
				return call_user_func_array(
					array(
						$this->_storageObject,
						$method
					),
					$arguments
				);
			}

			return parent::__call( $method, $arguments );
		}

		//*************************************************************************
		//* Interface Methods
		//*************************************************************************

		/**
		 * @return string
		 */
		public function serialize()
		{
			return $this->_storageObject->serialize();
		}

		/**
		 * @param string $serialized
		 * @return mixed
		 */
		public function unserialize( $serialized )
		{
			return $this->_storageObject->unserialize( $serialized );
		}

		/**
		 * @param object $object
		 * @return bool
		 */
		public function offsetExists( $object )
		{
			return $this->_storageObject->offsetExists( $object );
		}

		/**
		 * @param object $object
		 * @return bool
		 */
		public function offsetGet( $object )
		{
			return $this->_storageObject->offsetGet( $object );
		}

		/**
		 * @param object $object
		 * @param mixed $info
		 * @return bool
		 */
		public function offsetSet( $object, $info )
		{
			return $this->_storageObject->offsetSet( $object, $info );
		}

		/**
		 * @param object $object
		 * @return bool
		 */
		public function offsetUnset( $object )
		{
			return $this->_storageObject->offsetUnset( $object );
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param \SplObjectStorage $storageObject
		 *
		 * @return Aspects\Storage\ObjectStorage
		 */
		public function setStorageObject( $storageObject )
		{
			$this->_storageObject = $storageObject;
			return $this;
		}

		/**
		 * @return \SplObjectStorage
		 */
		public function getStorageObject()
		{
			return $this->_storageObject;
		}
	}
}