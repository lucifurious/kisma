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
class Bootstrap extends \Kisma\Core\Seed implements \Kisma\Core\Interfaces\FormTypes
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string
	 */
	protected $_formType = null;
	/**
	 * @var string If set, pre-pended to id and name attributes (id=prefix_field,name=prefix[field])
	 */
	protected $_prefix = null;
	/**
	 * @var string The HTML of the form
	 */
	protected $_contents = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array|null|object|string $formType
	 * @param string                   $prefix
	 * @param array                    $settings
	 */
	public function __construct( $formType = self::Vertical, $prefix = null, $settings = array() )
	{
		$this->_formType = $formType;
		$this->_prefix = $prefix;
		$this->_contents = null;

		parent::__construct( $settings );
	}

	/**
	 * @param string $type
	 * @param array  $attributes
	 *
	 * @return string
	 */
	public function input( $type, array $attributes = array() )
	{
		$_type = trim( strtolower( $type ) );
		$_labelAttributes = Option::get( $attributes, 'labelAttributes', array(), true );
		$_wrapInput = Scalar::in( $_type, 'checkbox', 'radio' );

		$_labelEnd = null;

		$_id = Option::get( $attributes, 'id' );
		$_name = Option::get( $attributes, 'name' );

		if ( null === $_id )
		{
			$_id = $_name;
		}

		if ( null === ( $_label = Option::get( $attributes, 'label', null, true ) ) )
		{
			$_label = ucwords( str_replace( '_', ' ', $_name ) );
		}

		if ( null !== $this->_prefix )
		{
			$_id = $this->_prefix . '_' . $_id;
			$_name = $this->_prefix . '[' . $_name . ']';
			$attributes['id'] = $_id;
			$attributes['name'] = $_name;
		}

		if ( null !== $_id && !isset( $_labelAttributes['for'] ) )
		{
			$_labelAttributes['for'] = $_id;
		}

		//	Add .control-label for the class to labels
		if ( self::Horizontal == $this->_formType )
		{
			$_class = Option::get( $attributes, 'class', null );
			$_labelAttributes['class'] = Markup::addValue( $_class, ( $_wrapInput ? $_type : 'control-label' ) );
		}

		$_label = Markup::tag( 'label', $_labelAttributes, ( $_wrapInput ? null : $_label ), !$_wrapInput );
		$_labelEnd = ( $_wrapInput ? $_label . '</label>' : null );

		if ( null !== ( $_hint = Option::get( $attributes, 'hint', null, true ) ) )
		{
			$_hint = Markup::tag( 'span', array( 'class'=> 'help-block' ), $_hint );
		}

		$_blockStart = $_blockEnd = null;
		$_inputStart = $_inputEnd = null;

		switch ( $this->_formType )
		{
			case self::Horizontal:
				$_blockStart = Markup::tag( 'div', array( 'class'=> 'control-group' ), null, false );
				$_inputStart = Markup::tag( 'div', array( 'class'=> 'controls' ), null, false );
				$_inputEnd = $_blockEnd = '</div>';

				if ( !$_wrapInput )
				{
					$_blockStart .= $_label . $_labelEnd;
					$_label = $_labelEnd = null;
				}
				break;

			case self::Search:
				$_class = Option::get( $attributes, 'class' );
				$attributes['class'] = Markup::addValue( $_class, 'search-query' );
				break;
		}

		$attributes = Markup::kvpToString( $attributes );

		$_html = <<<HTML
{$_blockStart}{$_inputStart}{$_label}<input type="{$_type}" {$attributes}>{$_labelEnd}{$_hint}{$_inputEnd}{$_blockEnd}
HTML;

		$this->_contents .= $_html;

		return $_html;
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
		if ( self::Vertical !== $this->_formType )
		{
			$_class = Markup::addValue( Option::get( $attributes, 'class', array() ), 'form' . $this->_formType );
			$attributes['class'] = $_class;
		}

		$_html = Markup::kvpToString( $attributes );

		if ( !empty( $legend ) )
		{
			$legend = Markup::wrap( 'legend', $legend );
		}

		$_submit = null;
		if ( !empty( $submitButton ) )
		{
			$_submit = $this->button(
				'submit',
				array(
					'text' => ( true === $submitButton ? 'Submit' : $submitButton ),
				)
			);
		}

		$_html = <<<HTML
<form {$_html}>
	{$legend}
	{$this->_contents}
	{$_submit}
</form>
HTML;

		$this->_contents = null;

		return $_html;
	}

	/**
	 * @param string $type
	 * @param array  $attributes
	 *
	 * @return string
	 */
	public function button( $type = 'submit', array $attributes = array() )
	{
		$_type = trim( strtolower( $type ) ) ? : 'button';
		$_class = Option::get( $attributes, 'class' );
		$_text = Option::get( $attributes, 'text', 'Click Me!' );
		$attributes['class'] = Markup::addValue( $_class, 'btn' );
		$attributes = Markup::kvpToString( $attributes );

		$_html = <<<HTML
<button type="{$_type}" {$attributes}>{$_text}</button>
HTML;

		return $_html;
	}

	//*************************************************************************
	//* [GS]etters
	//*************************************************************************

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

}
