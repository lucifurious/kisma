<?php
/**
 * OperationMode.php
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
 * OperationMode
 * Predefined operation modes
 */
class OperationMode extends SeedEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const __default = self::Development;
	/**
	 * @var string
	 */
	const Development = 'development';
	/**
	 * @var string
	 */
	const Testing = 'testing';
	/**
	 * @var string
	 */
	const Integration = 'integration';
	/**
	 * @var string
	 */
	const Production = 'production';
}
