<?php
/**
 * FilterInput.php
 */
namespace Kisma\Core\Utility;
/**
 * FilterInput
 * Helpers for working with filter_input and filter_var
 */
class FilterInput implements \Kisma\Core\Interfaces\SeedUtility
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
	public static function __invoke()
	{
		return call_user_func_array( array( 'FilterInput::get' ), func_get_args() );
	}

	/**
	 * Filter chooser based on number or string. Not very smart really.
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public static function smart( $value )
	{
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

		return filter_var( $value, $_filter );
	}

	/**
	 * The master function, performs all filters and gets. Gets around lack of INPUT_SESSION and INPUT_REQUEST
	 * support.
	 *
	 * @param int|array      $type          One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, INPUT_ENV,
	 *                                      INPUT_SESSION and INPUT_REQUEST. You may also pass in an array and use this
	 *                                      method to call filter_var with the value found in the array
	 * @param string         $key           The name of a variable to get.
	 * @param mixed          $defaultValue  The default value if the key is not found
	 * @param int            $filter        The filter to use (see the manual page). Defaults to FILTER_DEFAULT.
	 * @param int|array|null $filterOptions Associative array of options or bitwise disjunction of flags. If
	 *                                      filter accepts options,
	 *                                      flags can be provided in "flags" field of array. For the "callback" filter,
	 *                                      callback type should be passed. The callback must accept one argument,
	 *                                      the value to be filtered, and return the value after filtering/sanitizing it.
	 *
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	public static function get( $type, $key, $defaultValue = null, $filter = FILTER_DEFAULT, $filterOptions = null )
	{
		//	Allow usage as filter_var()
		if ( is_array( $type ) )
		{
			return trim( filter_var( Option::get( $type, $key, $defaultValue ), $filter, $filterOptions ) );
		}

		//	Based on the type, pull the right value
		switch ( $type )
		{
			case INPUT_REQUEST:
				return trim( filter_var( Option::get( $_REQUEST, $key, $defaultValue ), $filter, $filterOptions ) );

			case INPUT_SESSION:
				return trim( filter_var( Option::get( $_SESSION, $key, $defaultValue ), $filter, $filterOptions ) );

			case INPUT_GET:
			case INPUT_POST:
			case INPUT_COOKIE:
			case INPUT_SERVER:
			case INPUT_ENV:
				return trim( filter_input( $type, $key, $filter, $filterOptions ) );
		}

		//	No clue what you want man...
		throw new \InvalidArgumentException( 'The filter type of "' . $type . '" is unknown or not supported.' );
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
		return self::get( INPUT_POST, $key, $defaultValue, $filter, $filterOptions );
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
		return self::get( INPUT_COOKIE, $key, $defaultValue, $filter, $filterOptions );
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
		return self::get( INPUT_SERVER, $key, $defaultValue, $filter, $filterOptions );
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
		return self::get( INPUT_ENV, $key, $defaultValue, $filter, $filterOptions );
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
		return self::get( INPUT_SESSION, $key, $defaultValue, $filter, $filterOptions );
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
		return self::get( INPUT_REQUEST, $key, $defaultValue, $filter, $filterOptions );
	}
}