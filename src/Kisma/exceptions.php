<?php
/**
 * exceptions.php
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
namespace Kisma;

//*************************************************************************
//* Core
//*************************************************************************

/**
 * This base Kisma exception
 */
class KismaException extends \Exception
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var mixed
	 */
	protected $_info = null;

	//*************************************************************************
	//* Default/Magic Methods
	//*************************************************************************

	/**
	 * Constructs an exception.
	 *
	 * @param mixed      $message
	 * @param int|null   $code
	 * @param mixed      $previous
	 * @param mixed|null $info Additional information
	 */
	public function __construct( $message = null, $code = null, $previous = null, $info = null )
	{
		//	If an exception is passed in, translate...
		if ( null === $code && $message instanceof \Exception )
		{
			/** @var $_exception \Exception */
			$_exception = $message;
			$message = $_exception->getMessage();
			$code = $_exception->getCode();
			$previous = $_exception->getPrevious();
		}

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
	public function getInfo()
	{
		return $this->_info;
	}
}

/**
 *
 */
class ObjectException extends KismaException
{
}

/**
 *
 */
class EventException extends ObjectException
{
}

/**
 *
 */
class EventHandlerException extends EventException
{
}

/**
 *
 */
class InvalidEventHandlerException extends EventHandlerException
{
}

/**************************************************************************
 ** Process Lock
 **************************************************************************/

/**
 * Base class for lock file exceptions
 */
class ProcessLockException extends KismaException
{
}

/**
 *
 */
class ProcessLockExistsException extends KismaException
{
}

/**
 *
 */
class ProcessLockAgeException extends KismaException
{
}

/**
 *
 */
class ProcessLockFileException extends KismaException
{
}

//*************************************************************************
//* Property
//*************************************************************************

/**  */
class SettingException extends KismaException
{
}

/**  */
class InvalidSettingValueException extends SettingException
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param mixed  $previous
	 * @param mixed  $info
	 */
	public function __construct( $key = null, $value = null, $previous = null, $info = null )
	{
		$_message =
			'An invalid value was provided for "' . $key . '": ' .
				(
				is_scalar( $value ) ? $value :
					(
					null === $value ? 'NULL' : print_r( $value, true )
					)
				);

		parent::__construct( $_message, null, $previous, $info );
	}
}

/**  */
class InvalidSettingKeyException extends SettingException
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param string $key      The rejected key
	 * @param mixed  $provider Class name of the storage provider
	 * @param mixed  $previous
	 * @param mixed  $info
	 */
	public function __construct( $key = null, $provider = null, $previous = null, $info = null )
	{
		$_message = 'The key "' . $key . '" is not valid for provider: ' . $provider;
		parent::__construct( $_message, null, $previous, $info );
	}
}

/**
 * A base exception for property errors
 */
class PropertyException extends KismaException
{
}

/**
 * A base exception for property errors
 */
class BogusPropertyException extends PropertyException
{
}

/**
 */
class UndefinedPropertyException extends PropertyException
{
}

/**
 */
class ReadOnlyPropertyException extends PropertyException
{
}

/**
 */
class WriteOnlyPropertyException extends PropertyException
{
}

/**
 * A base exception for AutoProperty errors
 */
class AutoPropertyException extends PropertyException
{
}

//*************************************************************************
//* Storage
//*************************************************************************

class StorageException extends KismaException
{
}

class DatabaseException extends StorageException
{
}

class ObjectStorageException extends StorageException
{
}

class CouchDbException extends StorageException
{
}

/**************************************************************************
 ** Events
 **************************************************************************/

//*************************************************************************
//* Services
//*************************************************************************

class ServiceException extends ObjectException
{
}

//*************************************************************************
//* Transformers
//*************************************************************************

class UtilityException extends ObjectException
{
}

class TransformerException extends UtilityException
{
}

class InvalidTransformerInputException extends TransformerException
{
}


