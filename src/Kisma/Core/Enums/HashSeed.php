<?php
/**
 * HashSeed.php
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
 * HashSeed
 * The various supported hash types for the Hash utility class
 */
class HashSeed extends SeedEnum implements \Kisma\Core\Interfaces\HashSeed
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int Default value
	 */
	const __default = self::All;
}
