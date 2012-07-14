<?php
/**
 * @file
 * Provides ...
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Framework
 * @package kisma
 * @since 1.0.0
 *
 * @ingroup framework
 */

namespace Kisma\Utility;

//*************************************************************************
//* Aliases
//*************************************************************************

use Kisma\Kisma;
use Kisma\Components\Seed;
use Kisma\IUtility;

//*************************************************************************
//* Requirements
//*************************************************************************

/**
 * Render
 */
class Render extends Seed implements IUtility
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Render a bootstrap template
	 *
	 * @param array $payload
	 */
	public static function bootstrap( $payload = array() )
	{
		$_template = Option::o( $payload, 'template', '_bootstrap_container.twig' );
		Kisma::app()->render( $_template, $payload );
	}

}
