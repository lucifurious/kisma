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
namespace Kisma\Provider
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
	 * ObjectStorageServiceProvider
	 * An quick 'n easy / down 'n dirty service provider that provides object storage by leveraging an \SplObjectStorage object.
	 *
	 * You can, of course, create your own storage providers and override the storage class in your components.
	 *
	 * Properties
	 * ==========
	 * @property \SplObjectStorage $storageObject
	 *
	 * Methods
	 * =======
	 * @method addAll( \SplObjectStorage $storage )
	 * @method attach( \object $object, $data = NULL )
	 * @method \boolean contains( \object $object )
	 * @method \int count()
	 * @method \object current()
	 * @method detach( \object $object )
	 * @method \string getHash( \string $object )
	 * @method getInfo()
	 * @method \int key()
	 * @method next()
	 * @method offsetGet( \object $object )
	 * @method offsetSet( \object $object, $data = NULL )
	 * @method offsetUnset( \object $object )
	 * @method removeAll( \SplObjectStorage $storage )
	 * @method removeAllExcept ( \SplObjectStorage $storage )
	 * @method rewind()
	 * @method setInfo( $data )
	 * @method \boolean valid()
	 */
	class ObjectStorageServiceProvider extends \Kisma\Components\SilexServiceProvider implements \Countable, \Iterator, \Serializable, \ArrayAccess
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
		 * Register the object storage service provider
		 * @param \Kisma\Kisma $app
		 */
		public function register( \Kisma\Kisma $app )
		{
			$_this = $this;

			$app['db.object'] = $app->share( function() use ( $app, $_this )
			{
				return $_this;
			} );
		}

		/**
		 * @param array $options
		 */
		public function __construct( $options = array() )
		{
			parent::__construct( $options );

			$this->_storageObject = new \SplObjectStorage();
		}

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
		 * @return Aspects\Storage\ObjectStorageServiceProvider
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