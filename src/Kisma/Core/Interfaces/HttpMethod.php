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
	const GET = 'GET';
	/**
	 * @var string
	 */
	const PUT = 'PUT';
	/**
	 * @var string
	 */
	const HEAD = 'HEAD';
	/**
	 * @var string
	 */
	const POST = 'POST';
	/**
	 * @var string
	 */
	const DELETE = 'DELETE';
	/**
	 * @var string
	 */
	const OPTIONS = 'OPTIONS';
	/**
	 * @var string
	 */
	const COPY = 'COPY';
	/**
	 * @var string
	 */
	const PATCH = 'PATCH';
	/**
	 * @var string
	 */
	const MERGE = 'MERGE';
	/**
	 * @var string
	 */
	const TRACE = 'TRACE';
	/**
	 * @var string
	 */
	const CONNECT = 'CONNECT';

	/**
	 * @deprecated in v0.2.21, removal in v0.3.0
	 * @var string
	 */
	const Get = 'GET';
	/**
	 * @deprecated in v0.2.21, removal in v0.3.0
	 * @var string
	 */
	const Put = 'PUT';
	/**
	 * @deprecated in v0.2.21, removal in v0.3.0
	 * @var string
	 */
	const Head = 'HEAD';
	/**
	 * @deprecated in v0.2.21, removal in v0.3.0
	 * @var string
	 */
	const Post = 'POST';
	/**
	 * @deprecated in v0.2.21, removal in v0.3.0
	 * @var string
	 */
	const Delete = 'DELETE';
	/**
	 * @deprecated in v0.2.21, removal in v0.3.0
	 * @var string
	 */
	const Options = 'OPTIONS';
	/**
	 * @deprecated in v0.2.21, removal in v0.3.0
	 * @var string
	 */
	const Copy = 'COPY';
	/**
	 * @deprecated in v0.2.21, removal in v0.3.0
	 * @var string
	 */
	const Patch = 'PATCH';
	/**
	 * @deprecated in v0.2.21, removal in v0.3.0
	 * @var string
	 */
	const Merge = 'MERGE';
	/**
	 * @deprecated in v0.2.21, removal in v0.3.0
	 * @var string
	 */
	const Trace = 'TRACE';
	/**
	 * @deprecated in v0.2.21, removal in v0.3.0
	 * @var string
	 */
	const Connect = 'CONNECT';
}

