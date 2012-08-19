<?php
/**
 * HashType.php
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
 * HashType
 * Supported hash algorithms
 */
class HashType extends SeedEnum implements \Kisma\Core\Interfaces\HashType
{
	/**
	 * @var int
	 */
	const __default = self::SHA1;
}
