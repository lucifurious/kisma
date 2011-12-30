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
	 * ConfigurationServiceProvider
	 * An encapsulated options container
	 */
	class OptionsServiceProvider extends \Kisma\Components\ServiceProvider implements \Serializable, \ArrayAccess
	{
		//*************************************************************************
		//* Constants 
		//*************************************************************************

		/**
		 * @var string
		 */
		const AppConfig = 'app.config';

		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var array
		 */
		private $_appConfig = array();

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * Register the object storage service provider
		 *
		 * @param \Kisma\Kisma $app
		 */
		public function register( \Kisma\Kisma $app )
		{
			$app[self::AppConfig] = $app->share( function() use ( $app )
			{
				return new self();
			} );
		}

		/**
		 * @param string $name
		 * @param mixed  $value
		 */
		public function __set( $name, $value )
		{
			$this->_appConfig[$name] = $value;
		}

		/**
		 * @param $name
		 *
		 * @return mixed
		 */
		public function __get( $name )
		{
			return $this->_appConfig[$name];
		}

		/**
		 * @param string $name
		 *
		 * @return mixed
		 */
		public function __unset( $name )
		{
			unset( $this->_appConfig[$name] );
		}

		/**
		 * @param string $name
		 *
		 * @return mixed
		 */
		public function __isset( $name )
		{
			return isset( $this->_appConfig[$name] );
		}

		/**
		 * Returns an object representation of the config
		 *
		 * @return \stdClass
		 */
		public function toObject()
		{
			return \Kisma\Utility\Transform::toObject( $this->_appConfig );
		}

		//*************************************************************************
		//* Interface Methods
		//*************************************************************************

		/**
		 * @return string
		 */
		public function serialize()
		{
			return \serialize( $this->_appConfig );
		}

		/**
		 * @param string $serialized
		 *
		 * @return mixed
		 */
		public function unserialize( $serialized )
		{
			$this->_appConfig = \unserialize( $serialized );
		}

		/**
		 * @param mixed $offset
		 *
		 * @return bool
		 */
		public function offsetExists( $offset )
		{
			return isset( $this->_appConfig[$offset] );
		}

		/**
		 * @param mixed $offset
		 *
		 * @return bool
		 */
		public function offsetGet( $offset )
		{
			return $this->_appConfig[$offset];
		}

		/**
		 * @param mixed $offset
		 * @param mixed $value
		 *
		 * @return bool
		 */
		public function offsetSet( $offset, $value )
		{
			return $this->_appConfig[$offset] = $value;
		}

		/**
		 * @param mixed $offset
		 *
		 * @return bool
		 */
		public function offsetUnset( $offset )
		{
			unset( $this->_appConfig[$offset] );
		}

	}
}