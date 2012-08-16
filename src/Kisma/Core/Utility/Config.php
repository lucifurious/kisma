<?php
/**
 * @file
 * Provides methods to get settings out of the app configs
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2012 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Utility
 * @package kisma.utility
 * @since 1.0.0
 *
 * @ingroup utility
 */

namespace Kisma\Core\Utility;

//*************************************************************************
//* Requirements
//*************************************************************************

/**
 * Config
 * Provides methods to get settings out of the app configs
 */
class Config extends Option
{
	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * @var string
	 */
	static $_prefix = 'app.config.';

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Gets a config value
	 *
	 * @param string	 $key
	 * @param mixed|null $defaultValue
	 *
	 * @return mixed
	 */
	public static function get( $key, $defaultValue = null )
	{
		return \Kisma\Kisma::app( self::$_prefix . $key, $defaultValue );
	}

	/**
	 * Alias for {@link \Kisma\Kisma::so}
	 *
	 * @param array  $options
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	public static function set( &$options = array(), $key, $value = null )
	{
		$_originalValue = \Kisma\Kisma::app(self::$_prefix . $key);
		$_app[self::$_prefix . $key] = $value;
		return $_originalValue;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param string $prefix
	 */
	public static function setPrefix( $prefix )
	{
		self::$_prefix = $prefix;
	}

	/**
	 * @return string
	 */
	public static function getPrefix()
	{
		return self::$_prefix;
	}

}