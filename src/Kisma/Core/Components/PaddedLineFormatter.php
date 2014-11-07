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

use Monolog\Formatter\LineFormatter;

/**
 * Pads the level name to 9 characters so logs line up
 */
class PaddedLineFormatter extends LineFormatter
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type int
     */
    const MAX_LEVEL_LENGTH = 9;
    /**
     * @type string
     */
    const SIMPLE_FORMAT = "[%datetime%][%level_name%] %message% %context% %extra%\n";

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * {@inheritdoc}
     */
    public function format( array $record )
    {
        if ( isset( $record['level_name'] ) )
        {
            //  Pad the level name so the log lines are event
            $record['level_name'] = str_pad( $record['level_name'], static::MAX_LEVEL_LENGTH - strlen( $record['level_name'] ), ' ' );
        }

        return parent::format( $record );
    }

}