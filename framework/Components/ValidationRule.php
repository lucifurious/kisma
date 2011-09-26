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
 * CValidator is the base class for all validators.
 *
 * Child classes must implement the {@link validateAttribute} method.
 *
 * The following properties are defined in CValidator:
 * <ul>
 * <li>{@link attributes}: array, list of attributes to be validated;</li>
 * <li>{@link message}: string, the customized error message. The message
 *   may contain placeholders that will be replaced with the actual content.
 *   For example, the "{attribute}" placeholder will be replaced with the label
 *   of the problematic attribute. Different validators may define additional
 *   placeholders.</li>
 * <li>{@link on}: string, in which scenario should the validator be in effect.
 *   This is used to match the 'on' parameter supplied when calling {@link CModel::validate}.</li>
 * </ul>
 *
 * When using {@link createValidator} to create a validator, the following aliases
 * are recognized as the corresponding built-in validator classes:
 * <ul>
 * <li>required: {@link CRequiredValidator}</li>
 * <li>filter: {@link CFilterValidator}</li>
 * <li>match: {@link CRegularExpressionValidator}</li>
 * <li>email: {@link CEmailValidator}</li>
 * <li>url: {@link CUrlValidator}</li>
 * <li>unique: {@link CUniqueValidator}</li>
 * <li>compare: {@link CCompareValidator}</li>
 * <li>length: {@link CStringValidator}</li>
 * <li>in: {@link CRangeValidator}</li>
 * <li>numerical: {@link CNumberValidator}</li>
 * <li>captcha: {@link CCaptchaValidator}</li>
 * <li>type: {@link CTypeValidator}</li>
 * <li>file: {@link CFileValidator}</li>
 * <li>default: {@link CDefaultValueValidator}</li>
 * <li>exist: {@link CExistValidator}</li>
 * <li>boolean: {@link CBooleanValidator}</li>
 * <li>date: {@link CDateValidator}</li>
 * <li>safe: {@link CSafeValidator}</li>
 * <li>unsafe: {@link CUnsafeValidator}</li>
 * </ul>
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CValidator.php 3113 2011-03-24 21:49:40Z qiang.xue $
 * @package system.validators
 * @since 1.0
 */
abstract class BaseValidationRule extends Component
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var array list of items to be validated.
	 */
	protected $_rule;
	/**
	 * @var string
	 */
	protected $_message = null;

	/**
	 * Validates a single attribute.
	 * This method should be overridden by child classes.
	 * @param CModel $model the data object being validated
	 * @param string $attribute the name of the attribute to be validated.
	 */
	abstract protected function validateAttribute($model,$attribute);

	/**
	 * Validates the specified model.
	 */
	public function validate( $model, $fields = null )
	{
		if(is_array($fields))
			$fields=array_intersect($this->attributes,$fields);
		else
			$fields=$this->attributes;
		foreach($fields as $attribute)
		{
			if(!$this->skipOnError || !$model->hasErrors($attribute))
				$this->validateAttribute($model,$attribute);
		}
	}

	/**
	 * Returns the JavaScript needed for performing client-side validation.
	 * Do not override this method if the validator does not support client-side validation.
	 * Two predefined JavaScript variables can be used:
	 * <ul>
	 * <li>value: the value to be validated</li>
	 * <li>messages: an array used to hold the validation error messages for the value</li>
	 * </ul>
	 * @param CModel $model the data object being validated
	 * @param string $attribute the name of the attribute to be validated.
	 * @return string the client-side validation script. Null if the validator does not support client-side validation.
	 * @see CActiveForm::enableClientValidation
	 * @since 1.1.7
	 */
	public function clientValidateAttribute($model,$attribute)
	{
	}

	/**
	 * Returns a value indicating whether the validator applies to the specified scenario.
	 * A validator applies to a scenario as long as any of the following conditions is met:
	 * <ul>
	 * <li>the validator's "on" property is empty</li>
	 * <li>the validator's "on" property contains the specified scenario</li>
	 * </ul>
	 * @param string $scenario scenario name
	 * @return boolean whether the validator applies to the specified scenario.
	 * @since 1.0.2
	 */
	public function applyTo($scenario)
	{
		return empty($this->on) || isset($this->on[$scenario]);
	}

	/**
	 * Adds an error about the specified attribute to the active record.
	 * This is a helper method that performs message selection and internationalization.
	 * @param CModel $model the data object being validated
	 * @param string $attribute the attribute being validated
	 * @param string $message the error message
	 * @param array $params values for the placeholders in the error message
	 */
	protected function addError($model,$attribute,$message,$params=array())
	{
		$params['{attribute}']=$model->getAttributeLabel($attribute);
		$model->addError($attribute,strtr($message,$params));
	}

	/**
	 * Checks if the given value is empty.
	 * A value is considered empty if it is null, an empty array, or the trimmed result is an empty string.
	 * Note that this method is different from PHP empty(). It will return false when the value is 0.
	 * @param mixed $value the value to be checked
	 * @param boolean $trim whether to perform trimming before checking if the string is empty. Defaults to false.
	 * @return boolean whether the value is empty
	 * @since 1.0.9
	 */
	protected function isEmpty($value,$trim=false)
	{
		return $value===null || $value===array() || $value==='' || $trim && is_scalar($value) && trim($value)==='';
	}
}

