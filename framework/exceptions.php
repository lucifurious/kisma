<?php
//! Exceptions used by Kisma(tm)
/*!
Kisma(tm) makes use of the exceptions provided by SPL.
In addition, some additional information exceptions are defined.

The SPL exceptions are:

 @exception BadFunctionCallException thrown if a callback refers to an undefined function or if some arguments are missing.
 @exception BadMethodCallException thrown if a callback refers to an undefined method or if some arguments are missing.
 @exception DomainException thrown if a value does not adhere to a defined valid data domain.
 @exception InvalidArgumentException thrown if an argument does not match with the expected value.
 @exception LengthException thrown if a length is invalid.
 @exception LogicException represents error in the program logic.
 @exception OutOfBoundsException thrown if a value is not a valid key. This represents errors that cannot be detected at compile time.
 @exception OutOfRangeException thrown when an illegal index was requested. This represents errors that should be detected at compile time.
 @exception OverflowException thrown when you add an element into a full container.
 @exception RangeException thrown to indicate range errors during program execution. Normally this means there was an arithmetic error other than under/overflow. This is the runtime version of DomainException.
 @exception RuntimeException thrown if an error which can only be found on runtime occurs.
 @exception UnderflowException thrown when you try to remove an element of an empty container.
 @exception UnexpectedValueException thrown if a value does not match with a set of values. Typically this happens when a function calls another function and expects the return value to be of a certain type or value not including arithmetic or buffer related errors.

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
}

//*************************************************************************
//* Property 
//*************************************************************************

/**
 * A base exception for property errors
 */
class PropertyException extends KismaException
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
//* Database 
//*************************************************************************

/**
 *
 */
class DatabaseException extends KismaException
{
}


class ComponentException extends KismaException
{
	
}