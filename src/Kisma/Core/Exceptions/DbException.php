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

use Kisma\Core\Interfaces\HttpResponse;
use Kisma\Core\Utility\Option;

/**
 * DatabaseException
 */
class DatabaseException extends SeedException
{
    //*************************************************************************
    //	Members
    //*************************************************************************

    /**
     * @var string
     */
    protected $_sqlState;
    /**
     * @var string
     */
    protected $_driverName;
    /**
     * @var string|int
     */
    protected $_driverCode;
    /**
     * @var string
     */
    protected $_driverMessage;

    //*************************************************************************
    //	Methods
    //*************************************************************************

    /**
     * @param \PDO           $pdo       The PDO instance on which the error occurred
     * @param \Exception     $exception The exception thrown
     * @param int|mixed|null $code      The HTTP response code to use. Defaults to 500 - Internal Server Error
     * @param mixed          $previous  The previous exception
     * @param array          $context   Additional information for downstream consumers
     */
    public function __construct( $pdo, $exception, $code = HttpResponse::InternalServerError, $previous = null, $context = null )
    {
        $this->_driverName = $pdo->getAttribute( \PDO::ATTR_DRIVER_NAME );

        $this->_sqlState = Option::get( $_info, 0 );
        $this->_driverCode = Option::get( $_info, 1 );
        $this->_driverMessage = Option::get( $_info, 2 );

        parent::__construct( $exception, $code, $previous, $context );
    }

    /**
     * @return int|string
     */
    public function getDriverCode()
    {
        return $this->_driverCode;
    }

    /**
     * @return string
     */
    public function getDriverMessage()
    {
        return $this->_driverMessage;
    }

    /**
     * @return string
     */
    public function getDriverName()
    {
        return $this->_driverName;
    }

    /**
     * @return string
     */
    public function getSqlState()
    {
        return $this->_sqlState;
    }

}
