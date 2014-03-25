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

//Wrapper for Thread class
use Traversable;

class ThreadPool implements \IteratorAggregate, \Traversable
{
    /**
     * @var PoolConfig
     */
    protected $_config;

	var $poolSize;
	var $defaultTimeout;
	var $futures = array();
	var $pipes = array();
	var $queue = array();
	var $output;
	var $error;
	var $index = 0;

	function ThreadPool( $size, $timeout )
	{
		$this->poolSize = $size;
		$this->defaultTimeout = $timeout;
	}

	function scheduleCommand( $executor, $command, $callback )
	{
		$future = new Future( "" + $this->index++, $command, $callback, $executor );
		$this->futures[$future->taskId] = $future;
		$this->_output[$future->taskId] = "";
		$this->_error[$future->taskId] = "";
		if ( count( $this->_io ) >= $this->poolSize )
		{
			array_push( $this->queue, $future );
		}
		else
		{
			$this->scheduleNow( $future );
		}

		return $future;
	}

	function scheduleNow( $future )
	{
		$future->startup( $this->defaultTimeout );
		$this->_io[$future->taskId] = $future->thread->_io[1];
		echo 'thread ' . $future->taskId . " started, command:" . $future->command . "\n";
	}

	function loop()
	{
		while ( count( $this->_io ) > 0 )
		{
			$this->runOnce();
			while ( count( $this->_io ) < $this->poolSize && count( $this->queue ) > 0 )
			{
				$future = array_shift( $this->queue );
				$this->scheduleNow( $future );
			}
		}
	}

	function runOnce()
	{
		$streams = $this->_io;
		if ( count( $streams ) > 0 )
		{
			$read = $streams;
			$write = null;
			$except = null;
			stream_select( $read, $write, $except, 1 );
			foreach ( $read as $r )
			{
				$id = array_search( $r, $streams );
				$thread = $this->futures[$id]->thread;
				if ( $thread->isActive() )
				{
					$this->_output[$id] .= $thread->listen();
					if ( $thread->isBusy() )
					{
						$thread->close();
						unset( $this->_io[$id] );
						$this->futures[$id]->end( $this->_output[$id], "" );
						unset( $this->_output[$id] );
						echo
							"thread $id timeout, duration " .
							$this->futures[$id]->thread->getDurationSeconds() .
							"s, command:" .
							$this->futures[$id]->command .
							"\n";
					}
				}
				else
				{
					$this->_output[$id] .= $thread->listen();
					$this->_error[$id] .= $thread->getError();
					$thread->close();
					unset( $this->_io[$id] );
					$this->futures[$id]->end( $this->_output[$id], $this->_error[$id] );
					unset( $this->_output[$id] );
					unset( $this->_error[$id] );
					echo
						"thread $id completed, duration " .
						$this->futures[$id]->thread->getDurationSeconds() .
						"s, command:" .
						$this->futures[$id]->command .
						"\n";
				}

			}
		}
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Retrieve an external iterator
	 *
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing <b>Iterator</b> or
	 *       <b>Traversable</b>
	 */
	public function getIterator()
	{
		// TODO: Implement getIterator() method.
	}
}
