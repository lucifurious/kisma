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
 * @namespace		\Kisma\Components
 * @since			v1.0.0
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
 * Service
 * The base class for services provided
 */
class Service extends Component implements \Kisma\IService
{
	//********************************************************************************
	//* Constants
	//********************************************************************************

	//********************************************************************************
	//* Properties
	//********************************************************************************

	/**
	 * @var \Kisma\ServiceType The type of service provided by this object
	 */
	protected $_serviceType = \Kisma\ServiceType::AllPurpose;

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * @param \Kisma\Components\Event $event
	 * @return bool
	 */
	public function onBeforeServiceCall( $event )
	{
		//	Default implementation
		return true;
	}

	/**
	 * @param \Kisma\Components\Event $event
	 * @return bool
	 */
	public function onAfterServiceCall( $event )
	{
		//	Default implementation
		return true;
	}

	//********************************************************************************
	//* Property Accessors
	//********************************************************************************

	/**
	 * @param \Kisma\ServiceType $serviceType
	 * @return \Kisma\Components\Service
	 */
	public function setServiceType( $serviceType )
	{
		$this->_serviceType = $serviceType;
		return $this;
	}

	/**
	 * @return \Kisma\ServiceType
	 */
	public function getServiceType()
	{
		return $this->_serviceType;
	}

}