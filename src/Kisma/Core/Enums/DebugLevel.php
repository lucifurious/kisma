<?php
/**
 * DebugLevel.php
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
 */
class DebugLevel extends SeedEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int Default value
	 */
	const __default = self::Normal;
	/**
	 * @var int
	 */
	const Normal = 0;
	/**
	 * @var int
	 */
	const Verbose = 1;
	/**
	 * @var int
	 */
	const VeryChatty = 2;
	/**
	 * @var int
	 */
	const WillNotShutUp = 3;
	/**
	 * @var int
	 */
	const Nutty = 4;
	/**
	 * @var int
	 */
	const Satanic = 666;

}
