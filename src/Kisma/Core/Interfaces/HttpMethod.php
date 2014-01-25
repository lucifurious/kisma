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
namespace Kisma\Core\Interfaces;

/**
 * HttpMethod
 * Defines the available Http methods for CURL
 */
interface HttpMethod
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const Get = 'GET';
	/**
	 * @var string
	 */
	const Put = 'PUT';
	/**
	 * @var string
	 */
	const Head = 'HEAD';
	/**
	 * @var string
	 */
	const Post = 'POST';
	/**
	 * @var string
	 */
	const Delete = 'DELETE';
	/**
	 * @var string
	 */
	const Options = 'OPTIONS';
	/**
	 * @var string
	 */
	const Copy = 'COPY';
	/**
	 * @var string
	 */
	const Patch = 'PATCH';
	/**
	 * @var string
	 */
	const Merge = 'MERGE';
	/**
	 * @var string
	 */
	const Trace = 'TRACE';
	/**
	 * @var string
	 */
	const Connect = 'CONNECT';
}
