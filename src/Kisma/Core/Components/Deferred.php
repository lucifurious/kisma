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

use Kisma\Core\Interfaces\DeferredLike;

/**
 * Deferred
 * A PHP version of the jQuery Deferred object
 */
class Deferred extends Seed implements DeferredLike
{
    /**
     * @var string
     */
    protected $_state;
    /**
     * @var object
     */
    protected $_promise;

    /**
     *
     */
    public function __construct()
    {
        $this->_promise = new Promise( $this );
    }

    /**
     * @return Promise
     */
    public function promise()
    {
        return $this->_promise ? : $this->_promise = new Promise( $this, func_get_args() );
    }

    /**
     * @return object
     */
    public function getPromise()
    {
        return $this->_promise;
    }

    /**
     * @param string $state
     *
     * @throws InvalidArgumentException
     * @return Deferred
     */
    public function setState( $state )
    {
        if ( !DeferredStates::contains( $state ) )
        {
            throw new InvalidArgumentException( 'The state "' . $state . '" is invalid.' );
        }

        $this->_state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->_state;
    }
}
