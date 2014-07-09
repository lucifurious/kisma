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
 * ErrorHandler
 * A dead-simple error handler class
 */
class ErrorHandler
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var int
	 */
	protected static $_backtraceLines = 10;
	/**
	 * @var int
	 */
	protected static $_sourceLines = 25;
	/**
	 * @var string
	 */
	protected static $_viewRoute;
	/**
	 * @var string|\Exception
	 */
	protected static $_error;
	/**
	 * @var int
	 */
	protected static $_startLine;
	/**
	 * @var bool Logs notices instead of generated error
	 */
	protected static $_ignoreNotices = true;
	/**
	 * @var callable
	 */
	protected static $_priorHandler = null;
	/**
	 * @var callable
	 */
	protected static $_priorExceptionHandler = null;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 *
	 */
	public static function register()
	{
		static::$_priorHandler = \set_error_handler( array( __CLASS__, 'onError' ) );
		static::$_priorExceptionHandler = \set_exception_handler( array( __CLASS__, 'onException' ) );
	}

	/**
	 *
	 */
	public static function unregister()
	{
		if ( !empty( static::$_priorHandler ) )
		{
			\set_error_handler( static::$_priorHandler );
		}

		if ( !empty( static::$_priorExceptionHandler ) )
		{
			\set_exception_handler( static::$_priorExceptionHandler );
		}
	}

	/**
	 * @param int    $code
	 * @param string $message
	 * @param string $file
	 * @param int    $line
	 * @param array  $context
	 *
	 * @return bool
	 */
	public static function onError( $code, $message, $file = null, $line = null, $context = null )
	{
		$_trace = \debug_backtrace();
		$_traceText = static::_cleanTrace( $_trace, 3 );

		if ( E_NOTICE == $code )
		{
			//return false;
		}

		static::$_error = array(
			'code'       => $code,
			'type'       => null,
			'message'    => $message,
			'file'       => $file,
			'line'       => $line,
			'trace'      => $_traceText,
			'traces'     => $_trace,
			'source'     => static::_getCodeLines( $file, $line, static::$_sourceLines ),
			'start_line' => static::$_startLine,
		);

		Log::error( static::$_error['message'] . ' (' . static::$_error['code'] . ')' );

		return static::renderError();
	}

	/**
	 * @param \Exception $exception
	 *
	 * @return bool
	 */
	public static function onException( $exception )
	{
		$_trace = $exception->getTrace();
		$_traceText = static::_cleanTrace( $_trace, 3 );

		if ( E_NOTICE == $exception->getCode() )
		{
			return false;
		}

		static::$_error = array(
			'code'       => $exception->getCode(),
			'type'       => '',
			'message'    => $exception->getMessage(),
			'file'       => $exception->getFile(),
			'line'       => $exception->getLine(),
			'trace'      => $_traceText,
			'traces'     => $exception->getTrace(),
			'source'     => static::_getCodeLines( $exception->getFile(), $exception->getLine(), static::$_sourceLines ),
			'start_line' => static::$_startLine,
		);

		Log::error( static::$_error['message'] . ' (' . static::$_error['code'] . ')' );

		return static::renderError();
	}

	/**
	 * Renders an error
	 *
	 * @return bool
	 */
	public static function renderError()
	{
		try
		{
			$_errorTemplate = \Kisma::get( 'app.error_template', '_error.twig' );

			Render::twigView(
				$_errorTemplate,
				array(
					'base_path'         => \Kisma::get( 'app.base_path' ),
					'app_root'          => \Kisma::get( 'app.root' ),
					'page_title'        => 'Error',
					'error'             => static::$_error,
					'page_header'       => 'Something has gone awry...',
					'page_header_small' => 'Not cool. :(',
					'navbar'            => array(
						'brand' => 'Kisma v' . \Kisma::KismaVersion,
						'items' => array(
							array(
								'title'  => 'Kisma on GitHub!',
								'href'   => 'http://github.com/kisma/kisma/',
								'target' => '_blank',
								'active' => 'active',
							),
						),
					),
				)
			);

			return true;
		}
		catch ( \Exception $_ex )
		{
			Log::error( 'Exception during rendering of error: ' . print_r( static::$_error, true ) );
		}

		return false;
	}

	/**
	 * Cleans up a trace array
	 *
	 * @param array $trace
	 * @param int   $skipLines
	 * @param null  $basePath
	 *
	 * @return null|string
	 */
	protected static function _cleanTrace( array &$trace, $skipLines = null, $basePath = null )
	{
		$_trace = array();
		$_basePath = $basePath ? : \Kisma::get( 'app.base_path' );

		//	Skip some lines
		if ( !empty( $skipLines ) && count( $trace ) > $skipLines )
		{
			$trace = array_slice( $trace, $skipLines );
		}

		foreach ( $trace as $_index => $_code )
		{
			$_traceItem = array();

			Scalar::sins( $trace[$_index], 'file', 'Unspecified' );
			Scalar::sins( $trace[$_index], 'line', 0 );
			Scalar::sins( $trace[$_index], 'function', 'Unspecified' );

			$_traceItem['file_name'] = trim(
				str_replace(
					array(
						$_basePath,
						"\t",
						"\r",
						"\n",
						PHP_EOL,
						'phar://',
					),
					array(
						null,
						'    ',
						null,
						null,
						null,
						null,
					),
					$trace[$_index]['file']
				)
			);

			$_args = null;

			if ( isset( $_code['args'] ) && !empty( $_code['args'] ) )
			{
				foreach ( $_code['args'] as $_arg )
				{
					if ( is_object( $_arg ) )
					{
						$_args .= get_class( $_arg ) . ', ';
					}
					else if ( is_array( $_arg ) )
					{
						$_args .= '[array], ';
					}
					else if ( is_bool( $_arg ) )
					{
						if ( $_arg )
						{
							$_args .= 'true, ';
						}
						else
						{
							$_args .= 'false, ';
						}
					}
					else if ( is_numeric( $_arg ) )
					{
						$_args .= $_arg . ', ';
					}
					else if ( is_scalar( $_arg ) )
					{
						$_args .= '"' . $_arg . '", ';
					}
					else
					{
						$_args .= '"' . gettype( $_arg ) . '", ';
					}
				}
			}

			$_traceItem['line'] = $trace[$_index]['line'];

			if ( isset( $_code['type'] ) )
			{
				$_traceItem['function'] = ( isset( $_code['class'] ) ? $_code['class'] : null ) . $_code['type'] . $_code['function'];
			}
			else
			{
				$_traceItem['function'] = $_code['function'];
			}

			$_traceItem['function'] .= '(' . ( $_args ? ' ' . trim( $_args, ', ' ) . ' ' : null ) . ')';
			$_traceItem['index'] = $_index;
			$_trace[] = $_traceItem;
		}

		return $_trace;
	}

	/**
	 * @param string $fileName
	 * @param int    $line
	 * @param int    $maxLines
	 * @param bool   $html If true, return each line terminated with <BR/> instead of PHP_EOL
	 *
	 * @return string
	 * @return null|string
	 */
	protected static function _getCodeLines( $fileName, $line, $maxLines, $html = false )
	{
		$line--;

		if ( $line <= 0 || false === ( $_source = @file( $fileName ) ) )
		{
			return null;
		}

		$_result = null;
		$_window = intval( $maxLines / 2 );

		$_start = ( 0 > ( $line - $_window ) ? 0 : ( $line - $_window ) );

		/**
		 * Set starting line to pass to syntax highlighter. If $maxLines is odd, we lose a half a line. The modulo corrects for that.
		 */
		static::$_startLine = $_start + ( $maxLines % 2 );

		while ( $_start <= ( $line + $_window ) )
		{
			if ( !isset( $_source[$_start] ) )
			{
				break;
			}

			$_line = str_replace(
				array( "\r", "\n", "\t", PHP_EOL ),
				array( null, null, '    ', null ),
				$_source[$_start++]
			);

			if ( false === $html )
			{
				$_result .= $_line . PHP_EOL;
			}
			else
			{
				$_result .= $_line . '<br />';
			}
		}

		return $_result;
	}
}
