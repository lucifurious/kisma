<?php
/**
 * FilterInput.php
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link		  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license	   http://github.com/Pogostick/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @category	  Kisma_Utility
 * @package	   kisma.utility
 * @namespace	 \Kisma\Utility
 * @since		 v1.0.0
 * @filesource
 */
namespace Kisma\Utility;

/**
 * FilterInput
 * Helpers for working with filter_input and filter_var
 */
class FilterInput implements \Kisma\IUtility
{
	//*************************************************************************
	//* Filter Getters
	//*
	//* These methods are useful to pull a value from one of the PHP
	//* super globals (i.e. $_GET, $_POST, $_ENV, etc.).
	//*************************************************************************

	/**
	 * The default method for this object. Calls FilterInput::get()
	 *
	 * @static
	 * @return mixed
	 */
	public static function __invoke()
	{
		return call_user_func_array( array( 'FilterInput::get' ), func_get_args() );
	}

	/**
	 * The master function, performs all filters and gets. Gets around lack of INPUT_SESSION and INPUT_REQUEST
	 * support.
	 *
	 * @static
	 *
	 * @param int            $type One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, INPUT_ENV,
	 * INPUT_SESSION and INPUT_REQUEST. You may also pass in an array and use this
	 * method to call filter_var with the value found in the array
	 * @param string         $key The name of a variable to get.
	 * @param int            $filter The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 * filter accepts options,
	 * flags can be provided in "flags" field of array. For the "callback" filter,
	 * callback type should be passed. The callback must accept one argument,
	 * the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function get( $type, $key, $defaultValue = null, $filter = FILTER_DEFAULT,
								$filterOptions = null )
	{
		//	Allow usage as filter_var()
		if ( is_array( $type ) )
		{
			return filter_var( Option::o( $type, $key, $defaultValue ), $filter, $filterOptions );
		}

		//	Based on the type, pull the right value
		switch ( $type )
		{
			case INPUT_REQUEST:
				return filter_var( Option::o( $_REQUEST, $key, $defaultValue ), $filter, $filterOptions );
				break;

			case INPUT_SESSION:
				return filter_var( Option::o( $_SESSION, $key, $defaultValue ), $filter, $filterOptions );

			case INPUT_GET:
			case INPUT_POST:
			case INPUT_COOKIE:
			case INPUT_SERVER:
			case INPUT_ENV:
				return filter_input( $type, $key, $filter, $filterOptions );
		}

		//	No clue what you want man...
		throw new \InvalidArgumentException( 'The type of "' . $type . '" is unknown or not supported.' );
	}

	/**
	 * @static
	 *
	 * @param string         $key The name of a variable to get.
	 * @param int            $filter The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 * filter accepts options,
	 * flags can be provided in "flags" field of array. For the "callback" filter,
	 * callback type should be passed. The callback must accept one argument,
	 * the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function post( $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		return self::get( INPUT_POST, $key, $defaultValue, $filter, $filterOptions );
	}

	/**
	 * @static
	 *
	 * @param string         $key The name of a variable to get.
	 * @param int            $filter The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 * filter accepts options,
	 * flags can be provided in "flags" field of array. For the "callback" filter,
	 * callback type should be passed. The callback must accept one argument,
	 * the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function cookie( $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		return self::get( INPUT_COOKIE, $key, $defaultValue, $filter, $filterOptions );
	}

	/**
	 * @static
	 *
	 * @param string         $key The name of a variable to get.
	 * @param int            $filter The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 * filter accepts options,
	 * flags can be provided in "flags" field of array. For the "callback" filter,
	 * callback type should be passed. The callback must accept one argument,
	 * the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function server( $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		return self::get( INPUT_SERVER, $key, $defaultValue, $filter, $filterOptions );
	}

	/**
	 * @static
	 *
	 * @param string         $key The name of a variable to get.
	 * @param int            $filter The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 * filter accepts options,
	 * flags can be provided in "flags" field of array. For the "callback" filter,
	 * callback type should be passed. The callback must accept one argument,
	 * the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function env( $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		return self::get( INPUT_ENV, $key, $defaultValue, $filter, $filterOptions );
	}

	/**
	 * @static
	 *
	 * @param string         $key The name of a variable to get.
	 * @param int            $filter The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 * filter accepts options,
	 * flags can be provided in "flags" field of array. For the "callback" filter,
	 * callback type should be passed. The callback must accept one argument,
	 * the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function session( $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		return self::get( INPUT_SESSION, $key, $defaultValue, $filter, $filterOptions );
	}

	/**
	 * @static
	 *
	 * @param string         $key The name of a variable to get.
	 * @param int            $filter The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 * filter accepts options,
	 * flags can be provided in "flags" field of array. For the "callback" filter,
	 * callback type should be passed. The callback must accept one argument,
	 * the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function request( $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		return self::get( INPUT_REQUEST, $key, $defaultValue, $filter, $filterOptions );
	}
}

