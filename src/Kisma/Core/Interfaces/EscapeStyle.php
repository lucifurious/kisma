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
 * EscapeStyle.php
 * Defines the various ways characters can be escaped
 *
 * @deprecated in v0.2.44, to be removed in v0.3.0.
 * @see        \Kisma\Core\Enums\EscapeStyle
 */
interface EscapeStyle
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var int No escape (AARRGH!)
     */
    const NONE = 0;
    /**
     * @var int Escaped with a double character (i.e. '')
     */
    const DOUBLED = 1;
    /**
     * @var int Escaped with a backslash (i.e. \')
     */
    const SLASHED = 2;
}
