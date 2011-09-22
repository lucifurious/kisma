<?php
/**
 * Kisma(tm) : PHP Microframework (http://github.com/lucifurious/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/lucifurious/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/lucifurious/kisma/ Kisma(tm)
 * @license			http://github.com/lucifurious/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @package			kisma
 * @namespace		\Kisma\Components
 * @since			v1.0.0
 * @filesource
 */
namespace Kisma\Components;
/**
 *
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
	protected $_type = \Kisma\ServiceType::AllPurpose;
	/**
	 * @var bool If true, options will be validated
	 */
	protected $_validateOptions = true;
	/**
	 * @var array The options considered valid
	 */
	protected $_serviceOptions = array();
	/**
	 * @var boolean If true, callbacks will be validated
	 */
	protected $_validateCallbacks = true;
	/**
	 * @var array The callbacks considered valid
	 */
	protected $_serviceCallbacks = array();
	/**
	 * @var array The list of client-side callbacks
	 */
	protected $_callbacks = array();

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * @param \Kisma\Events\Event $event
	 * @return bool
	 */
	public function onBeforeServiceCall( $event )
	{
		//	Default implementation
		return true;
	}

	/**
	 * @param \Kisma\Events\Event $event
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
	 * @param array $callbacks
	 * @return \Kisma\Components\Service $this
	 */
	public function setCallbacks( $callbacks )
	{
		$this->_callbacks = $callbacks;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getCallbacks()
	{
		return $this->_callbacks;
	}

	/**
	 * @param array $serviceCallbacks
	 * @return \Kisma\Components\Service $this
	 */
	public function setServiceCallbacks( $serviceCallbacks )
	{
		$this->_serviceCallbacks = $serviceCallbacks;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getServiceCallbacks()
	{
		return $this->_serviceCallbacks;
	}

	/**
	 * @param array $serviceOptions
	 * @return \Kisma\Components\Service $this
	 */
	public function setServiceOptions( $serviceOptions )
	{
		$this->_serviceOptions = $serviceOptions;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getServiceOptions()
	{
		return $this->_serviceOptions;
	}

	/**
	 * @param \Kisma\ServiceType $type
	 * @return \Kisma\Components\Service $this
	 */
	public function setType( $type )
	{
		$this->_type = $type;
		return $this;
	}

	/**
	 * @return \Kisma\ServiceType
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @param boolean $validateCallbacks
	 * @return \Kisma\Components\Service $this
	 */
	public function setValidateCallbacks( $validateCallbacks )
	{
		$this->_validateCallbacks = $validateCallbacks;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getValidateCallbacks()
	{
		return $this->_validateCallbacks;
	}

	/**
	 * @param boolean $validateOptions
	 * @return \Kisma\Components\Service $this
	 */
	public function setValidateOptions( $validateOptions )
	{
		$this->_validateOptions = $validateOptions;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getValidateOptions()
	{
		return $this->_validateOptions;
	}

}