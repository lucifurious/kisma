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
 * @category		Kisma\Examples\Blog
 * @package			kisma.examples.blog.controllers
 * @since			v1.0.0
 * @filesource
 */

use Kisma\K;
use Kisma\Components;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use \Doctrine\CouchDB\HTTP\HTTPException;

/**
 * BlogController
 * Example controller for the blog example
 */
class BlogController extends \Kisma\Components\Controller
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const DatabaseName = 'kisma_examples_blog_posts';

	/**
	 * @var \Doctrine\CouchDB\CouchDBClient
	 */
	protected $_client = null;

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * @param Kisma\Event\ControllerEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeAction( \Kisma\Event\ControllerEvent $event )
	{
		//	Open zee db...
		\Kisma\Kisma::app()->register( new \Kisma\Provider\CouchDbServiceProvider() );
		$_app = \Kisma\Kisma::app();
		$this->_client = $_app['couchdbs']['db.blog']->getCouchDBClient();

		return parent::onBeforeAction( $event );
	}

	//*************************************************************************
	//* Actions
	//*************************************************************************

	/**
	 * @param \Silex\Application|\Kisma\Kisma			$app
	 * @param \Symfony\Component\HttpFoundation\Request  $request
	 */
	public function indexAction( Application $app, Request $request )
	{
		\Kisma\Utility\Log::info( 'Action index called.' );

		try
		{
			$_info = $this->_client->getDatabaseInfo( self::DatabaseName );
		}
		catch ( \Doctrine\CouchDB\HTTP\HTTPException $_ex )
		{
			if ( 404 == $_ex->getCode() )
			{
				//	Database not there, create...
				$this->_client->createDatabase( self::DatabaseName );
			}
		}

		$_data = array(
			'blogs' => $this->_client->getChanges(),
		);

		\Kisma\Utility\Log::trace( 'Data = ' . print_r( $_data, true ) );

		$app->render(
			'blog/index.twig',
			$_data
		);
	}

	/**
	 * @param \Silex\Application|\Kisma\Kisma						$app
	 * @param \Symfony\Component\HttpFoundation\Request			  $request
	 */
	public function showAction( Application $app, Request $request )
	{
		\Kisma\Utility\Log::info( 'Action show called.' );
	}

	/**
	 * @param \Silex\Application|\Kisma\Kisma						$app
	 * @param \Symfony\Component\HttpFoundation\Request			  $request
	 */
	public function updateAction( Application $app, Request $request )
	{
		\Kisma\Utility\Log::info( 'Action update called.' );
	}

	/**
	 * @param \Silex\Application|\Kisma\Kisma						$app
	 * @param \Symfony\Component\HttpFoundation\Request			  $request
	 */
	public function createAction( Application $app, Request $request )
	{
		\Kisma\Utility\Log::info( 'Action create called.' );
	}

}
