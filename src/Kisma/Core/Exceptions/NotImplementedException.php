<?php
/**
 * NotImplementedException.php
 */
namespace Kisma\Core\Exceptions;
/**
 * NotImplementedException
 * Used when things aren't implemented
 */
class NotImplementedException extends SeedException
{
	/**
	 * @param string|null $message
	 * @param int|null    $code
	 * @param mixed|null  $previous
	 * @param mixed|null  $context
	 */
	public function __construct( $message = null, $code = null, $previous = null, $context = null )
	{
		if ( null === $message )
		{
			$message = 'This feature has not been implemented.';
		}

		parent::__construct( $message, $code, $previous, $context );
	}

}
