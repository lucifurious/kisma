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
class Html extends \Kisma\Components\Aspect implements \Kisma\ITransform
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Returns an HTML representation of the value.
	 * @param mixed|object|array $value The value being transformed
	 * @param array $options
	 * @return string
	 */
	public function htmlTransform( $value, $options = array() )
	{
		return $value;
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	//*************************************************************************
	//* Properties
	//*************************************************************************

}