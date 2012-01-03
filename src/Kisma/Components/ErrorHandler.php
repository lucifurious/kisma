<?php
/**
 * @file
 * A generic error handler
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Silex
 * @package kisma.components
 * @since 1.0.0
 *
 * @ingroup silex
 */

namespace Kisma\Components;

use Kisma\Event as Event;
use Kisma\Utility as Utility;

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

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Event handler for a generic error
	 *
	 * @param \Kisma\Event\ErrorEvent $event
	 */
	public static function onError( $event )
	{
		$_trace = $event->getTrace( false, self::$_backtraceLines );
		$_traceText = self::_cleanTrace( $_trace, 3 );

		self::$_error = array(
			'code' => $event->getCode(),
			'type' => $event->getTypeString(),
			'message' => $event->getMessage(),
			'file' => $event->getFile(),
			'line' => $event->getLine(),
			'trace' => $_traceText,
			'traces' => $_trace,
		);

		\Kisma\Kisma::app()->renderError( self::$_error );
	}

	/**
	 * Handles the exception.
	 *
	 * @param \Kisma\Event\ErrorEvent $event
	 */
	public static function onException( $event )
	{
		/** @var $_exception \Exception */
		$_exception = $event->getException();

		self::$_error = array(
			'code' => $event->getCode(),
			'type' => $event->getTypeString(),
			'message' => $event->getMessage(),
			'file' => $event->getFile(),
			'line' => $event->getLine(),
			'trace' => $_exception->getTraceAsString(),
			'traces' => $event->getTrace(),
			'source' => self::_getCodeLines( $event->getFile(), $event->getLine(), self::$_sourceLines ),
		);

		$_app = \Kisma\Kisma::app();

		\Kisma\Kisma::log( self::$_error['message'] . ' (' . self::$_error['code'] . ')', \Kisma\LogLevel::Error );

		\Kisma\Kisma::app()->renderError( self::$_error );
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * Cleans up a trace array
	 *
	 * @param array $trace
	 * @param int   $skipLines
	 *
	 * @return null|string
	 */
	protected static function _cleanTrace( array &$trace, $skipLines = null )
	{
		$_traceText = null;

		//	Skip some lines
		if ( !empty( $skipLines ) && count( $trace ) > $skipLines )
		{
			$trace = array_slice( $trace, $skipLines );
		}

		foreach ( $trace as $_index => $_code )
		{
			Utility\Scalar::sins( $trace[$_index], 'file', 'Unspecified' );
			Utility\Scalar::sins( $trace[$_index], 'line', 0 );
			Utility\Scalar::sins( $trace[$_index], 'function', 'Unspecified' );

			$_traceText .= '#' . $_index . ' ' . $trace[$_index]['file'] . ' (' . $trace[$_index]['line'] . '): ';

			if ( isset( $_code['type'] ) )
			{
				$_traceText .= ( isset( $_code['class'] ) ? $_code['class'] :
					null ) . $_code['type'] . $_code['function'];
			}
			else
			{
				$_traceText .= $_code['function'];
			}

			$_traceText .= '()' . PHP_EOL;
		}

		return $_traceText;
	}

	/**
	 * @param string $fileName
	 * @param int	$line
	 * @param int	$maxLines
	 *
	 * @return string
	 */
	protected static function _getCodeLines( $fileName, $line, $maxLines )
	{
		$_lineNumber = $line - 1;

		if ( $_lineNumber < 0 || false === ( $_source = @file( $fileName ) ) )
		{
			return null;
		}

		if ( $_lineNumber >= ( $_lineCount = count( $_source ) ) )
		{
			return null;
		}

		$_result = null;

		$_halfLines = (int)( $maxLines / 2 );
		$_beginLine = $_lineNumber - $_halfLines > 0 ? $_lineNumber - $_halfLines : 0;
		$_endLine = $_lineNumber + $_halfLines < $_lineCount ? $_lineNumber + $_halfLines : $_lineCount - 1;

		for ( $_i = $_beginLine; $_i <= $_endLine; $_i++ )
		{
			$_line = trim( str_replace( array( "\r", "\t", "\n" ), '  ', $_source[$_i] ) );

			if ( !empty( $_line ) )
			{
				$_result .= $_source[$_i];
			}
		}

		return $_result;
	}
}
