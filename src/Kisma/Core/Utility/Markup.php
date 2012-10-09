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
		self::OpenEnd        => '<',
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

		foreach ( Option::clean( $attributes ) as $_key => $_value )
		{
			$_tags[] = ( self::$_uppercaseTags ? strtoupper( $_key ) : strtolower( $_key ) ) . '="' . $_value . '"';
		}

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
