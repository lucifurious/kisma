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
 * @category  Kisma_Container
 * @package   kisma.container
 * @namespace \Kisma\Container
 * @since	 v1.0.0
 * @filesource
 */
namespace Kisma\Container;

//*************************************************************************
//* Aliases
//*************************************************************************

use Kisma\Components;

/**
 * Document
 * A magical bare-bones generic document. This is the basis for document storage objects.
 *
 * @property array $fields
 */
class Document extends \Kisma\Components\Seed implements IContainer
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var array The document fields
	 */
	protected $_fields = null;

	//*************************************************************************
	//* Magic
	//*************************************************************************

	/**
	 * Checks if a document property is set
	 *
	 * @param string $property
	 *
	 * @return boolean
	 */
	public function __isset( $property )
	{
		return isset( $this->_fields[$property] );
	}

	/**
	 * Unsets a document property
	 *
	 * @param string $property
	 *
	 * @return \Kisma\Components\Document
	 */
	public function __unset( $property )
	{
		\Kisma\Utility\Option::unsetOption( $this->_fields, $property );
		\Kisma\Kisma::app()->dispatch( \Kisma\Event\ContainerEvent::ContentsModified, new \Kisma\Event\ContainerEvent( $this, $property, null ) );
		return $this;
	}

	/**
	 * Gets a property from the document
	 *
	 * @param string $property
	 *
	 * @return mixed|null
	 */
	public function __get( $property )
	{
		return \Kisma\Utility\Option::get( $this->_fields, $property );
	}

	/**
	 * Sets a property within the document
	 *
	 * @param string $property
	 * @param mixed  $value
	 *
	 * @return Document
	 */
	public function __set( $property, $value )
	{
		\Kisma\Utility\Option::set( $this->_fields, $property );
		\Kisma\Kisma::app()->dispatch( \Kisma\Event\ContainerEvent::ContentsModified, new \Kisma\Event\ContainerEvent( $this, $property, $value ) );
		return $this;
	}

	/**
	 * @return \stdClass
	 */
	public function toObject()
	{
		$_obj = new \stdClass();

		foreach ( $this->_fields as $_key => $_value )
		{
			$_obj->{$_key} = $_value;
		}

		return $_obj;
	}

	/**
	 * @return array
	 */
	public function __sleep()
	{
		return array(
			'fields',
		);
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * Set the current document. Calling with no parameters resets the object
	 *
	 * @param array|\stdClass $fields
	 *
	 * @return \Kisma\Container\Document
	 */
	public function setFields( $fields = array() )
	{
		if ( empty( $_fields ) )
		{
			$this->_fields = array();
		}

		if ( $fields instanceof \stdClass )
		{
			foreach ( $fields as $_field => $_value )
			{
				$this->_fields[$_field] = $_value;
			}
		}
		else
		{
			$this->_fields = $fields;
		}

		\Kisma\Kisma::app()->dispatch( \Kisma\Event\ContainerEvent::ContentsModified, new \Kisma\Event\ContainerEvent( $this ) );

		return $this;
	}

	/**
	 * @return array
	 */
	public function &getFields()
	{
		return $this->_fields;
	}

}
