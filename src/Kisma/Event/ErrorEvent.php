<?php
/**
 * @file
 * A generic Kisma event class
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Events
 * @package kisma.events
 * @since 1.0.0
 *
 * @ingroup events
 */

namespace Kisma\Event;

/**
 * KismaEvent
 * Wrapper for an event triggered within Kisma
 *
 * @property-read \Kisma\Components\Seed $target
 * @property-read int $code
 * @property-read string $message
 * @property-read string $file
 * @property-read int $line
 * @property-read \Exception $exception
 * @property-read array $trace
 */
class ErrorEvent extends KismaEvent
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string A PHP error
	 */
	const Error = 'error';
	/**
	 * @var string A PHP Exception
	 */
	const Exception = 'exception';

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var int
	 */
	protected $_code;
	/**
	 * @var string
	 */
	protected $_message;
	/**
	 * @var string
	 */
	protected $_file;
	/**
	 * @var int
	 */
	protected $_line;
	/**
	 * @var \Exception
	 */
	protected $_exception;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param object $target
	 * @param mixed  $code
	 * @param string $message
	 * @param string $fileName
	 * @param string $lineNumber
	 */
	public function __construct( $target, $code, $message = null, $fileName = null, $lineNumber = null )
	{
		parent::__construct( $target );

		if ( $code instanceof \Exception )
		{
			$this->_exception = $code;
			$this->_code =
				( $code instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface ?
					$code->getStatusCode() : $code->getCode() );
			$this->_message = $code->getMessage();
			$this->_file = $code->getFile();
			$this->_line = $code->getLine();
			$this->_trace = $code->getTrace();

		}
		else
		{
			$this->_exception = null;
			$this->_code = $code;
			$this->_message = $message;
			$this->_file = $fileName;
			$this->_line = $lineNumber;
		}
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * Retrieves a string representing the type of error that occurred.
	 *
	 * @return string
	 */
	public function getTypeString()
	{
		static $_types = array(
			E_ERROR => 'Error',
			E_WARNING => 'Warning',
			E_PARSE => 'Parsing Error',
			E_NOTICE => 'Notice',
			E_CORE_ERROR => 'Core Error',
			E_CORE_WARNING => 'Core Warning',
			E_COMPILE_ERROR => 'Compile Error',
			E_COMPILE_WARNING => 'Compile Warning',
			E_USER_ERROR => 'User Error',
			E_USER_WARNING => 'User Warning',
			E_USER_NOTICE => 'User Notice',
			E_STRICT => 'Runtime Notice',
			E_RECOVERABLE_ERROR => 'Recoverable Error'
		);

		return \Kisma\Utility\Option::o( $_types, $this->_code, 'Unknown Error Type (' . $this->_code . ')' );
	}

	/**
	 * @return int
	 */
	public function getCode()
	{
		return $this->_code;
	}

	/**
	 * @return \Exception
	 */
	public function getException()
	{
		return $this->_exception;
	}

	/**
	 * @return string
	 */
	public function getFile()
	{
		return $this->_file;
	}

	/**
	 * @return int
	 */
	public function getLine()
	{
		return $this->_line;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->_message;
	}

	/**
	 * @param bool $returnObject
	 * @param int  $traceLimit
	 *
	 * @return array
	 */
	public function getTrace( $returnObject = false, $traceLimit = 0 )
	{
		if ( null === $this->_exception )
		{
			return debug_backtrace( $returnObject );
		}

		return $this->_exception->getTrace();
	}
}
