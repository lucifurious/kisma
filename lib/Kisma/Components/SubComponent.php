<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link		  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license	   http://github.com/Pogostick/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @category	  Kisma|Components
 * @package	   kisma.components
 * @namespace	 \Kisma\Components
 * @since		 v1.0.0
 * @filesource
 */
namespace Kisma\Components;

	//*************************************************************************
	//* Requirements
//*************************************************************************

use Kisma\K;
use \Pimple;

/**
 * SubComponent
 * The seed within...
 *
 * Basics
 * ======
 * SubComponent is the base class of all Kisma classes. It's used mainly
 * to distinguish its more capable son, Component, from his sister
 * Aspect.
 *
 * Features
 * ========
 *   o  Property and option management
 *
 * Properties: Always exist, and always have a default value.
 * ===========================================================================
 *
 * @property array $options
 * @property \Exception[] $errors
 * @property int $index
 * @property-read int $count
 * @property boolean $skipNext
 * @property boolean $readOnly
 * @property boolean $logging
 */
abstract class SubComponent extends \Pimple implements \Kisma\IKisma, \Kisma\IConfigurable
{
	//*************************************************************************
	//* Default/Magic Methods
	//*************************************************************************

	/**
	 * The base component constructor
	 *
	 * @param array $options
	 *
	 * @return \Kisma\Components\SubComponent
	 */
	public function __construct( $options = array() )
	{
		//	Catch null input...
		if ( null === $options || !is_array( $options ) || empty( $options ) )
		{
			$options = array();
		}
		else
		{
			$options = K::cleanOptions( $options );
		}

		//	Merge the options...
		$this['options'] = array_merge( $this['options'], $options );

		//	Loop through, set...
		foreach ( $this['options'] as $_key => $_value )
		{
			try
			{
				K::__property( $this, $_key, \Kisma\AccessorMode::Set, $_value );
				unset( $this['options'][$_key] );
			}
			catch ( \Kisma\UndefinedPropertyException $_ex )
			{
				//	Undefined, add to options...
				// $_options[$_key] = $_value;
			}
		}
	}

	/**
	 * Destructor stub
	 */
	public function __destruct()
	{
		//	Does nothing, but allows references from subclasses
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * sets all options at once
	 *
	 * @param array $options
	 * @param bool  $overwriteAll
	 *
	 * @return \Kisma\Components\Component $this
	 */
	public function setOptions( $options = array(), $overwriteAll = true )
	{
		//	Bulk set all options
		if ( true !== $overwriteAll )
		{
			foreach ( $options as $_key => $_value )
			{
				$this->setOption( $_key, $_value );
			}
		}
		else
		{
			$this['options'] = $options;
		}

		return $this;
	}

	/**
	 * @param string	 $name
	 * @param mixed|null $value
	 *
	 * @return mixed
	 */
	public function setOption( $name, $value = null )
	{
		K::so( $this['options'], $name, $value );
		return $this;
	}

	/**
	 * @return array
	 */
	public function &getOptions()
	{
		return $this['options'];
	}

	/**
	 * @param string	 $name
	 * @param mixed|null $defaultValue
	 * @param bool	   $deleteAfter If true, key is removed from the option list after it is read.
	 *
	 * @return mixed
	 */
	public function getOption( $name, $defaultValue = null, $deleteAfter = false )
	{
		return K::o( $this['options'], $name, $defaultValue, $deleteAfter );
	}
}