<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license http://github.com/Pogostick/kisma/licensing/
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Kisma_Components
 * @package kisma.components
 * @namespace \Kisma\Components
 * @since v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Components
 */
namespace Kisma\Components
{
	/**
	 * Aspect
	 * Aspects allow objects to take on functionality defined in another class.
	 *
	 * @TODO Replace with traits once PHP 5.4 is released
	 *
	 * @property string $eventHandlerSignature The prefix of a method indicating it is an event handler
	 * @property array $eventMap A map of event names to handlers provided by this aspect.
	 * @property-read \Kisma\Components\Component $linker
	 */
	abstract class Aspect extends SubComponent implements \Kisma\IAspect
	{
		//********************************************************************************
		//* Properties
		//********************************************************************************

		/**
		 * @var string The prefix of a method indicating it is an event handler
		 */
		protected $_eventHandlerSignature = 'on';
		/**
		 * @var array A map of event names to handlers provided by this aspect.
		 */
		protected $_eventMap = array();
		/**
		 * @var \Kisma\Components\Component
		 */
		protected $_linker = null;

		//********************************************************************************
		//* Public Methods
		//********************************************************************************

		/**
		 * Link to a parent component
		 *
		 * @param \Kisma\Components\Component $linker
		 * @return \Kisma\Components\Aspect
		 */
		public function link( Component $linker )
		{
			$this->_linker = $linker;

			//	Bind all my events at once
			foreach ( $this->_eventMap as $_eventName => $_method )
			{
				if ( in_array( $_eventName, array_keys( $this->_eventMap ) ) )
				{
					//	Bind our handler to the linker, pass self as $eventData
					$linker->{$this->getOption( \KismaOptions::AppEventClass, \KismaSettings::DefaultAppEventClass )}->bind(
						$_eventName,
						array(
							$this,
							$_method
						),
						$this
					);
				}
			}

			return $this;
		}

		/**
		 * Unlinks the aspect from a $linker
		 *
		 * @param \Kisma\Components\Component $linker
		 * @return \Kisma\Components\Aspect
		 */
		public function unlink( Component $linker )
		{
			//	Unbind all my events at once
			foreach ( $this->_eventMap as $_eventName => $_method )
			{
				if ( in_array( $_eventName, array_keys( $this->_eventMap ) ) )
				{
					$linker->{$this->getOption( \KismaOptions::AppEventClass, \KismaSettings::DefaultAppEventClass )}->unbind(
						$_eventName,
						array(
							$this,
							$_method
						)
					);
				}
			}

			//	Clear the linker and return
			$this->_linker = null;
			return $this;
		}

		//*************************************************************************
		//* Private Methods
		//*************************************************************************

		/**
		 * Builds a hash of events and handlers that are present in this object based on the event handler signature.
		 * This merely builds the hash, nothing is done with it.
		 *
		 * @param bool $appendToList
		 * @return array
		 */
		protected function _findEventHandlers( $appendToList = false )
		{
			if ( null !== $this->_linker )
			{
				$_mirror = new \ReflectionClass( $this->_linker );

				//	See if there are any events that should be ignored
				$_ignores = $this->getOption( \KismaOptions::IgnoreEvents, array() );

				//	Clean up the ignore list
				array_walk(
					$_ignores,
					function( &$ignore )
					{
						$ignore = \K::kismaTag( $ignore );
					}
				);

				//	If we're appending to the list, then don't erase prior data
				if ( false === $appendToList )
				{
					$this->_eventMap = array();
				}

				//	Check each method for the event handler signature
				foreach ( $_mirror->getMethods() as $_method )
				{
					$_realMethodName = $_method->name;
					$_length = strlen( $this->_eventHandlerSignature );
					$_stub = substr( $_realMethodName, 0, $_length );

					if ( 0 == strcasecmp( $_stub, $this->_eventHandlerSignature ) )
					{
						$_eventKey = \K::kismaTag( substr( $_realMethodName, $_length ), true );

						//	Map the callback to the key
						$this->_eventMap[$_eventKey] = array(
							$this->_linker,
							$_realMethodName
						);
					}
				}
			}

			//	Return the current map
			return $this->_eventMap;
		}

		//********************************************************************************
		//* Properties
		//********************************************************************************

		/**
		 * Returns the currently linked parent
		 *
		 * @param \Kisma\IAspectable $linker
		 * @return \Kisma\Components\Aspect
		 */
		public function setLinker( \Kisma\IAspectable $linker = null )
		{
			$this->_linker = $linker;
			$this->_findEventHandlers( true );
			return $this;
		}
		/**
		 * @return \Kisma\Components\Component
		 */
		public function getLinker()
		{
			return $this->_linker;
		}

		/**
		 * @param string $eventHandlerSignature
		 * @return \Kisma\Components\Aspect
		 */
		public function setEventHandlerSignature( $eventHandlerSignature = 'on' )
		{
			$this->_eventHandlerSignature = $eventHandlerSignature;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getEventHandlerSignature()
		{
			return $this->_eventHandlerSignature;
		}

		/**
		 * @param array $eventMap
		 * @return \Kisma\Components\Aspect
		 */
		public function setEventMap( $eventMap = array() )
		{
			$this->_eventMap = $eventMap;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getEventMap()
		{
			return $this->_eventMap;
		}

	}
	
}