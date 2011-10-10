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
	 * Html
	 * Transforms structured data to HTML
	 */
	class Html extends \Kisma\Components\Aspect implements \Kisma\ITransformer
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
		 *
		 * @param array|mixed|object $input The value being transformed
		 * @param array $options
		 * @return string
		 */
		public function transform( $input, $options = array() )
		{
			return $input;
		}

		//*************************************************************************
		//* Private Methods
		//*************************************************************************

		//*************************************************************************
		//* Properties
		//*************************************************************************

	}

}