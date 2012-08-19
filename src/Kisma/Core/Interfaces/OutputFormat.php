<?php
/**
 * OutputFormat.php
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2012 Jerry Ablan/Pogostick, LLC.
 * @license   http://github.com/Pogostick/Kisma/blob/master/LICENSE
 * @author    Jerry Ablan <kisma@pogostick.com>
 */
namespace Kisma\Core\Interfaces;
/**
 * OutputFormat
 * Various pre-defined output formats
 */
interface OutputFormat
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const Raw = 0;
	/**
	 * @var int
	 */
	const JSON = 1;
	/**
	 * @var int
	 */
	const XML = 2;
	/**
	 * @var int
	 */
	const HTTP = 3;
}
