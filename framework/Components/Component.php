<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link      http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license   http://github.com/Pogostick/kisma/licensing/
 * @author    Jerry Ablan <kisma@pogostick.com>
 * @category  Kisma_Components
 * @package   kisma.components
 * @namespace \Kisma\Components
 * @since     v1.0.0
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
	//*************************************************************************
	//* Aliases
	//*************************************************************************

	/**
	 * Kisma Aliases
	 */
	use Kisma\Aspects as Aspects;

	/**
	 * Component
	 * A basic building block of Kisma. Aspectable and included event handling and an optional object store
	 *
	 * Basics
	 * ======
	 *
	 * Features
	 * ========
	 *   o Virtualized Aspects
	 *
	 * Properties: Always exist, and always have a default value.
	 * ===========================================================================
	 * @property Aspect[] $aspects
	 * @property Aspects\Reactors\AppEvent $eventHandling
	 *
	 * Options: May or may not be set, use as you'd like
	 * ===========================================================================
	 * @property string $appEventClass
	 * @property string $objectStorageClass Set to false to not use object storage
	 *
	 * Optional Aspects
	 * ================
	 * @property Aspects\ObjectStorage $objectStorage
	 */
	abstract class Component extends SubComponent implements \Kisma\IAspectable, \Kisma\IReactor
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var Aspect[] This component's aspects
		 */
		protected $_aspects = array();

		//*************************************************************************
		//* Default/Magic Methods
		//*************************************************************************

		/**
		 * Allow calling Aspect methods from the object
		 *
		 * @throws \BadMethodCallException
		 * @param string $method
		 * @param array  $arguments
		 * @return mixed
		 */
		public function __call( $method, $arguments )
		{
			//	See if any of our aspects have this method
			foreach ( $this->_aspects as $_aspect )
			{
				//	Call aspect methods if they exist
				if ( method_exists( $_aspect, $method ) )
				{
					return call_user_func_array(
						array(
							$_aspect,
							$method
						),
						$arguments
					);
				}
			}

			//	Guess not...
			throw new \BadMethodCallException( __CLASS__ . '::' . $method . ' is undefined.' );
		}

		/**
		 * Magic getter. Used to provide aspects as virtual properties
		 *
		 * @param string $name
		 * @return Aspect|mixed
		 * @throws \UndefinedPropertyException
		 */
		public function __get( $name )
		{
			//	We are ONLY virtualizing aspects as properties to avoid namespace collision
			if ( false !== ( $_aspect = $this->hasAspect( $name, true ) ) )
			{
				return $_aspect;
			}

			throw new \UndefinedPropertyException( 'The property "' . $name . '" is undefined.' );
		}

		//*************************************************************************
		//* Aspect Handling Methods
		//*************************************************************************

		/**
		 * Determines if this component has the requested aspect linked. If not, the return value is false.
		 * If the aspect is linked, the return value is the key name of the aspect.
		 * If $returnAspect is set to true, the aspect instance will be returned
		 *
		 * @param string $aspectName
		 * @param bool $returnAspect If true, instead of the standardized name being returned, you get the aspect object.
		 * @return false|string|\Kisma\Components\Aspect
		 */
		public function hasAspect( $aspectName, $returnAspect = false )
		{
			return \K::hasComponent( $this->_aspects, $aspectName, $returnAspect );
		}

		/**
		 * Link an aspect to this component
		 * @param string $aspectName
		 * @param array  $options
		 * @return \Kisma\Components\Aspect
		 */
		public function linkAspect( $aspectName, $options = array() )
		{
			if ( false === ( $_aspect = $this->hasAspect( $aspectName, true ) ) )
			{
				\K::logDebug( 'Linking Aspect: ' . $aspectName );

				if ( !isset( $options['linker'] ) )
				{
					$options['linker'] = $this;
				}

				$_aspectKey = \K::kismaTag( $aspectName, true );
				$this->_aspects[$_aspectKey] = $_aspect = \K::createComponent( $aspectName, $options );
			}

			return $_aspect;
		}

		/**
		 * Links multiple aspects to this component. Pass array of aspect options
		 * indexed by aspect class name.
		 *
		 * @param array $aspects
		 * @return \Kisma\Components\Component
		 */
		public function linkAspects( $aspects = array() )
		{
			foreach ( $aspects as $_aspectClass => $_options )
			{
				$this->linkAspect( $_aspectClass, $_options );
			}

			return $this;
		}

		/**
		 * Unlinks all aspects from this component.
		 * @return \Kisma\Components\Component
		 */
		public function unlinkAspects()
		{
			foreach ( $this->_aspects as $_aspectClass => $_aspect )
			{
				$this->unlinkAspect( $_aspectClass );
			}

			//	Make a fresh array
			$this->_aspects = array();
			return $this;
		}

		/**
		 * Unlinks an aspect from this component
		 * @param string $aspectName
		 * @return bool
		 * @see Aspect
		 */
		public function unlinkAspect( $aspectName )
		{
			if ( false !== ( $_aspectKey = $this->hasAspect( $aspectName ) ) )
			{
				$this->_aspects[$_aspectKey]->unlink( $this );
				unset( $this->_aspects[$_aspectKey] );
				return true;
			}

			return false;
		}

		//*************************************************************************
		//* Private Methods
		//*************************************************************************

		/**
		 * Extends the base by adding aspect loading
		 * @param array $options\Kisma\
		 * @param bool  $noMerge If true, this object's options will be cleared first
		 * @return void
		 */
		protected function _loadConfiguration( $options = array(), $noMerge = false )
		{
			parent::_loadConfiguration( $options, $noMerge );

			//	And load our aspects
			if ( $this->_loadAspects( $this->getOption( \KismaOptions::AspectOptions, array(), true ) ) )
			{
				//	Auto-bind events, remove from $options
				if ( false !== $this->getOption( \KismaOptions::AutoBindEvents, true ) )
				{
					$this->{\KismaSettings::DefaultAppEventClass}->autoBind(
						$this->getOption( \KismaOptions::AutoBindOptions, array(), true )
					);
				}
			}
		}

		/**
		 * Loads the standard component aspects (Aspects\Reactors\AppEvent and Aspects\Storage\ObjectStorage)
		 * avoiding recursion.
		 *
		 * @param array $options
		 * @return bool
		 */
		protected function _loadAspects( $options = array() )
		{
			$_classes = array_merge(
				\K::o( $options, 'classes', array() ),
				array(
					//	We require event handling
					\KismaSettings::DefaultAppEventClass,
				)
			);

			//	Add in object storage if it's not disabled
			$_objectStorageClass = $this->getOption(
				\KismaOptions::ObjectStorageClass,
				\KismaSettings::DefaultObjectStorageClass
			);

			if ( false !== $_objectStorageClass )
			{
				$_classes[] = $_objectStorageClass;
			}

			foreach ( $_classes as $_aspectClass )
			{
				//	The linker will see if we have it already...
				$this->linkAspect( $_aspectClass );
			}

			return true;
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param array $aspects
		 * @return \Kisma\Components\Component
		 */
		public function setAspects( $aspects = array() )
		{
			$this->_aspects = $aspects;
			return $this;
		}

		/**
		 * @return Aspect[]
		 */
		public function getAspects()
		{
			return $this->_aspects;
		}

	}
}