<?php
/**
 * Bootstrap.php
 */
namespace Kisma\Core\Utility;
use Kisma\Core\Interfaces\InputTypes;

/**
 * Bootstrap class
 * Provides Twitter Bootstrap form-building
 */
class Bootstrap implements \Kisma\Core\Interfaces\FormTypes
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string
	 */
	protected static $_formType = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param string $type
	 * @param string $legend
	 * @param array  $attributes
	 *
	 * @return string
	 */
	public static function startForm( $type = self::Vertical, $legend = null, array $attributes = array() )
	{
		if ( is_array( $type ) )
		{
			$attributes = $type;
			$type = self::Vertical;
		}

		if ( self::Vertical !== $type )
		{
			$_class = Markup::addValue( Option::get( $attributes, 'class', array() ), 'form' . $type );
			$attributes['class'] = $_class;
		}

		self::$_formType = $type;
		$_html = Markup::kvpToString( $attributes );

		if ( !empty( $legend ) )
		{
			$legend = Markup::wrap( 'legend', $legend );
		}

		return <<<HTML
<form {$_html}>
	{$legend}
HTML;

	}

	/**
	 * @param string $formStart The form HTML up to this point...
	 * @param bool   $submitButton
	 *
	 * @return string
	 */
	public static function endForm( $formStart, $submitButton = true )
	{
		$_submit = null;

		if ( !empty( $submitButton ) )
		{
			$_submit = self::addButton(
				array(
					'type' => 'submit',
					'text' => ( true === $submitButton ? 'Submit' : $submitButton ),
				)
			);

		}

		$_formStart = str_ireplace( '%%__BUTTONS__%%', $_submit, $formStart );

		self::$_formType = null;

		return <<<HTML
{$_formStart}</form>
HTML;

	}

	/**
	 * @param array $attributes
	 *
	 * @return string
	 */
	public static function addButton( array $attributes = array() )
	{
		$_type = Option::get( $attributes, 'type', 'button', true );
		$_class = Option::get( $attributes, 'class' );
		$_text = Option::get( $attributes, 'text', 'Click Me!' );
		$attributes['class'] = Markup::addValue( $_class, 'btn' );
		$attributes = Markup::kvpToString( $attributes );

		$_html = <<<HTML
<button type="{$_type}" {$attributes}>{$_text}</button>
HTML;

		return $_html;
	}

	/**
	 * @param array $attributes
	 *
	 * @return string
	 */
	public static function addInput( array $attributes = array() )
	{
		$_type = trim( strtolower( Option::get( $attributes, 'type', InputTypes::Text, true ) ) );
		$_labelAttributes = Option::get( $attributes, 'labelAttributes', array(), true );
		$_wrapInput = Scalar::in( $_type, 'checkbox', 'radio' );

		$_labelEnd = null;

		$_id = Option::get( $attributes, 'id' );
		$_name = Option::get( $attributes, 'name' );

		if ( null === $_id )
		{
			$_id = $_name;
		}

		if ( null !== ( $_label = Option::get( $attributes, 'label', null, true ) ) )
		{
			if ( null !== $_id && !isset( $_labelAttributes['for'] ) )
			{
				$_labelAttributes['for'] = $_id;
			}

			//	Add .control-label for the class to labels
			if ( self::Horizontal == self::$_formType )
			{
				$_class = Option::get( $attributes, 'class', null );
				$_labelAttributes['class'] = Markup::addValue( $_class, ( $_wrapInput ? $_type : 'control-label' ) );
			}

			$_label = Markup::tag( 'label', $_labelAttributes, ( $_wrapInput ? null : $_label ), !$_wrapInput );
			$_labelEnd = ( $_wrapInput ? $_label . '</label>' : null );
		}

		if ( null !== ( $_hint = Option::get( $attributes, 'hint', null, true ) ) )
		{
			$_hint = Markup::tag( 'span', array( 'class'=> 'help-block' ), $_hint );
		}

		$_blockStart = $_blockEnd = null;
		$_inputStart = $_inputEnd = null;

		switch ( self::$_formType )
		{
			case self::Horizontal:
				$_blockStart = Markup::tag( 'div', array( 'class'=> 'control-group' ), null, false );
				$_inputStart = Markup::tag( 'div', array( 'class'=> 'controls' ), null, false );
				$_inputEnd = $_blockEnd = '</div>';
				break;

			case self::Search:
				$_class = Option::get( $attributes, 'class' );
				$attributes['class'] = Markup::addValue( $_class, 'search-query' );
				break;
		}

		$attributes = Markup::kvpToString( $attributes );

		$_html = <<<HTML
{$_blockStart}{$_inputStart}{$_label}<input type="{$_type}" {$attributes}>{$_labelEnd}%%__BUTTONS__%%{$_hint}{$_inputEnd}{$_blockEnd}
HTML;

		return $_html;
	}

	/**
	 * @param $formType
	 */
	public static function setFormType( $formType )
	{
		self::$_formType = $formType;
	}

	/**
	 * @return null
	 */
	public static function getFormType()
	{
		return self::$_formType;
	}

}
