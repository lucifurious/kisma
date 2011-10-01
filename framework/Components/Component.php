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
namespace Kisma\Components;

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
 * @property boolean $enableObjectStorage
 * @property \Kisma\Aspects\EventHandling $eventHandling
 *
 * Options: May or may not be set, use as you'd like
 * ===========================================================================
 * @property string $eventHandlingClass
 * @property string $objectStorageClass
 *
 * Optional Aspects
 * ================
 * @property \Kisma\Aspects\ObjectStorage $objectStorage
 */
abstract class Component extends \Kisma\Components\SubComponent implements \Kisma\IAspectable
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Kisma\Components\Aspect[] This component's aspects
	 */
	protected $_aspects = array();
	/**
	 * @var bool If true, this component will automatically link the EventHandling aspect.
	 */
	protected $_enableObjectStorage = true;

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
	 */
	public function __get( $name )
	{
		//	We are ONLY virtualizing aspects as properties to avoid namespace collision
		if ( false !== ( $_aspect = $this->hasAspect( $name, true ) ) )
		{
			return $_aspect;
		}
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
		return K::hasComponent( $this->_aspects, $aspectName, $returnAspect );
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
			K::logDebug( 'Linking Aspect: ' . $aspectName );

			if ( !isset( $options['linker'] ) )
			{
				$options['linker'] = $this;
			}

			$_aspectKey = K::kismaTag( $aspectName, true );
			$this->_aspects[$_aspectKey] = $_aspect = K::createComponent( $aspectName, $options );
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
	 * @param array $options
	 * @param bool  $noMerge If true, this object's options will be cleared first
	 * @return void
	 */
	protected function _loadConfiguration( $options = array(), $noMerge = false )
	{
		parent::_loadConfiguration( $options, $noMerge );

		//	And load our aspects
		if ( $this->_loadAspects( $this->getOption( 'aspect_options', array(), true ) ) )
		{
			//	Auto-bind events, remove from $options
			if ( false !== $this->getOption( 'auto_bind_events', true ) )
			{
				$this->{'kisma.aspects.event_handling'}->autoBind(
					$this->getOption( 'auto_bind_options', array(), true )
				);
			}
		}
	}

	/**
	 * Loads the standard component aspects (EventHandling and ObjectStorage)
	 * avoiding recursion.
	 *
	 * @param array $options
	 * @return bool
	 */
	protected function _loadAspects( $options = array() )
	{
		$_classes = K::o(
			$options,
			'classes',
			array(
				'kisma.aspects.event_handling' => true,
				'kisma.aspects.object_storage' => $this->_enableObjectStorage,
			)
		);

		foreach ( $_classes as $_aspectClass => $_enabled )
		{
			if ( true === $_enabled )
			{
				//	If we don't have this aspect, load it.
				if ( false === $this->hasAspect( $_aspectClass ) )
				{
					$this->linkAspect( $_aspectClass );
				}
			}
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
	 * @return \Kisma\Components\Aspect[]
	 */
	public function getAspects()
	{
		return $this->_aspects;
	}

	/**
	 * @param boolean $enableObjectStorage
	 * @return \Kisma\Components\Component
	 */
	public function setEnableObjectStorage( $enableObjectStorage = true )
	{
		$this->_enableObjectStorage = $enableObjectStorage;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getEnableObjectStorage()
	{
		return $this->_enableObjectStorage;
	}

}
