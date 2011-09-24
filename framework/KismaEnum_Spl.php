<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license			http://github.com/Pogostick/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @category		Kisma
 * @package			kisma
 * @namespace		Kisma
 * @since			v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma Kisma
 */
namespace Kisma;

//*************************************************************************
//* Requirements
//*************************************************************************

require_once __DIR__ . '/interfaces.php';

/**
 *
 */
class KismaEnum extends \SplEnum implements \Kisma\IKisma
{
}
