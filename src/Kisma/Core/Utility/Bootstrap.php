<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
 *
 * Kisma(tm) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kisma(tm) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kisma(tm).  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Kisma\Core\Utility;

use Kisma\Core\Interfaces\FormTypes;
use Kisma\Core\Interfaces\UtilityLike;

/**
 * Bootstrap class
 * Provides Twitter Bootstrap form-building
 */
class Bootstrap extends HtmlMarkup implements FormTypes, UtilityLike
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The block pattern for forms
	 */
	const VerticalGroupPattern
		= <<<HTML
	%%__LABEL__%%
	%%__INPUT__%%
	%%__HELP_BLOCK__%%
HTML;

	/**
	 * @var string The block pattern for horizontal forms
	 */
	const HorizontalGroupPattern
		= <<<HTML
<div class="control-group">
	%%__LABEL__%%
	<div class="controls">
		%%__INPUT__%%
		%%__HELP_BLOCK__%%
	</div>
</div>
HTML;

	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var string If set, pre-pended to id and name attributes (id=prefix_field,name=prefix[field])
	 */
	protected $_prefix = null;
	/**
	 * @var string The HTML of the form
	 */
	protected $_contents = null;
	/**
	 * @var string
	 */
	protected $_blockPattern = self::VerticalGroupPattern;
	/**
	 * @var string
	 */
	protected $_formType = self::Vertical;
	/**
	 * @var bool If true, the form is built into the $contents property and the self::renderForm method is used to output the results.
	 */
	protected $_builderMode = true;
	/**
	 * @var array The form attributes
	 */
	protected $_attributes = array();
	/**
	 * @var array The default form data
	 */
	protected $_formData = array();
	/**
	 * @var string CSRF token junk
	 */
	protected $_csrf = null;
	/**
	 * @var string
	 */
	protected $_removePrefix = null;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param string      $formType
	 * @param array       $attributes
	 * @param string|null $prefix
	 *
	 * @return \Kisma\Core\Utility\Bootstrap
	 */
	public function __construct( $formType = self::Vertical, $attributes = array(), $prefix = null )
	{
		$this->_formType = $formType;
		$this->_prefix = $prefix;
		$this->_attributes = $attributes;
		$this->_contents = null;

		if ( class_exists( '\\Yii', false ) && \Yii::app()->getRequest()->enableCsrfValidation )
		{
			$_tokenName = \Yii::app()->getRequest()->csrfTokenName;
			$_tokenValue = \Yii::app()->getRequest()->csrfToken;

			$this->_csrf
				= <<<HTML
<input type="hidden" name="{$_tokenName}" value="{$_tokenValue}" />
HTML;
		}
	}

	/**
	 * @param string $label
	 * @param array  $labelAttributes
	 * @param array  $inputAttributes
	 * @param string $wrapper
	 *
	 * @return string
	 */
	public static function textFormGroup( $label = null, $labelAttributes = array(), $inputAttributes = array(), $wrapper = null )
	{
		$_label = static::label( array_merge( $labelAttributes, array( 'class' => 'form-label' ) ), $label );
		$_input = static::text( array_merge( $inputAttributes, array( 'class' => 'form-control' ) ), null, $wrapper );

		return static::div( array( 'class' => 'form-group' ), $_label . $_input );
	}

	/**
	 * @param array  $fields
	 * @param string $prefix
	 */
	public function renderFields( array $fields, $prefix = null )
	{
		$_html = null;

		if ( null !== $prefix )
		{
			$this->_prefix = $prefix;
		}

		foreach ( $fields as $_section => $_fields )
		{
			$_inner = static::wrap( 'legend', $_section );

			if ( !is_array( $_fields ) )
			{
				$_fields = array( $_fields );
			}

			foreach ( $_fields as $_name => $_field )
			{
				if ( false !== Option::get( $_field, 'private', false ) )
				{
					continue;
				}

				if ( !isset( $_field['id'] ) )
				{
					$_field['id'] = $_name;
				}

				$_contents = ( !isset( $_field['contents'] ) && isset( $_field['value'] )
					? $_field['value']
					: Option::get( $_field,
						'contents',
						null,
						true ) );

				$_inner .= $this->field( Option::get( $_field, 'type', 'text', true ),
					$_field,
					$_contents );
			}

			$_html .= static::wrap( 'fieldset', $_inner );
		}

		echo $_html;
	}

	/**
	 * @param string $type
	 * @param array  $attributes
	 * @param string $contents
	 *
	 * @return string
	 */
	public function field( $type, array $attributes = array(), $contents = null )
	{
		$_html = null;
		$_type = strtolower( trim( $type ) );
		$_wrapInput = Scalar::in( $_type, 'checkbox', 'radio' );
		$_labelEnd = null;
		$_labelAttributes = array();
		$_id = Option::get( $attributes, 'id', Option::get( $attributes, 'name' ) );
		$_inputAppend = Option::get( $attributes, 'append', null, true );
		$_inputPrepend = Option::get( $attributes, 'prepend', null, true );

		$this->cleanNames( $attributes );

		$_label = Option::get( $attributes, 'label', null, true );

		if ( is_array( $_label ) )
		{
			$_labelAttributes = Option::get( $_label, 'attributes', array() );
			$_labelText = Option::get( $_label, 'value', array() );
		}
		else
		{
			$_labelText = $_label;
		}

		//	Add .control-label for the class to labels
		if ( static::Horizontal == $this->_formType )
		{
			if ( !isset( $_labelAttributes['class'] ) )
			{
				$_labelAttributes['class'] = null;
			}

			$_labelAttributes['class'] = static::addValue( $_labelAttributes['class'], ( $_wrapInput ? $_type : 'control-label' ) );
		}

		$_label = static::tag( 'label', $_labelAttributes, ( $_wrapInput ? null : $_labelText ), !$_wrapInput );
		$_labelEnd = ( $_wrapInput ? $_label . '</label>' : null );

		if ( null !== ( $_hint = Option::get( $attributes, 'hint', null, true ) ) )
		{
			$_hint = static::tag( 'span', array( 'class' => 'help-block' ), $_hint );
		}

		$_blockStart = $_blockEnd = null;
		$_inputStart = $_inputEnd = null;
		$_inputWrap = trim( ( $_inputAppend ? 'input-append' : null ) . ' ' . ( $_inputPrepend ? 'input-prepend' : null ) );

		switch ( $this->_formType )
		{
			case static::Horizontal:

				$_blockStart = static::tag( 'div', array( 'class' => 'control-group' ), null, false );
				$_inputStart = static::tag( 'div', array( 'class' => 'controls' ), null, false );
				$_inputEnd = $_blockEnd = '</div>';

				if ( !$_wrapInput )
				{
					$_blockStart .= $_label . $_labelEnd;
					$_label = $_labelEnd = null;
				}
				break;

			case static::Search:
				$_class = Option::get( $attributes, 'class' );
				$attributes['class'] = static::addValue( $_class, 'search-query' );
				break;
		}

		if ( null === $contents && !empty( $this->_formData ) )
		{
			$contents = Option::get( $this->_formData, $_id );
		}

		//	X-editable?
		if ( false !== stripos( Option::get( $attributes, 'class' ), 'x-editable' ) )
		{
			//	Replace contents with the x-editable link...
			$contents = static::tag( 'a',
				array(
					'class'               => 'x-editable',
					'href'                => '#',
					'id'                  => $_id,
					'data-type'           => $_type,
					'data-pk'             => Option::get( $this->_attributes, 'x-editable-pk', Option::get( $this->_formData, 'id' ) ),
					'data-url'            => Option::get( $this->_attributes,
						'x-editable-url',
						Option::get( $attributes, 'x-editable-url', null, true ) ),
					'data-original-title' => $_labelText,
					'data-inputclass'     => Option::get( $attributes, 'class', 'input-xlarge' ),
				),
				$contents );

			//	New type of just HTML...
			$_type = 'html';
		}

		switch ( $_type )
		{
			case 'html':
				$_input = $contents;
				break;

			case 'textarea':
				$attributes['class'] = static::addValue( Option::get( $attributes, 'class' ), 'input-xxlarge' );

				$_input = static::wrap( $_type,
					$contents,
					$attributes );
				break;

			case 'select':
				if ( !isset( $attributes['value'] ) )
				{
					$attributes['value'] = $contents;
				}

				$_input = static::select( Option::get( $attributes, 'data', array(), true ),
					$attributes );
				break;

			case 'button':
				$_input = static::button( $attributes, $contents );
				break;

			default:
				$_input = $this->_handleUnknownField( $_type, $attributes, $contents );
				break;
		}

		if ( !empty( $_inputWrap ) )
		{
			$_input = '<div class="' . $_inputWrap . '">' . $_inputPrepend . $_input . $_inputAppend . '</div>';
		}

		$_html
			= <<<HTML
{$_blockStart}{$_inputStart}{$_label}{$_input}{$_labelEnd}{$_hint}{$_inputEnd}{$_blockEnd}
HTML;

		if ( false !== $this->_builderMode )
		{
			$this->_contents .= $_html;
		}

		return $_html;
	}

	/**
	 * @param string $type
	 * @param array  $attributes
	 * @param string $contents
	 *
	 * @return string
	 */
	protected function _handleUnknownField( $type, array $attributes = array(), $contents = null )
	{
		if ( !isset( $attributes['value'] ) )
		{
			$attributes['value'] = $contents;
		}

		if ( null !== ( $_length = Option::get( $attributes, 'maxlength' ) ) )
		{
			$_class = Option::get( $attributes, 'class' );

			if ( $_length <= 64 )
			{
				$attributes['class'] = static::addValue( $_class, 'input-large' );
			}
			else if ( $_length < 128 )
			{
				$attributes['class'] = static::addValue( $_class, 'input-xlarge' );
			}
			else if ( $_length >= 128 )
			{
				$attributes['class'] = static::addValue( $_class, 'input-xxlarge' );
			}
		}

		$attributes = Convert::kvpToString( $attributes );

		return <<<HTML
<input type="{$type}" {$attributes}>
HTML;
	}

	/**
	 * @param string $legend
	 * @param string $submitButton
	 * @param array  $attributes
	 *
	 * @internal param bool $submit
	 * @return string
	 */
	public function renderForm( $legend = null, $submitButton = 'Submit', array $attributes = array() )
	{
		if ( static::VerticalGroupPattern !== $this->_blockPattern )
		{
			$attributes['class'] = static::addValue( Option::get( $attributes, 'class', array() ), 'form' . $this->_formType );
		}

		$_html = Convert::kvpToString( $attributes );

		if ( !empty( $legend ) )
		{
			$legend = static::wrap( 'legend', $legend );
		}

		$_submit = null;

		if ( !empty( $submitButton ) )
		{
			$_submit = $this->button( 'submit',
				array(
					'text' => ( true === $submitButton ? 'Submit' : $submitButton ),
				) );
		}

		$_html
			= <<<HTML
<form {$_html}>
	{$legend}
	{$this->_contents}
	<div class="form-actions">
	{$_submit}
	</div>
	{$this->_csrf}
</form>
HTML;

		$this->_contents = null;

		return $_html;
	}

	/**
	 * @param array $attributes
	 */
	public function cleanNames( array &$attributes = array() )
	{
		$_id = Option::get( $attributes, 'id' );
		$_name = $_savedName = Option::get( $attributes, 'name', $_id );

		if ( null === $_id )
		{
			$_id = $_name;
		}

		if ( !empty( $this->_removePrefix ) )
		{
			$_savedName = str_replace( $this->_removePrefix, null, $_id );
		}

		if ( null === ( $_label = Option::get( $attributes, 'label', null, true ) ) )
		{
			$_label = ucwords( str_replace( array( '_text', '_nbr', '_ind', '_blob', '_' ),
				array( null, null, null, null, ' ' ),
				$_savedName ) );
		}

		if ( null !== $this->_prefix )
		{
			$_id = $this->_prefix . '_' . $_id;
			$_name = $this->_prefix . '[' . $_name . ']';
		}

		$attributes['id'] = $_id;
		$attributes['name'] = $_name;
		$attributes['label'] = $_label;
	}

	/**
	 * @param array  $items
	 * @param string $logoutUrl Set to the URL used for logout. Leave null to not create a logout option.
	 *
	 * @return null|string
	 */
	public static function buildMenuItems( array $items = array(), $logoutUrl = null )
	{
		$_liTags = null;

		foreach ( $items as $_linkName => $_menuItem )
		{
			$_icon = null;

			if ( !is_array( $_menuItem ) )
			{
				$_liTags .= $_menuItem;
			}
			else
			{
				if ( null !== ( $_icon = Option::get( $_menuItem, 'icon' ) ) )
				{
					$_icon = static::wrap( 'i', null, array( 'class' => 'icon-' . $_icon ) );
				}

				$_class = $_menuItem['active'] ? 'active' : 'inactive';

				$_liTags
					.= <<<HTML
<li class="{$_class}"><a href="{$_menuItem['href']}">{$_icon}{$_linkName}</a></li>
HTML;
			}
		}

		if ( !empty( $logoutUrl ) )
		{
			$_token
				= ( class_exists( '\\Yii', false ) && false !== \Yii::app()->getRequest()->enableCsrfValidation ) ?
				'?token=' . \Yii::app()->getRequest()->getCsrfToken() : null;

			$_liTags
				.= <<<HTML
<li class="pull-right"><a href="{$logoutUrl}{$_token}">Logout</a></li>
HTML;
		}

		return $_liTags;
	}

	/**
	 * @param string $contents
	 *
	 * @return Bootstrap
	 */
	public function setContents( $contents )
	{
		$this->_contents = $contents;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getContents()
	{
		return $this->_contents;
	}

	/**
	 * @param string $prefix
	 *
	 * @return Bootstrap
	 */
	public function setPrefix( $prefix )
	{
		$this->_prefix = $prefix;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->_prefix;
	}

	/**
	 * @param array $attributes
	 *
	 * @return Bootstrap
	 */
	public function setAttributes( $attributes )
	{
		$this->_attributes = $attributes;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}

	/**
	 * @param string $blockPattern
	 *
	 * @return Bootstrap
	 */
	public function setBlockPattern( $blockPattern )
	{
		$this->_blockPattern = $blockPattern;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getBlockPattern()
	{
		return $this->_blockPattern;
	}

	/**
	 * @param boolean $builderMode
	 *
	 * @return Bootstrap
	 */
	public function setBuilderMode( $builderMode )
	{
		$this->_builderMode = $builderMode;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getBuilderMode()
	{
		return $this->_builderMode;
	}

	/**
	 * @param string $formType
	 *
	 * @return Bootstrap
	 */
	public function setFormType( $formType )
	{
		$this->_formType = $formType;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFormType()
	{
		return $this->_formType;
	}

	/**
	 * @param array $formData
	 *
	 * @return Bootstrap
	 */
	public function setFormData( $formData )
	{
		$this->_formData = $formData;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getFormData()
	{
		return $this->_formData;
	}

	/**
	 * @param string $removePrefix
	 *
	 * @return Bootstrap
	 */
	public function setRemovePrefix( $removePrefix )
	{
		$this->_removePrefix = $removePrefix;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRemovePrefix()
	{
		return $this->_removePrefix;
	}
}
