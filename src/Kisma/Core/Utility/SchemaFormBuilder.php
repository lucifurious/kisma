<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2013 Jerry Ablan <jerryablan@gmail.com>
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

use Kisma\Core\Interfaces\UtilityLike;
use Kisma\Core\SeedUtility;

/**
 * SchemaFormBuilder
 * Builds generic forms from generic schemas
 */
class SchemaFormBuilder implements UtilityLike
{
	/**
	 * @param array|string $schema
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public static function create( $schema )
	{
		$_schema = $schema;

		if ( !is_array( $_schema ) || ( is_string( $_schema ) && false === ( $_schema = json_decode( $schema, true ) ) ) )
		{
			throw new \InvalidArgumentException( 'You must provide a schema in either an array or a JSON string.' );
		}

		return static::_buildFormFields( $_schema );
	}

	/**
	 * @param array $schema
	 *
	 * @return string
	 */
	protected static function _buildFormFields( $schema )
	{
		$_form = null;

		foreach ( $schema as $_field => $_settings )
		{
			$_tag = null;
			$_value = Option::get( $_settings, 'value' );
			$_label = Option::get( $_settings, 'label', $_field );
			$_labelAttributes = Option::get( $_settings, 'label_attributes', array( 'for' => $_field ) );

			$_attributes = array_merge(
				array(
					 'name' => $_field,
					 'id'   => $_field,
				),
				Option::get( $_settings, 'attributes', array() )
			);

			if ( false !== ( $_required = Option::get( $_settings, 'required', false ) ) )
			{
				$_attributes['class'] = HtmlMarkup::addValue( Option::get( $_attributes, 'class' ), 'required' );
			}

			$_form .= HtmlMarkup::label( $_labelAttributes, $_label );

			switch ( $_settings['type'] )
			{
				case 'text':
					$_form .= HtmlMarkup::tag( 'textarea', $_attributes, $_value ) . PHP_EOL;
					break;

				case 'select':
					$_attributes['value'] = $_value ? : Option::get( $_settings, 'default' );
					$_attributes['size'] = Option::get( $_settings, 'size', 1 );
					$_form .= HtmlMarkup::select( Option::get( $_settings, 'options', array() ), $_attributes ) . PHP_EOL;
					break;

				default:
					$_attributes['type'] = 'text';
					$_attributes['value'] = $_value ? : Option::get( $_settings, 'default' );
					$_attributes['maxlength'] = Option::get( $_settings, 'length' );
					$_form .= HtmlMarkup::tag( 'input', $_attributes, null, true, true ) . PHP_EOL;
					break;
			}
		}

		return $_form;
	}
}

