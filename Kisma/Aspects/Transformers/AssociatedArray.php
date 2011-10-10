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
 * @category      Kisma_Aspects_Transformers
 * @namespace     \Kisma\Aspects\Transformers
 * @package       kisma.aspect.transformers
 * @since         v1.0.0
 * @filesource
 */

/**************************************************************************
/* Namespace Declarations
/*************************************************************************/

/**
 * @namespace Kisma Kisma
 */
namespace Kisma\Aspects\Transformers
{
	//*************************************************************************
	//* Imports
	//*************************************************************************

	use Kisma\Components as Components;
	use Kisma\Services as Services;
	use Kisma\Utility as Utility;

	//*************************************************************************
	//* Requirements
	//*************************************************************************

	/**
	 * Array
	 * Class description
	 */
	class AssociatedArray extends Components\Aspect implements \Kisma\ITransformer
	{
		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * Returns an associated array of data representing the value.
		 *
		 * @param string|object|array $value The value being encoded
		 * @param array $options [optional]
		 * @return array
		 */
		public function transform( $value, $options = array() )
		{
			if ( !is_string( $value ) | !is_object( $value ) || !is_array( $value ) )
			{
				throw new InvalidTransformerInputException( 'The type "' . gettype( $value ) . '" is not supported.' );
			}

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
}