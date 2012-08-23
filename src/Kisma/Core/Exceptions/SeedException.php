<?php
/**
 * SeedException.php
 * Exception awesomeness!
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/lucifurious/kisma/licensing/} for complete information.
 *
 * @copyright          Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 * @link               http://github.com/lucifurious/kisma/ Kisma(tm)
 * @license            http://github.com/lucifurious/kisma/licensing/
 * @author             Jerry Ablan <kisma@pogostick.com>
 * @filesource
 *
 * Kisma(tm) makes use of the exceptions provided by PHP's SPL.
 *
 * The SPL exceptions are:
 *
 * @exception          \BadFunctionCallException thrown if a callback refers to an undefined function or if some arguments are missing.
 * @exception          \BadMethodCallException thrown if a callback refers to an undefined method or if some arguments are missing.
 * @exception          \DomainException thrown if a value does not adhere to a defined valid data domain.
 * @exception          \InvalidArgumentException thrown if an argument does not match with the expected value.
 * @exception          \LengthException thrown if a length is invalid.
 * @exception          \LogicException represents error in the program logic.
 * @exception          \OutOfBoundsException thrown if a value is not a valid key. This represents errors that cannot be detected at compile time.
 * @exception          \OutOfRangeException thrown when an illegal index was requested. This represents errors that should be detected at compile time.
 * @exception          \OverflowException thrown when you add an element into a full container.
 * @exception          \RangeException thrown to indicate range errors during program execution. Normally this means there was an arithmetic error other than under/overflow. This is the runtime version of DomainException.
 * @exception          \RuntimeException thrown if an error which can only be found on runtime occurs.
 * @exception          \UnderflowException thrown when you try to remove an element of an empty container.
 * @exception          \UnexpectedValueException thrown if a value does not match with a set of values. Typically this happens when a function calls another function and expects the return value to be of a certain type or value not including arithmetic or buffer related errors.
 */
namespace Kisma\Core\Exceptions;
/**
 * SeedException
 * This base Kisma exception
 */
class SeedException extends \Exception
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var mixed
	 */
	protected $_context = null;

	//*************************************************************************
	//* Default/Magic Methods
	//*************************************************************************

	/**
	 * Constructs a Kisma exception.
	 *
	 * @param mixed $message
	 * @param int   $code
	 * @param mixed $previous
	 * @param mixed $context Additional information for downstream consumers
	 */
	public function __construct( $message = null, $code = null, $previous = null, $context = null )
	{
		//	If an exception is passed in, translate...
		if ( null === $code && $message instanceof \Exception )
		{
			$context = $code;

			$_exception = $message;
			$message = $_exception->getMessage();
			$code = $_exception->getCode();
			$previous = $_exception->getPrevious();
		}

		$this->_context = $context;
		parent::__construct( $message, $code, $previous );
	}

	/**
	 * Return a code/message combo when printed.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return '[' . $this->getCode() . '] ' . $this->getMessage();
	}

	/**
	 * @return mixed
	 */
	public function getContext()
	{
		return $this->_context;
	}
}
