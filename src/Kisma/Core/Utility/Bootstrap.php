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
class Bootstrap extends HtmlMarkup implements \Kisma\Core\Interfaces\FormTypes
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The block pattern for forms
	 */
	const VerticalGroupPattern = <<<HTML
	%%__LABEL__%%
	%%__INPUT__%%
	%%__HELP_BLOCK__%%
HTML;

	/**
	 * @var string The block pattern for horizontal forms
	 */
	const HorizontalGroupPattern = <<<HTML
<div class="control-group">
	%%__LABEL__%%
	<div class="controls">
		%%__INPUT__%%
		%%__HELP_BLOCK__%%
	</div>
</div>
HTML;

	//*************************************************************************
	//* Private Members
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

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array       $attributes
	 * @param string|null $prefix
	 *
	 * @return \Kisma\Core\Utility\Bootstrap
	 */
	public function __construct( $attributes = array(), $prefix = null )
	{
		$this->_prefix = $prefix;
		$this->_contents = Markup::openTag( 'form', $attributes );
	}

	/**
	 * @param string $type
	 * @param array  $attributes
	 * @param string $value
	 * @param bool   $close
	 * @param bool   $selfClose
	 *
	 * @return string
	 */
	public function field( $type, array $attributes = array(), $value = null, $close = true, $selfClose = false )
	{
		$_html = null;
		$_type = strtolower( trim( $type ) );
		$_wrapInput = Scalar::in( $_type, 'checkbox', 'radio' );
		$_labelEnd = null;

		$this->cleanNames( $attributes );

		$_id = $attributes['id'];
		$_label = $attributes['label'];

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
			$_hint = Markup::tag( 'span', array( 'class' => 'help-block' ), $_hint );
		}

		$_blockStart = $_blockEnd = null;
		$_inputStart = $_inputEnd = null;

		switch ( $this->_formType )
		{
			case self::Horizontal:
				$_blockStart = Markup::tag( 'div', array( 'class' => 'control-group' ), null, false );
				$_inputStart = Markup::tag( 'div', array( 'class' => 'controls' ), null, false );
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

		if ( 'html' == $_type )
		{
			$_input = $_html;
		}
		else
		{
			if ( 'textarea' == $_type )
			{
				$_input = Markup::wrap(
					$_type,
					Option::get( $attributes, 'value', null, true ),
					$attributes
				);
			}
			else
			{
				$attributes = Convert::kvpToString( $attributes );
				$_input = <<<HTML
<input type="{$_type}" {$attributes}>
HTML;
			}
		}

		$_html = <<<HTML
{$_blockStart}{$_inputStart}{$_label}{$_input}{$_labelEnd}{$_hint}{$_inputEnd}{$_blockEnd}
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

		$_html = Convert::kvpToString( $attributes );

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
	 * @param array $attributes
	 */
	public function cleanNames( array &$attributes = array() )
	{
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
		}

		$attributes['id'] = $_id;
		$attributes['name'] = $_name;
		$attributes['label'] = $_label;
	}

	/**
	 * @param array $items
	 * @param bool  $logout If true, a "logout" item will be appended to the menu bar
	 *
	 * @return null|string
	 */
	public static function buildMenuItems( array $items = array(), $logout = false )
	{
		$_liTags = null;

		foreach ( $items as $_linkName => $_menuItem )
		{
			$_class = $_menuItem['active'] ? 'active' : 'inactive';

			$_liTags .= <<<HTML
<li class="{$_class}"><a href="{$_menuItem['href']}">{$_linkName}</a></li>
HTML;
		}

		if ( false !== $logout )
		{
			$_liTags .= <<<HTML
<li class="pull-right"><a href="/app/logout/">Logout</a></li>
HTML;

		}

		return $_liTags;
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
