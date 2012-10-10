<?php
/**
 * Markup.php
 */
namespace Kisma\Core\Utility;
/**
 * Markup
 * Markup related functions
 */
class Markup
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int The delimiter position
	 */
	const OpenStart = 0;
	/**
	 * @var int The delimiter position
	 */
	const OpenEnd = 1;
	/**
	 * @var int The delimiter position
	 */
	const CloseStart = 2;
	/**
	 * @var int The delimiter position
	 */
	const CloseEnd = 3;
	/**
	 * @var int The delimiter position
	 */
	const SelfCloseStart = 2;
	/**
	 * @var int The delimiter position
	 */
	const SelfCloseEnd = 3;

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var array The delimiters for the tags in this markup. This should handle any SGML-based markup
	 */
	protected static $_delimiters = array(
		self::OpenStart      => '<',
		self::OpenEnd        => '>',
		self::CloseStart     => '</',
		self::CloseEnd       => '>',
		self::SelfCloseStart => '<',
		self::SelfCloseEnd   => '/>',
	);
	/**
	 * @var bool If true, tags and attributes will be uppercased
	 */
	protected static $_uppercaseTags = false;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param string $name
	 * @param string $value
	 * @param array  $attributes
	 * @param bool   $close
	 * @param bool   $selfClose
	 *
	 * @return string
	 */
	public static function tag( $name, $attributes = array(), $value = null, $close = true, $selfClose = false )
	{
		//	Nah nah...
		if ( !$close && $selfClose )
		{
			$selfClose = false;
		}

		$_tags = array(
			self::$_delimiters[( $selfClose ? self::SelfCloseStart : self::OpenStart )] . ( self::$_uppercaseTags ? strtoupper( $name ) :
				strtolower( $name ) )
		);

		$_tags[] = self::kvpToString( $attributes );
		$_tags[] = self::$_delimiters[( $selfClose ? self::SelfCloseEnd : self::OpenEnd )];

		if ( !$selfClose )
		{
			if ( null !== $value )
			{
				$_tags[] = $value;
			}

			if ( $close )
			{
				$_tags[] = self::$_delimiters[self::CloseStart] . ( self::$_uppercaseTags ? strtoupper( $name ) : strtolower( $name ) );
			}
		}

		return trim( implode( ' ', $_tags ) );
	}

	/**
	 * @param string $tag
	 * @param string $value
	 * @param bool   $selfClose
	 *
	 * @return string
	 */
	public static function wrap( $tag, $value = null, $selfClose = false )
	{
		$_tags = array(
			self::$_delimiters[( $selfClose ? self::SelfCloseStart : self::OpenStart )] . ( self::$_uppercaseTags ? strtoupper( $tag ) :
				strtolower( $tag ) )
		);

		$_tags[] = self::$_delimiters[( $selfClose ? self::SelfCloseEnd : self::OpenEnd )];

		if ( !$selfClose )
		{
			if ( null !== $value )
			{
				$_tags[] = $value;
			}

			$_tags[] = self::$_delimiters[self::CloseStart] . ( self::$_uppercaseTags ? strtoupper( $tag ) : strtolower( $tag ) );
		}

		return trim( implode( ' ', $_tags ) );
	}

	/**
	 * Adds items to a list, ensuring uniqueness
	 *
	 * @param string|array $original   The original value
	 * @param string|array $value      The thing(s) to add
	 *
	 * @return array|string
	 */
	public static function addValue( $original, $value )
	{
		$_list = ( !is_array( $original ) ? explode( ' ', trim( $original ) ) : $original );

		foreach ( Option::clean( $value ) as $_value )
		{
			if ( !in_array( $_value, $_list ) )
			{
				$_list[] = $_value;
			}
		}

		return is_array( $original ) ? $_list : implode( ' ', $_list );
	}

	/**
	 * Removes an item from a list of things.
	 *
	 * @param string|array $original The existing class(es). If a string is passed in, a string is returned. If an array is passed in, an array is returned.
	 * @param string|array $value
	 *
	 * @return array|string
	 */
	public static function removeValue( $original, $value )
	{
		$_list = ( !is_array( $original ) ? explode( ' ', trim( $original ) ) : $original );
		$_oldList = Option::clean( $value );

		foreach ( $_list as $_index=> $_value )
		{
			if ( in_array( $_value, $_oldList ) )
			{
				unset( $_list[$_index] );
			}
		}

		return is_array( $original ) ? $_list : implode( ' ', $_list );
	}

	/**
	 * Takes a kvp traversable and converts to a ' key="value" ' string suitable for framing.
	 *
	 * @param array|object $array
	 * @param int          $trueConvert  The value to substitute for boolean true
	 * @param int          $falseConvert The value to substitute for boolean false
	 *
	 * @return string
	 */
	public static function kvpToString( $array, $trueConvert = 1, $falseConvert = 0 )
	{
		$_result = array();

		foreach ( Option::clean( $array ) as $_key => $_value )
		{
			if ( null !== $_value )
			{
				if ( false === $_value )
				{
					$_value = $falseConvert;
				}
				else if ( true === $_value )
				{
					$_value = $trueConvert;
				}
				else if ( is_array( $_value ) )
				{
					$_value = implode( ' ', $_value );
				}

				$_result[] = ( self::$_uppercaseTags ? strtoupper( $_key ) : strtolower( $_key ) ) . '="' . $_value . '"';
			}
		}

		return trim( implode( ' ', $_result ) );
	}

	/**
	 * @param int    $which
	 * @param string $what
	 *
	 * @return Markup
	 */
	public function setDelimiter( $which, $what )
	{
		Option::set( self::$_delimiters, $which, $what );

		return $this;
	}

	/**
	 * @param array $delimiters
	 *
	 * @return Markup
	 */
	public function setDelimiters( $delimiters )
	{
		self::$_delimiters = $delimiters;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getDelimiters()
	{
		return self::$_delimiters;
	}

	/**
	 * @param boolean $uppercaseTags
	 */
	public static function setUppercaseTags( $uppercaseTags )
	{
		self::$_uppercaseTags = $uppercaseTags;
	}

	/**
	 * @return boolean
	 */
	public static function getUppercaseTags()
	{
		return self::$_uppercaseTags;
	}

}
