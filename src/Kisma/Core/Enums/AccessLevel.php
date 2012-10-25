<?php
/**
 * AccessLevel.php
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license   http://github.com/Pogostick/Kisma/blob/master/LICENSE
 * @author    Jerry Ablan <kisma@pogostick.com>
 */
namespace Kisma\Core\Enums;
/**
 * AccessLevel
 * Various pre-defined application access levels
 */
class AccessLevel extends SeedEnum implements \Kisma\Core\Interfaces\AccessLevels
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int The default access level
	 */
	const __default = self::Guest;
}
