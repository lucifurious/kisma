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
class ErrorHandler extends Seed
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var int
	 */
	protected $_backtraceLines = 10;
	/**
	 * @var int
	 */
	protected $_sourceLines = 25;
	/**
	 * @var string
	 */
	protected $_viewRoute;
	/**
	 * @var string|\Exception
	 */
	protected $_error;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Event handler for a generic error
	 *
	 * @param \Kisma\Event\ErrorEvent $event
	 */
	public function onError( $event )
	{
		$_trace = $event->getTrace( false, $this->_backtraceLines );
		$_traceText = $this->_cleanTrace( $_trace, 3 );

		$this->_error = array(
			'code' => $event->getCode(),
			'type' => $event->getTypeString(),
			'message' => $event->getMessage(),
			'file' => $event->getFile(),
			'line' => $event->getLine(),
			'trace' => $_traceText,
			'traces' => $_trace,
		);

		\Kisma\K::app( 'renderer' )->render( '_error', $this->_error );
	}

	/**
	 * Handles the exception.
	 *
	 * @param \Kisma\Event\ErrorEvent $event
	 */
	public function onException( $event )
	{
		/** @var $_exception \Exception */
		$_exception = $event->getTarget();

		$this->_error = array(
			'code' => $event->getCode(),
			'type' => $event->getTypeString(),
			'message' => $event->getMessage(),
			'file' => $event->getFile(),
			'line' => $event->getLine(),
			'trace' => $_exception->getTraceAsString(),
			'traces' => $event->getTrace(),
		);

		\Kisma\K::app( 'renderer' )->render( '_exception', $this->_error );
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
	protected function _cleanTrace( array &$trace, $skipLines = null )
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
				$_traceText = $_code['function'];
			}

			$_traceText .= '()' . PHP_EOL;
		}

		return $_traceText;
	}

}
