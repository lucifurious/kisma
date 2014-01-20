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
namespace Kisma\Core\Interfaces\Events;

/**
 * ServiceLike
 * Defines the event interface for all services
 */
interface ServiceLike
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string Fired before service call
	 */
	const BeforeServiceCall = 'kisma.core.service_like.before_service_call';
	/**
	 * @var string Fired when the service call succeeded
	 */
	const Success = 'kisma.core.service_like.success';
	/**
	 * @var string Fired if there was a failure in the service call
	 */
	const Failure = 'kisma.core.service_like.failure';
	/**
	 * @var string Fired after service call
	 */
	const AfterServiceCall = 'kisma.core.service_like.after_service_call';
	/**
	 * @var string Fired when processing is complete
	 */
	const Complete = 'kisma.core.service_like.complete';
}
