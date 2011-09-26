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
 * @namespace Kisma\Aspects Kisma components
 */
namespace Kisma\Aspects;

/**
 * Convenience alias for the Kisma helpers
 * @see \Kisma\Kisma
 */
use \Kisma\Kisma as K;

/**
 * Aspect
 * Aspects allow objects to take on functionality defined in another class.
 * @TODO Replace with traits once PHP 5.4 is released
 */
class Aspect extends \Kisma\Components\Component implements \Kisma\IAspect
{
	//********************************************************************************
	//* Properties
	//********************************************************************************

	/**
	 * @var bool 
	 */
	protected $_enabled = true;
	/**
	 * @var \Kisma\Components\Component
	 */
	protected $_parent = null;

	//********************************************************************************
	//* Public Methods
	//********************************************************************************

	/**
	 * Link to a parent component
	 * @param \Kisma\Components\Component $parent
	 * @return \Aspect
	 */
	public function link( \Kisma\Components\Component $parent )
	{
		$this->_parent = $parent;

		//	Bind all my events at once
		foreach ( $this->_events as $_eventName => $_callback )
		{
			$this->_parent->bind( $_eventName, $_callback );
		}

		return $this;
	}

	/**
	 * Unlinks the aspect from a $parent
	 * @param \Kisma\Components\Component $parent
	 * @return \Aspect
	 */
	public function unlink( Component $parent )
	{
		//	Unbind all my events at once
		foreach ( $this->_events as $_eventName => $_callback )
		{
			$parent->unbind( $_eventName, $_callback );
		}

		return $this;
	}

	//********************************************************************************
	//* Property Accessors
	//********************************************************************************

	/**
	 * Enable/disable aspect
	 * @param bool $disable
	 * @return \Kisma\Aspects\Aspect
	 */
	public function enable( $disable = false )
	{
		$this->_enabled = !$disable;
		return $this;
	}

	/**
	 * @param boolean $enabled
	 * @return \Kisma\Aspects\Aspect $this
	 */
	public function setEnabled( $enabled = true )
	{
		$this->_enabled = $enabled;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getEnabled()
	{
		return $this->_enabled;
	}

	/**
	 * @param \Kisma\Components\Component $parent
	 * @return \Kisma\Aspects\Aspect $this
	 */
	public function setParent( $parent = null )
	{
		$this->_parent = $parent;
		return $this;
	}

	/**
	 * @return \Kisma\Components\Component
	 */
	public function getParent()
	{
		return $this->_parent;
	}

}
