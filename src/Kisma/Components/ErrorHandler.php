<?php
/**
 * @file
 * A generic error handler
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Silex
 * @package kisma.components
 * @since 1.0.0
 *
 * @ingroup silex
 */

namespace Kisma\Components;

use \Kisma\K;
use \Kisma\AppConfig;
use \Kisma\Event as Event;
use \Kisma\Utility as Utility;

/**
 * ErrorHandler
 * Generic error handler
 */
class ErrorHandler extends Seed implements \Kisma\IReactor
{
	//*************************************************************************
	//* Private Members
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

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Event handler for a generic error
	 *
	 * @param \Kisma\Event\ErrorEvent $event
	 *
	 * @return bool
	 */
	public static function onError( $event )
	{
		$_trace = $event->getTrace( false, self::$_backtraceLines );
		$_traceText = self::_cleanTrace( $_trace, 3 );

		if ( E_NOTICE == $event->getCode() )
		{
			return false;
		}

		self::$_error = array(
			'code' => $event->getCode(),
			'type' => $event->getTypeString(),
			'message' => $event->getMessage(),
			'file' => $event->getFile(),
			'line' => $event->getLine(),
			'trace' => $_traceText,
			'traces' => $_trace,
			'source' => self::_getCodeLines( $event->getFile(), $event->getLine(), self::$_sourceLines ),
			'start_line' => self::$_startLine,
		);

		return self::renderError();
	}

	/**
	 * Handles the exception.
	 *
	 * @param \Kisma\Event\ErrorEvent $event
	 *
	 * @return bool
	 */
	public static function onException( $event )
	{
		$_exception = $event->getException();
		$_trace = $_exception->getTrace();
		$_traceText = self::_cleanTrace( $_trace, 3 );

		if ( E_NOTICE == $event->getCode() )
		{
			return false;
		}

		self::$_error = array(
			'code' => $_exception->getCode(),
			'type' => $event->getTypeString(),
			'message' => $_exception->getMessage(),
			'file' => $_exception->getFile(),
			'line' => $_exception->getLine(),
			'trace' => $_traceText,
			'traces' => $_exception->getTrace(),
			'source' => self::_getCodeLines( $event->getFile(), $event->getLine(), self::$_sourceLines ),
			'start_line' => self::$_startLine,
		);

		K::log( self::$_error['message'] . ' (' . self::$_error['code'] . ')', \Kisma\LogLevel::Error );

		return self::renderError();
	}

	/**
	 * Renders an error
	 *
	 * @return bool
	 */
	public static function renderError()
	{
		$_app = K::app();

		//	Don't render errors if error handling or Twig are disabled, or if our app is naked Silex.
		if ( !( $_app instanceof \Kisma\Kisma ) || !$_app->serviceEnabled( 'error_handler' ) || !$_app->serviceEnabled( 'twig' ) )
		{
			return false;
		}

		try
		{
			$_errorTemplate = K::app( 'error_template', '_error.twig' );

			$_app->render(
				$_errorTemplate,
				array(
					'base_path' => \Kisma\Kisma::app( AppConfig::BasePath ),
					'app_root' => \Kisma\Kisma::app( 'app.config.app_root' ),
					'page_title' => 'Error',
					'error' => self::$_error,
					'page_header' => 'Something has gone awry...',
					'page_header_small' => 'Not cool. :(',
					'topbar' => array(
						'brand' => 'Kisma v' . \Kisma\Kisma::Version,
						'items' => array(
							array(
								'title' => 'Kisma on GitHub!',
								'href' => 'http://github.com/lucifurious/kisma/',
								'target' => '_blank',
								'active' => 'active',
							),
						),
					),
				)
			);

			return true;
		}
		catch ( Exception $_ex )
		{
			if ( isset( $_app['logger'] ) )
			{
				$_app['logger']->addError( 'Exception during rendering of error: ' . var_export( self::$_error,
					true ) );
			}
		}

		return false;
	}

//*************************************************************************
//* Private Methods
//*************************************************************************

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
		$_basePath = $basePath ? : \Kisma\Kisma::app( AppConfig::BasePath );

		//	Skip some lines
		if ( !empty( $skipLines ) && count( $trace ) > $skipLines )
		{
			$trace = array_slice( $trace, $skipLines );
		}

		foreach ( $trace as $_index => $_code )
		{
			$_traceItem = array();

			Utility\Scalar::sins( $trace[$_index], 'file', 'Unspecified' );
			Utility\Scalar::sins( $trace[$_index], 'line', 0 );
			Utility\Scalar::sins( $trace[$_index], 'function', 'Unspecified' );

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
				$_traceItem['function'] = ( isset( $_code['class'] ) ? $_code['class'] :
					null ) . $_code['type'] . $_code['function'];
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
	 * @param int	$line
	 * @param int	$maxLines
	 * @param bool   $html If true, return each line terminated with <BR/> instead of PHP_EOL
	 *
	 * @return string
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
		self::$_startLine = $_start + ( $maxLines % 2 );

		while ( $_start <= ( $line + $_window ) )
		{
			if ( !isset( $_source[$_start] ) )
			{
				break;
			}

			$_line =
				str_replace( array( "\r", "\n", "\t", PHP_EOL ), array( null, null, '    ', null ),
					$_source[$_start++] );

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

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param int $backtraceLines
	 */
	public static function setBacktraceLines( $backtraceLines )
	{
		self::$_backtraceLines = $backtraceLines;
	}

	/**
	 * @return int
	 */
	public static function getBacktraceLines()
	{
		return self::$_backtraceLines;
	}

	/**
	 * @return \Exception|string
	 */
	public static function getError()
	{
		return self::$_error;
	}

	/**
	 * @param int $sourceLines
	 */
	public static function setSourceLines( $sourceLines )
	{
		self::$_sourceLines = $sourceLines;
	}

	/**
	 * @return int
	 */
	public static function getSourceLines()
	{
		return self::$_sourceLines;
	}

	/**
	 * @param int $startLine
	 */
	public static function setStartLine( $startLine )
	{
		self::$_startLine = $startLine;
	}

	/**
	 * @return int
	 */
	public static function getStartLine()
	{
		return self::$_startLine;
	}

	/**
	 * @param string $viewRoute
	 */
	public static function setViewRoute( $viewRoute )
	{
		self::$_viewRoute = $viewRoute;
	}

	/**
	 * @return string
	 */
	public static function getViewRoute()
	{
		return self::$_viewRoute;
	}
}
