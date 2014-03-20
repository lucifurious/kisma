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
namespace Kisma\Core\Components;

use Kisma\Core\Enums\PromiseEvents;
use Kisma\Core\Events\Enums\DeferredEvents;
use Kisma\Core\Interfaces\DeferredLike;
use Kisma\Core\Interfaces\PromiseLike;

/**
 * Promise
 */
class Promise implements PromiseLike
{
    //*************************************************************************
    //	Members
    //*************************************************************************

    /**
     * @var string
     */
    protected $_state = null;
    /**
     * @var DeferredLike[]
     */
    protected $_deferred;
    /**
     * @var array[] The callbacks implemented by this promise
     */
    protected static $_callbacks = array(
        DeferredEvents::RESOLVE => array(
            'event'     => PromiseEvents::DONE,
            'callbacks' => array(),
            'state'     => 'resolved'
        ),
        DeferredEvents::REJECT  => array(
            'event'     => PromiseEvents::FAIL,
            'callbacks' => array(),
            'state'     => 'rejected'
        ),
        DeferredEvents::NOTIFY  => array(
            'event'     => PromiseEvents::PROGRESS,
            'callbacks' => array(),
            'state'     => false
        ),
    );

    /**
     * @param DeferredLike $deferred
     */
    public function __construct( $deferred )
    {
    }

    /**
     *
     */
    public function resolve()
    {
        foreach ( static::$_callbacks[DeferredEvents::RESOLVE] as $_listener )
        {
            //  Call listeners
        }
    }

    /**
     *
     */
    public function reject()
    {
        foreach ( static::$_callbacks[DeferredEvents::REJECT] as $_listener )
        {
            //  Call listeners
        }
    }

    /**
     *
     */
    public function notify()
    {
        foreach ( static::$_callbacks[DeferredEvents::NOTIFY] as $_listener )
        {
            //  Call listeners
        }
    }

    /**
     *
     */
    public function then()
    {
    }

    /**
     * Is performed always
     *
     * @return string
     */
    public function always()
    {
    }

    /**
     * Return the current state of the promise
     *
     * @return string
     */
    public function getState()
    {
        return $this->_state;
    }
}
