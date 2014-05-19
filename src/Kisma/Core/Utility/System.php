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
namespace Kisma\Core\Utility;

use Doctrine\Common\Cache\Cache;
use Kisma\Core\SeedUtility;

/**
 * System utilities
 */
class System extends SeedUtility
{
    /**
     * @param string $size Size of values returned:
     *                     'b' = bytes, 'k' = kilobytes, 'm' = mb, 'g' = gigabytes, 't' = terabytes.
     *                     Default is 'k', kilobytes. All kilobytes are returned in powers of 1024
     *
     * @return array Array of memory statistics from the running system.
     */
    public static function memory( $size = 'k' )
    {
        static $_validSizes = array( 'b', 'k', 'm', 'g', 't' );
        static $_stats = array(
            'timestamp' => false,
            'total'     => 0,
            'used'      => 0,
            'free'      => 0,
            'pct_free'  => 0,
            'php_limit' => 0,
        );

        if ( !isset( $_stats['created_at'] ) || empty( $_stats['created_at'] ) )
        {
            $_stats['created_at'] = microtime( true );
        }

        if ( !in_array( $size, $_validSizes ) )
        {
            $size = 'k';
        }
        else if ( 't' == $size )
        {
            $size = '-tera ';
        }

        try
        {
            $_memory = @explode( ' ', @trim( @str_ireplace( 'total:', null, exec( 'free -' . $size . 't' ) ) ) );

            if ( 3 != count( $_memory ) )
            {
                return false;
            }
        }
        catch ( \Exception $_ex )
        {
            return false;
        }

        $_stats[ Cache::STATS_MEMORY_USAGE ] = $_memory[1];
        $_stats[ Cache::STATS_MEMORY_AVAILABLE ] = $_memory[2];
        $_stats[ Cache::STATS_UPTIME ] =
            DateTime::prettySeconds( ( $_stats['updated_at'] = microtime( true ) ) - $_stats['created_at'] + $_stats[ Cache::STATS_UPTIME ] );

        $_stats['source_data'] = $_memory;
        $_stats['php.memory_limit'] = ini_get( 'memory_limit' );
        $_stats['memory_pct_free'] = ( $_memory[0] > 0 ? round( ( $_memory[1] / $_memory[0] ) * 100, 2 ) : 0 );
        $_stats['memory_total'] = $_memory[0];

        return $_stats;
    }
}