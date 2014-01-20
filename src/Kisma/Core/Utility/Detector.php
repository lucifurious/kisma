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

use Kisma\Core\Interfaces\PhpFrameworks;

/**
 * Detector
 * Provides detection services for various things
 */
class Detector implements PhpFrameworks
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Extensible framework sniffer.
	 * Subclass and add a YourClass::sniff_[framework]() method.
	 *
	 * @param string $path
	 */
	public static function framework( $path = null )
	{
		foreach ( \Kisma\Core\Enums\PhpFrameworks::getDefinedConstants() as $_constant => $_value )
		{
			$_thisClass = get_called_class();
			$_method = 'sniff_' . $_value;

			if ( method_exists( $_thisClass, $_method ) && call_user_func( array( $_thisClass, $_method ) ) )
			{
				\Kisma::set( 'app.framework', $_value );
//				Log::debug( 'PHP framework detected: ' . $_constant . ' (' . $_value . ')' );

				switch ( $_constant )
				{
					case \Kisma\Core\Enums\PhpFrameworks::Yii:
						/**
						 * Pull in all the parameters from the Yii app into the bag...
						 */
						foreach ( \Yii::app()->getParams()->toArray() as $_parameterName => $_parameterValue )
						{
							\Kisma::set( $_parameterName, $_parameterValue );
						}
						break;
				}

				return $_value;
			}
		}
	}

	/**
	 * Sniffs for the Yii Framework. Pretty stupidly...
	 *
	 * @return bool
	 */
	public static function sniff_yii()
	{
		return class_exists( '\\Yii', false );
	}
}
