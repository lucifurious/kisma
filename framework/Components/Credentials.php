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
 * Credentials
 * A simple class to encapsulate access credentials
 */
class Credentials extends Component
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string
	 */
	protected $_userName = null;
	/**
	 * @var string
	 */
	protected $_password = null;
	/**
	 * @var string
	 */
	protected $_location = null;
	/**
	 * @var string
	 */
	protected $_identifier = null;
	/**
	 * @var string
	 */
	protected $_primaryAccessKey = null;
	/**
	 * @var string
	 */
	protected $_secondaryAccessKey = null;
	/**
	 * @var string
	 */
	protected $_tertiaryAccessKey = null;

	//********************************************************************************
	//* Property Accessors
	//********************************************************************************

	/**
	 * @param string $identifier
	 * @return \Kisma\Components\Credentials $this
	 */
	public function setIdentifier( $identifier )
	{
		$this->_identifier = $identifier;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->_identifier;
	}

	/**
	 * @param string $location
	 * @return \Kisma\Components\Credentials $this
	 */
	public function setLocation( $location )
	{
		$this->_location = $location;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLocation()
	{
		return $this->_location;
	}

	/**
	 * @param string $password
	 * @return \Kisma\Components\Credentials $this
	 */
	public function setPassword( $password )
	{
		$this->_password = $password;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->_password;
	}

	/**
	 * @param string $primaryAccessKey
	 * @return \Kisma\Components\Credentials $this
	 */
	public function setPrimaryAccessKey( $primaryAccessKey )
	{
		$this->_primaryAccessKey = $primaryAccessKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrimaryAccessKey()
	{
		return $this->_primaryAccessKey;
	}

	/**
	 * @param string $secondaryAccessKey
	 * @return \Kisma\Components\Credentials $this
	 */
	public function setSecondaryAccessKey( $secondaryAccessKey )
	{
		$this->_secondaryAccessKey = $secondaryAccessKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSecondaryAccessKey()
	{
		return $this->_secondaryAccessKey;
	}

	/**
	 * @param string $tertiaryAccessKey
	 * @return \Kisma\Components\Credentials $this
	 */
	public function setTertiaryAccessKey( $tertiaryAccessKey )
	{
		$this->_tertiaryAccessKey = $tertiaryAccessKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTertiaryAccessKey()
	{
		return $this->_tertiaryAccessKey;
	}

	/**
	 * @param string $userName
	 * @return \Kisma\Components\Credentials $this
	 */
	public function setUserName( $userName )
	{
		$this->_userName = $userName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUserName()
	{
		return $this->_userName;
	}
}
