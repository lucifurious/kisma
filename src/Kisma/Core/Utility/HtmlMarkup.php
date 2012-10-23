<?php
/**
 * HtmlMarkup.php
 */
namespace Kisma\Core\Utility;
/**
 * HtmlMarkup class
 * Provides HTML markup functions
 */
class HtmlMarkup extends Markup
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Makes a <DIV>
	 *
	 * @param array  $attributes
	 * @param string $value
	 * @param bool   $close
	 *
	 * @return string
	 */
	public function div( array $attributes = array(), $value = null, $close = true )
	{
		return static::tag( 'div', $attributes, $value, $close );
	}

	/**
	 * Makes a <SPAN>
	 *
	 * @param array  $attributes
	 * @param string $value
	 * @param bool   $close
	 *
	 * @return string
	 */
	public function span( array $attributes = array(), $value = null, $close = true )
	{
		return static::tag( 'span', $attributes, $value, $close );
	}

	/**
	 * Makes an <INPUT>
	 *
	 * @param array  $attributes
	 * @param string $value
	 *
	 * @return string
	 */
	public function input( array $attributes = array(), $value = null )
	{
		return static::tag( 'input', $attributes, $value );
	}

	/**
	 * Makes a <SELECT>
	 *
	 * @param array $options    An array of options for the select box
	 * @param array $attributes Attributes for the SELECT
	 *
	 * @return string
	 */
	public static function select( array $options = array(), array $attributes = array() )
	{
		$_html = null;

		foreach ( $options as $_key => $_value )
		{
			$_html .= static::tag( 'option', array( 'value' => $_key ), $_value );
		}

		return static::tag( 'select', $attributes, $_html );
	}

	/**
	 * Makes an <A> tag
	 *
	 * @param array  $attributes
	 * @param string $value
	 *
	 * @return string
	 */
	public static function a( array $attributes = array(), $value = null )
	{
		return static::tag( 'a', $attributes, $value );
	}

	/**
	 * @param array  $attributes
	 * @param string $value
	 *
	 * @return string
	 */
	public static function radioButton( array $attributes = array(), $value = null )
	{
		$attributes['type'] = 'radio';

		return static::tag( 'input', $attributes, $value );
	}

	/**
	 * @param array  $attributes
	 * @param string $value
	 *
	 * @return string
	 */
	public static function checkbox( array $attributes = array(), $value = null )
	{
		$attributes['type'] = 'checkbox';

		return static::tag( 'input', $attributes, $value );
	}

	/**
	 * Makes a <STYLE> tag
	 *
	 * @param array  $attributes
	 * @param string $value
	 *
	 * @return string
	 */
	public static function style( array $attributes = array(), $value = null )
	{
		$attributes['type'] = 'text/css';

		return static::tag( 'style', $attributes, $value );
	}

	/**
	 * Makes a <LINK> tag
	 *
	 * @param array  $attributes
	 * @param string $value
	 *
	 * @return string
	 */
	public static function link( array $attributes = array(), $value = null )
	{
		$attributes['rel'] = 'stylesheet';
		$attributes['type'] = 'text/css';

		return static::tag( 'link', $attributes, $value, true, true );
	}

	/**
	 * Makes a <SCRIPT> tag
	 *
	 * @param array  $attributes
	 * @param string $value
	 *
	 * @return string the enclosed JavaScript
	 */
	public static function script( array $attributes = array(), $value = null )
	{
		$attributes['type'] = 'text/javascript';

		return static::tag( 'script', $attributes, $value );
	}

	/**
	 * Makes a <FORM> tag
	 *
	 * @param array  $attributes
	 * @param string $value
	 * @param bool   $close If true, tag will be closed
	 *
	 * @return string
	 */
	public static function form( array $attributes = array(), $value = null, $close = false )
	{
		return static::tag( 'form', $attributes, $value, $close );
	}

	/**
	 * Makes a <LEGEND> tag
	 *
	 * @param array  $attributes
	 * @param string $value
	 *
	 * @return string
	 */
	public static function legend( array $attributes = array(), $value = null )
	{
		return static::tag( 'legend', $attributes, $value );
	}

	/**
	 * Makes an <IMG> tag
	 *
	 * @param array  $attributes
	 *
	 * @return string
	 */
	public static function img( array $attributes = array() )
	{
		return static::tag( 'img', $attributes );
	}

	/**
	 * Makes a <BUTTON> tag
	 */
	public static function button( array $attributes = array(), $value = null )
	{
		return static::tag( 'button', $attributes, $value );
	}

	/**
	 * Makes a <LABEL> tag
	 */
	public static function label( array $attributes = array(), $value = null, $close = true )
	{
		return static::tag( 'label', $attributes, $value, $close );
	}

	/**
	 * Makes a <INPUT type="TEXT"> tag
	 */
	public static function text( array $attributes = array(), $value = null )
	{
		$attributes['type'] = 'text';

		return static::input( $attributes, $value );
	}

	/**
	 * Makes a <INPUT type="HIDDEN"> tag
	 */
	public static function hidden( array $attributes = array(), $value = null )
	{
		$attributes['type'] = 'hidden';

		return static::input( $attributes, $value );
	}

	/**
	 * Makes a <INPUT type="PASSWORD"> tag
	 */
	public static function password( array $attributes = array(), $value = null )
	{
		$attributes['type'] = 'password';

		return static::input( $attributes, $value );
	}

	/**
	 * Makes a <INPUT type="FILE"> tag
	 */
	public static function file( array $attributes = array(), $value = null )
	{
		$attributes['type'] = 'file';

		return static::input( $attributes, $value );
	}

	/**
	 * Makes a <TEXTAREA> tag
	 */
	public static function textarea( array $attributes = array(), $value = null )
	{
		return static::tag( 'textarea', $attributes, $value );
	}

}
