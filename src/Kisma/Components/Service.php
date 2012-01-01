<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license			http://github.com/Pogostick/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @category		Kisma_Components
 * @package			kisma.components
 * @since			v1.0.0
 * @filesource
 */
namespace Kisma\Components
{
	/**
	 * Service
	 * The base class for services provided
	 *
	 * Provides two event handlers:
	 *
	 * onBeforeServiceCall and onAfterServiceCall which are called before and after
	 * the service is run, respectively.
	 *
	 * @property int $serviceType The type of service provided by this object
	 * @property string $serviceName The name of this service
	 */
	abstract class Service extends Seed implements \Kisma\IComponentService
	{
		//********************************************************************************
		//* Private Members
		//********************************************************************************

		/**
		 * @var string The name of this service
		 */
		protected $_serviceName = null;

		//*************************************************************************
		//* Event Handlers
		//*************************************************************************

		/**
		 * @param \Kisma\Event\ServiceEvent $event
		 *
		 * @return bool
		 */
		public function onBeforeServiceCall( \Kisma\Event\ServiceEvent $event )
		{
			//	Default implementation
			return true;
		}

		/**
		 * @param \Kisma\Event\ServiceEvent $event
		 *
		 * @return bool
		 */
		public function onAfterServiceCall( \Kisma\Event\ServiceEvent $event )
		{
			//	Default implementation
			return true;
		}

		//********************************************************************************
		//* Property Accessors
		//********************************************************************************

		/**
		 * @param string $serviceName
		 *
		 * @return \Kisma\Components\Service
		 */
		public function setServiceName( $serviceName )
		{
			$this->_serviceName = $serviceName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getServiceName()
		{
			return $this->_serviceName;
		}

	}
}