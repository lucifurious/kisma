<?php
/**
 * HttpException.php
 */
namespace Kisma\Core\Exceptions;
/**
 * HttpException
 */
class HttpException extends ServiceException
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param int             $code
	 * @param string|null     $message
	 * @param \Exception|null $previous
	 * @param mixed|null      $context
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( $code, $message = null, $previous = null, $context = null )
	{
		if ( !\Kisma\Core\Enums\HttpResponse::contains( $code ) )
		{
			throw new \InvalidArgumentException( 'The code "' . $code . '" is not a valid HTTP response code.' );
		}

		if ( null === $message )
		{
			$message = \Kisma\Core\Utility\Inflector::untag( \Kisma\Core\Enums\HttpResponse::nameOf( $code ) );
		}

		parent::__construct( $message, $code, $previous, $context );
	}

}
