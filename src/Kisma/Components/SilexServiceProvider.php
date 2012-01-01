<?php
/**
 * @file
 * Provides a base for Silex Service Providers in Kisma
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Silex
 * @package kisma.components
 * @since 1.0.0
 *
 * @ingroup silex
 */

namespace Kisma\Components;

//*************************************************************************
//* Aliases 
//*************************************************************************

use Silex\Application;

/**
 * SilexServiceProvider
 * A base for Kisma Silex Service Providers
 */
abstract class SilexServiceProvider extends Service implements \Silex\ServiceProviderInterface
{
	//	Like the goggles, does nothing.
}
