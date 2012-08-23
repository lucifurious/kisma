<?php
/**
 * ProcessLockException.php
 */
namespace Kisma\Core\Exceptions;
/**
 * Base class for lock file exceptions
 */
class ProcessLockException extends SeedException
{
}

/**
 *
 */
class ProcessLockExistsException extends SeedException
{
}

/**
 *
 */
class ProcessLockAgeException extends SeedException
{
}

/**
 *
 */
class ProcessLockFileException extends SeedException
{
}

