<?php
/**
 * @file
 * A base class for Kisma models
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Framework
 * @package kisma.components
 * @since 1.0.0
 *
 * @ingroup framework
 */

namespace Kisma\Components;

//*************************************************************************
//* Aliases
//*************************************************************************

use Kisma\Event\ModelEvent;

/**
 * Model
 */
abstract class Model extends \Kisma\Container\CouchDb\Document implements \Kisma\IModel
{
	//*************************************************************************
	//* Private Members 
	//*************************************************************************

	/**
	 * @var array
	 */
	protected $_validationErrors = array();

	//*************************************************************************
	//* Public Methods 
	//*************************************************************************

	/**
	 * Clears all errors on the model
	 *
	 * @param string|null $field
	 */
	public function resetErrors( $field = null )
	{
		if ( null !== $field )
		{
			unset( $this->_validationErrors[$field] );
		}
		else
		{
			$this->_validationErrors = array();
		}
	}

	/**
	 * Validate the model or the fields in $fields
	 *
	 * @param array $fields
	 * @param bool  $reset
	 *
	 * @return bool
	 */
	public function validate( $fields = array(), $reset = true )
	{
		$this->dispatch( ModelEvent::BeforeValidate, new ModelEvent( $this ) );

		if ( true !== $reset )
		{
			$this->resetErrors();
		}

		$this->dispatch( ModelEvent::AfterValidate, new ModelEvent( $this ) );

		return true;
	}

	/**
	 * Indicates that this model has errors
	 *
	 * @param string|null $field If not specified, all fields are checked
	 *
	 * @return bool
	 */
	public function hasValidationErrors( $field = null )
	{
		if ( null === $field )
		{
			return !empty( $this->_validationErrors );
		}

		return isset( $this->_validationErrors[$field] );
	}

	/**
	 * Returns the errors for all attribute or a single attribute.
	 *
	 * @param string $field attribute name. Use null to retrieve errors for all attributes.
	 *
	 * @param bool   $humanReadable If true, returns a string suitable for logging
	 *
	 * @return string|array
	 */
	public function getValidationErrors( $field = null, $humanReadable = false )
	{
		$_result =
			( null === $field ? $this->_validationErrors :
				( isset( $this->_validationErrors[$field] ) ? $this->_validationErrors[$field] : array() ) );

		return $humanReadable ? print_r( $_result, true ) : $_result;
	}

	/**
	 * Adds an error to a field
	 *
	 * @param string $field
	 * @param string $error
	 */
	public function addError( $field, $error )
	{
		$this->_validationErrors[$field][] = $error;
	}

	/**
	 * Adds a list of errors in a 'field' => 'error' hash
	 *
	 * @param array $errors
	 */
	public function addErrors( $errors = array() )
	{
		foreach ( $errors as $_field => $_error )
		{
			if ( is_array( $_error ) )
			{
				$_error = array( $_field => $_error );
			}

			foreach ( $_error as $_message )
			{
				$this->addError( $_field, $_message );
			}
		}
	}

	/**
	 * @param mixed $offset
	 *
	 * @return bool
	 */
	public function offsetExists( $offset )
	{
		return property_exists( $this, $offset );
	}

	/**
	 * @param mixed $offset
	 *
	 * @return bool
	 */
	public function offsetGet( $offset )
	{
		return $this->{$offset};
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function offsetSet( $offset, $value )
	{
		$this->{$offset} = $value;
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset( $offset )
	{
		unset( $this->{$offset} );
	}

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * Triggered after validation is complete.
	 *
	 * @param \Kisma\Event\ModelEvent $event
	 *
	 * @return bool
	 */
	public function onAfterValidate( \Kisma\Event\ModelEvent $event )
	{
		return true;
	}

	/**
	 * Triggered before validation begins.
	 *
	 * @param \Kisma\Event\ModelEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeValidate( \Kisma\Event\ModelEvent $event )
	{
		return true;
	}

}