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
 * Model
 * Represents a set of data
 *
 * @throws \Kisma\ModelException
 *
 * @property array $fields
 * @property ValidationRule[] $rules
 *
 * @event beforeValidate
 * @event afterValidate
 */
abstract class Model extends \Kisma\Components\Component implements \Kisma\IModel
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var array The fields within this model
	 */
	protected $_fields = array();
	/**
	 * @var ValidationRule[] The validation rules for this model
	 */
	protected $_rules = array();

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array|null $fields
	 * @return bool
	 */
	public function validate( $fields = null )
	{
		$_fail = false;

		//	Allow for a single column name without array
		if ( null !== $fields && !is_array( $fields ) )
		{
			$fields = array( $fields );
		}

		//	Upstream validation failed
		$_event = new \Kisma\Events\ModelEvent(
			$this,
			array(
				'fields' => $fields
			)
		);

		if ( !$this->trigger( 'before_validate', $_event ) )
		{
			return false;
		}

		//	Check each rule
		foreach ( $this->_rules as $_rule )
		{
			if ( ! $_rule->validate( $this, $fields ) )
			{
				$_fail = true;
				break;
			}
		}

		//	Failed after validate
		if ( !$this->trigger( 'after_validate', $_event ) )
		{
			return false;
		}

		//	Guess we're cool
		return !$_fail && empty( $this->_errors );
	}

	/**
	 * Adds a new error to the specified column
	 * @param string $field
	 * @param Exception|string $error
	 * @return \Kisma\Components\Model
	 */
	public function addError( $field, $error )
	{
		$this->_errors[K::standardizeName( $field )][] = $error;
		return $this;
	}

	/**
	 * @param $errors
	 * @return \Kisma\Components\Model
	 */
	public function addErrors( $errors )
	{
		foreach ( $errors as $_field => $_error )
		{
			$this->addError( K::standardizeName( $_field ), $_error );
		}

		return $this;
	}

	/**
	 * Removes errors for all attributes or a single attribute.
	 * @param array|string|null $fields
	 * @return \Kisma\Components\Model
	 */
	public function clearErrors( $fields = null )
	{
		if ( null === $fields )
		{
			unset( $this->_errors );
			$this->_errors = array();
		}
		else
		{
			//	Allow for single field syntax
			if ( !is_array( $fields ) )
			{
				$fields = array( $fields );
			}

			//	Selective removal...
			foreach ( $fields as $_field )
			{
				$_field = K::standardizeName( $_field );

				if ( isset( $this->_errors[$_field] ) )
				{
					unset( $this->_errors[$_field] );
				}
			}
		}

		return $this;
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * @abstract
	 * @param ValidationRule $rule
	 * @param null $fields
	 * @return void
	 */
	abstract protected function _checkValidationRule( ValidationRule $rule, $fields = null );

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param $fields
	 * @return \Kisma\Components\Model
	 */
	public function setFields( $fields )
	{
		$this->_fields = $fields;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getFields()
	{
		return $this->_fields;
	}

	/**
	 * @param $validationRules
	 * @return \Kisma\Components\Model
	 */
	public function setRules( $validationRules )
	{
		$this->_rules = $validationRules;
		return $this;
	}

	/**
	 * @return array|\Kisma\Components\ValidationRule[]
	 */
	public function getRules()
	{
		return $this->_rules;
	}
}
