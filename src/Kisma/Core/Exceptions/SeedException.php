<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
 *
 * Kisma(tm) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kisma(tm) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kisma(tm).  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Kisma\Core\Exceptions;

/**
 * SeedException
 * This base Kisma exception
 */
class SeedException extends \Exception
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var mixed
	 */
	protected $_context = null;

	//*************************************************************************
	//* Methods
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
