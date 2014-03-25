<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
 *
 * @link Original code found here <https://gist.github.com/Arbow/982320>
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
namespace Kisma\Core\Components;

/**
 * LifeGuard
 * Keeps an eye on the pool
 */
class LifeGuard extends Seed
{
	//*************************************************************************
	//	Constants
	//*************************************************************************

	/**
	 * @type int The default maximum number of threads in the pool
	 */
	const DEFAULT_MAX_THREADS = 1;
	/**
	 * @type int The maximum threads that this component will watch
	 */
	const MAX_ALLOWED_THREADS = 100;

	//*************************************************************************
	//	Members
	//*************************************************************************

	/**
	 * @var array
	 */
	protected $_pool = array();
	/**
	 * @var callable
	 */
	protected $_callback;

	//*************************************************************************
	//	Methods
	//*************************************************************************

	public function __construct( $callback, $maxThreads = LifeGuard::DEFAULT_MAX_THREADS, $timeToRun = Thread::DEFAULT_THREAD_MAX_TIME_TO_RUN )
	{
		if ( !is_callable( $callback ) )
		{
			throw new \InvalidArgumentException( 'The callback specified is not callable.' );
		}

		$timeToRun = empty( $timeToRun ) ? PHP_INT_MAX : $timeToRun;

		$maxThreads = ( $maxThreads > 0 ? $maxThreads : static::DEFAULT_MAX_THREADS ) > static::MAX_ALLOWED_THREADS ?
			static::MAX_ALLOWED_THREADS : $maxThreads;

		$this->_callback = $callback;
		$this->_pool = new ThreadPool( $maxThreads, $timeToRun );
	}

	/**
	 * @param $task
	 */
	public function executeAsync( $task )
	{
		$this->_pool->scheduleCommand( $this, $task, $this->_callback );
	}

	/**
	 * @param $task
	 */
	public function executeWaitTerminal( $task )
	{
		if ( count( $this->_pool->_io ) > 0 )
		{
			$this->waitForAllTerminal();
		} //execute after current tasks end
		$future = $this->_pool->scheduleCommand( $this, $task, $this->_callback );
		while ( !$future->finished )
		{
			$this->_pool->runOnce();
		}
	}

	/**
	 *
	 */
	public function waitForAllTerminal()
	{
		$this->_pool->loop();
	}
}
