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
 * Thread
 * A general purpose thread class
 */
class Thread extends Seed
{
    //*************************************************************************
    //	Constants
    //*************************************************************************

    /**
     * @type int The number of ms for a thread to run
     */
    const DEFAULT_THREAD_MAX_TIME_TO_RUN = 0;
    /**
     * @type int
     */
    const STDIN = 0;
    /**
     * @type int
     */
    const STDOUT = 1;
    /**
     * @type int
     */
    const STDERR = 2;

    //*************************************************************************
    //	Members
    //*************************************************************************

    /**
     * @var resource
     */
    protected $_process = null;
    /**
     * @var array
     */
    protected $_io = array();
    /**
     * @var string
     */
    protected $_output = null;
    /**
     * @var string
     */
    protected $_ioBuffer = null;
    /**
     * @var null
     */
    protected $_error = null;
    /**
     * @var int
     */
    protected $_timeToRun = self::DEFAULT_THREAD_MAX_TIME_TO_RUN;
    /**
     * @var int
     */
    protected $_startTime;

    //*************************************************************************
    //	Methods
    //*************************************************************************

    /**
     * @param int $timeToRun
     */
    public function __construct( $timeToRun = self::DEFAULT_THREAD_MAX_TIME_TO_RUN )
    {
        $this->_process = 0;
        $this->_io = array();
        $this->_ioBuffer = $this->_output = $this->_error = null;

        $this->_maxRunTime = $timeToRun;
        $this->_startTime = microtime( true );
    }

    /**
     * @param callable|string $process
     * @param int             $timeToRun
     *
     * @return Thread
     */
    public static function create( $process, $timeToRun = null )
    {
        $_thread = new self( $timeToRun );

        $_threadInfo = array(
            static::STDIN  => array( 'pipe', 'r' ),
            static::STDOUT => array( 'pipe', 'w' ),
            static::STDERR => array( 'pipe', 'w' )
        );

        $_thread->_process = proc_open( $process, $_threadInfo, $_thread->_io );

        //  Set STDOUT and STDERR to be non-blocking
        stream_set_blocking( $_thread->_io[static::STDOUT], 0 );
        stream_set_blocking( $_thread->_io[static::STDERR], 0 );

        return $_thread;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        $this->_ioBuffer .= $this->listen();
        $_data = stream_get_meta_data( $this->_io[static::STDOUT] );

        return !Option::getBool( $_data, 'eof' );
    }

    /**
     * @return int
     */
    public function stop()
    {
        $_result = \proc_close( $this->_process );
        $this->_process = null;

        return $_result;
    }

    /**
     * Pushes a message to the process via STDIN
     *
     * @param mixed $message
     */
    public function push( $message )
    {
        fwrite( $this->_io[static::STDIN], $message );
    }

    /**
     * Retrieve the output, if any
     *
     * @return null|string
     */
    function listen()
    {
        $_buffer = $this->_ioBuffer;
        $this->_ioBuffer = null;

        while ( true )
        {
            $_data = stream_get_contents( $this->_io[static::STDOUT] );

            if ( empty( $_data ) )
            {
                break;
            }

            $_buffer .= $_data;
        }

        return $_buffer;
    }

    /**
     * Get process status
     *
     * @return array
     */
    function getStatus()
    {
        return proc_get_status( $this->_process );
    }

    //See if the command is taking too long to run (more than $this->_maxRunTime seconds)
    function isBusy()
    {
        return ( $this->_startTime > 0 ) && ( $this->_startTime + $this->_maxRunTime < time() );
    }

    //What command wrote to STDERR
    function getError()
    {
        $buffer = "";
        while ( $r = fgets( $this->_io[static::STDERR], 1024 ) )
        {
            $buffer .= $r;
        }

        return $buffer;
    }

    function getDurationSeconds()
    {
        return time() - $this->_startTime;
    }
}

class Future
{
    var $taskId;
    var $command;
    var $result;
    var $error;
    var $finished = false;
    var $started = false;
    var $thread;
    var $callback;
    var $executor;

    function Future( $taskId, $command, $callback, $executor )
    {
        $this->taskId = $taskId;
        $this->command = $command;
        $this->callback = $callback;
        $this->executor = $executor;
    }

    function startup( $timeout )
    {
        $this->started = true;
        $this->thread = Thread::create( $this->command, $timeout );
    }

    function end( $result, $error )
    {
        $this->result = $result;
        $this->_error = $error;
        $this->finished = true;
        call_user_func( $this->callback, $this->result, $this->_error );
    }
}
