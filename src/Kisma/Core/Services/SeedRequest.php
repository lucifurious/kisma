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
namespace Kisma\Core\Services;

use Kisma\Core\Interfaces\RequestLike;
use Kisma\Core\Interfaces\RequestSource;
use Kisma\Core\SeedBag;

/**
 * SeedRequest
 * A basic service request
 */
abstract class SeedRequest extends SeedBag implements RequestLike, RequestSource
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var int
	 */
	protected $_source = self::Http;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param array         $contents
	 * @param RequestSource $source
	 */
	public function __construct( $contents = array(), $source = null )
	{
		//	Set the request type properly...
		if ( null === $source && PHP_SAPI == 'cli' )
		{
			$this->_source = static::Cli;
		}

		parent::__construct( $contents );
	}

	/**
	 * @return int
	 */
	public function getSource()
	{
		return $this->_source;
	}

	/**
	 * @param int $source
	 *
	 * @return SeedRequest
	 */
	public function setSource( $source )
	{
		$this->_source = $source;

		return $this;
	}
}
