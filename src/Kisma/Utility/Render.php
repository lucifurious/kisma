<?php
/**
 * @file
 * Provides ...
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
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

//*************************************************************************
//* Requirements 
//*************************************************************************

/**
 * Render
 */
class Render extends \Kisma\Components\Seed implements \Kisma\IUtility
{
	//*************************************************************************
	//* Public Methods 
	//*************************************************************************

	/**
	 * Render a bootstrap template
	 * @param array $payload
	 */
	public static function bootstrap( $payload = array() )
	{
		$_template = Option::o( $payload, 'template', '_bootstrap_container.twig' );
		\Kisma\Kisma::app()->render( $_template, $payload );
	}

}
