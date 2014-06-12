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

/**
 * FilterInput
 * Helpers for working with filter_input and filter_var
 */
class FilterInput
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
	 * @return mixed
	 */
	public function __invoke()
	{
		return call_user_func_array( array( 'FilterInput::get' ), func_get_args() );
	}

	/**
	 * Filter chooser based on number or string. Not very smart really.
	 *
	 * @param mixed $value
	 * @param bool  $emptyValueIsNull If true, empty() values will be returned as NULL
	 *
	 * @return mixed
	 */
	public static function smart( $value, $emptyValueIsNull = false )
	{
		if ( is_array( $value ) )
		{
			filter_var_array( $value, FILTER_SANITIZE_STRING );
		}

		switch ( getType( $value ) )
		{
			case 'double':
			case 'float':
				$_filter = FILTER_SANITIZE_NUMBER_FLOAT;
				break;

			case 'integer':
				$_filter = FILTER_SANITIZE_NUMBER_INT;
				break;

			case 'string':
				$_filter = FILTER_SANITIZE_STRING;
				break;

			default:
				$_filter = FILTER_DEFAULT;
				break;
		}

		$_result = filter_var( $value, $_filter );

		return $emptyValueIsNull && empty( $_result ) ? null : $_result;
	}

	/**
	 * The master function, performs all filters and gets. Gets around lack of INPUT_SESSION and INPUT_REQUEST
	 * support.
	 *
	 * @param int|array      $type                         One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, INPUT_ENV,
	 *                                                     INPUT_SESSION and INPUT_REQUEST. You may also pass in an array and use this
	 *                                                     method to call filter_var with the value found in the array
	 * @param string         $key                          The name of a variable to get.
	 * @param mixed          $defaultValue                 The default value if the key is not found
	 * @param int            $filter                       The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param int|array|null $filterOptions                Associative array of options or bitwise disjunction of flags. If
	 *                                                     filter accepts options,
	 *                                                     flags can be provided in "flags" field of array. For the "callback" filter,
	 *                                                     callback type should be passed. The callback must accept one argument,
	 *                                                     the value to be filtered, and return the value after filtering/sanitizing it.
	 * @param bool           $emptyValueIsNull             If true, empty() values will be returned as NULL
	 *
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	public static function get( $type, $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null, $emptyValueIsNull = false )
	{
		//	Allow usage as filter_var()
		if ( is_array( $type ) )
		{
			$_result = filter_var( Option::get( $type, $key, $defaultValue ), $filter, $filterOptions );

			return $emptyValueIsNull && empty( $_result ) ? null : $_result;
		}

		$_haystack = null;

		//	Based on the type, pull the right value
		switch ( $type )
		{
			case INPUT_REQUEST:
				$_haystack = isset( $_REQUEST ) ? $_REQUEST : array();
				break;
			case INPUT_SESSION:
				$_haystack = isset( $_SESSION ) ? $_SESSION : array();
				break;
			case INPUT_GET:
				$_haystack = isset( $_GET ) ? $_GET : array();
				break;
			case INPUT_POST:
				$_haystack = isset( $_POST ) ? $_POST : array();
				break;
			case INPUT_COOKIE:
				$_haystack = isset( $_COOKIE ) ? $_COOKIE : array();
				break;
			case INPUT_SERVER:
				$_haystack = isset( $_SERVER ) ? $_SERVER : array();
				break;
			case INPUT_ENV:
				$_haystack = isset( $_ENV ) ? $_ENV : array();
				break;
			default:
				//	No clue what you want man...
				throw new \InvalidArgumentException( 'The filter type of "' . $type . '" is unknown or not supported.' );
		}

		$_value = empty( $_haystack ) ? $defaultValue : filter_var( Option::get( $_haystack, $key, $defaultValue ), $filter, $filterOptions );

		return $emptyValueIsNull && empty( $_value ) ? null : $_value;
	}

	/**
	 * @param string         $key           The name of a variable to get.
	 * @param int            $filter        The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue  The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 *                                      filter accepts options,
	 *                                      flags can be provided in "flags" field of array. For the "callback" filter,
	 *                                      callback type should be passed. The callback must accept one argument,
	 *                                      the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function post( $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		return static::get( INPUT_POST, $key, $defaultValue, $filter, $filterOptions );
	}

	/**
	 * @param string         $key           The name of a variable to get.
	 * @param int            $filter        The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue  The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 *                                      filter accepts options,
	 *                                      flags can be provided in "flags" field of array. For the "callback" filter,
	 *                                      callback type should be passed. The callback must accept one argument,
	 *                                      the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function cookie( $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		return static::get( INPUT_COOKIE, $key, $defaultValue, $filter, $filterOptions );
	}

	/**
	 * @param string         $key           The name of a variable to get.
	 * @param int            $filter        The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue  The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 *                                      filter accepts options,
	 *                                      flags can be provided in "flags" field of array. For the "callback" filter,
	 *                                      callback type should be passed. The callback must accept one argument,
	 *                                      the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function server( $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		return static::get( INPUT_SERVER, $key, $defaultValue, $filter, $filterOptions );
	}

	/**
	 * @param string         $key           The name of a variable to get.
	 * @param int            $filter        The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue  The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 *                                      filter accepts options,
	 *                                      flags can be provided in "flags" field of array. For the "callback" filter,
	 *                                      callback type should be passed. The callback must accept one argument,
	 *                                      the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function env( $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		return static::get( INPUT_ENV, $key, $defaultValue, $filter, $filterOptions );
	}

	/**
	 * @param string         $key           The name of a variable to get.
	 * @param int            $filter        The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue  The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 *                                      filter accepts options,
	 *                                      flags can be provided in "flags" field of array. For the "callback" filter,
	 *                                      callback type should be passed. The callback must accept one argument,
	 *                                      the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function session( $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		return static::get( INPUT_SESSION, $key, $defaultValue, $filter, $filterOptions );
	}

	/**
	 * @param string         $key           The name of a variable to get.
	 * @param int            $filter        The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param mixed          $defaultValue  The default value if the key is not found
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 *                                      filter accepts options,
	 *                                      flags can be provided in "flags" field of array. For the "callback" filter,
	 *                                      callback type should be passed. The callback must accept one argument,
	 *                                      the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @return mixed
	 */
	public static function request( $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		return static::get( INPUT_REQUEST, $key, $defaultValue, $filter, $filterOptions );
	}
}