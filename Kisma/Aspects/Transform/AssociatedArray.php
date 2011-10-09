<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright     Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link          http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license       http://github.com/Pogostick/kisma/licensing/
 * @author        Jerry Ablan <kisma@pogostick.com>
 * @category      Kisma_Aspects_Transform
 * @namespace     \Kisma\Aspects\Transform
 * @package       kisma.aspect.transform
 * @since         v1.0.0
 * @filesource
 */

/**************************************************************************
/* Namespace Declarations
/*************************************************************************/

/**
 * @namespace Kisma Kisma
 */
namespace Kisma\Aspects\Transform;

//*************************************************************************
//* Requirements
//*************************************************************************

/**
 * Array
 * Class description
 */
class AssociatedArray extends \Kisma\Components\Aspect implements \Kisma\ITransform
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Returns an associated array of data representing the value.
	 * @param mixed|object|array $value The value being encoded
	 * @param int $options [optional] Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT.
	 * @return string a JSON encoded string on success.
	 */
	public function associatedArrayTransform( $value, $options )
	{
		$_newValue = $value;

		//	Already an array
		if ( is_array( $_newValue ) )
		{
			return $_newValue;
		}

		//	Convert to common format
		return $value->toArray();
	}
}