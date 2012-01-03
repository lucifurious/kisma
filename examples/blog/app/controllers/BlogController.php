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
 * @category		Kisma_Components
 * @package			kisma.components
 * @since			v1.0.0
 * @filesource
 */

use Kisma\K;
use Kisma\Components;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * BlogController
 * Example controller for the blog example
 */
class BlogController extends \Kisma\Components\Controller
{
	//*************************************************************************
	//* Actions
	//*************************************************************************

	/**
	 * @param \Silex\Application                        $app
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function indexAction( Application $app, Request $request )
	{
		\Kisma\Utility\Log::info( 'Action index called.' );
		$app->render( 'blog/index.twig' );
	}

	/**
	 * @param \Silex\Application                        $app
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function showAction( Application $app, Request $request )
	{
		\Kisma\Utility\Log::info( 'Action show called.' );
	}

	/**
	 * @param \Silex\Application                        $app
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function updateAction( Application $app, Request $request )
	{
		\Kisma\Utility\Log::info( 'Action update called.' );
	}

	/**
	 * @param \Silex\Application                        $app
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function createAction( Application $app, Request $request )
	{
		\Kisma\Utility\Log::info( 'Action create called.' );
	}

}
