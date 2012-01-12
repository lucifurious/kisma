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

		require_once __DIR__ . '/../models/BlogPost.php';

		$_blog = new BlogPost();
		$_blog->title = 'Welcome to the blog!';
		$_blog->author = 'kisma_dude';
		$_blog->postDate = date( 'c' );
		$_blog->body = <<<HTML
<p>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse ullamcorper sapien vitae ligula placerat elementum. Pellentesque accumsan pellentesque eros sed facilisis. Phasellus pulvinar nulla non dolor scelerisque vitae mollis lacus adipiscing. Curabitur tristique tempor dolor quis tincidunt. Vestibulum placerat accumsan varius. Duis ultricies vestibulum hendrerit. Morbi porttitor tincidunt velit, eget rutrum lacus convallis egestas. Fusce tristique sagittis magna, vel porta mauris commodo pretium. Aliquam fermentum ullamcorper nulla. Etiam est turpis, sodales a semper vitae, fringilla quis nisl. Nam rhoncus velit tortor, et tristique nibh. Pellentesque iaculis, nisl malesuada sollicitudin blandit, purus est eleifend ipsum, quis vestibulum eros ante a sapien. Fusce turpis tellus, eleifend eget aliquet accumsan, imperdiet eu dui. Vivamus ac urna vel nibh tincidunt pellentesque ut sed enim. Maecenas aliquet ultricies magna a dictum. Sed varius, lorem vel consequat fermentum, purus ligula venenatis sem, in sollicitudin lorem felis non ipsum.
</p>
<p>
Fusce pellentesque elementum elit, quis tincidunt eros semper ut. Aliquam erat volutpat. Nam vel nisi purus. Nulla egestas eros eu augue vestibulum lacinia. Nunc mauris nisi, pulvinar a vestibulum eu, ultrices non metus. Mauris sit amet suscipit nunc. Integer mi justo, ultricies sit amet porta id, feugiat nec dolor. Aliquam vel nibh eget sem elementum dapibus. Nam in facilisis erat. Pellentesque libero nulla, rutrum sed interdum ut, ultrices ut sem. Curabitur sodales lobortis mi id feugiat. Fusce hendrerit cursus magna ac tincidunt. Sed egestas lacinia sodales.
</p>
<p>
Nunc placerat tortor eget metus suscipit id porta diam dignissim. Donec et consequat odio. Nullam nec ipsum et tortor consequat vulputate nec id mi. Morbi fringilla lectus a tellus euismod at posuere lorem tempor. Curabitur sit amet metus non lacus aliquet hendrerit. Suspendisse pretium, augue non eleifend tempor, quam nibh molestie mauris, in dignissim leo diam vitae sem. Integer sagittis sagittis placerat.
</p>
<p>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean fermentum vehicula felis vitae aliquet. Sed commodo commodo elit, vel tempor neque vestibulum vitae. Praesent ullamcorper pellentesque molestie. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer nulla purus, ornare a viverra commodo, interdum nec lorem. Sed pharetra nisl in odio consectetur laoreet.
</p>
<p>
Curabitur a eros arcu. Mauris malesuada vestibulum commodo. Sed scelerisque orci in est porta pulvinar. Nulla fermentum faucibus feugiat. Maecenas vitae est erat, at suscipit arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec sed libero sit amet tellus pretium elementum. Pellentesque lacinia erat tempus sem interdum viverra. Donec placerat congue nulla, eu fermentum ligula rhoncus ut. Sed tincidunt accumsan ligula, nec tincidunt urna scelerisque vitae. Proin porttitor bibendum mi at rhoncus.
</p>
HTML;

		$_data = array(
			'blogs' => $this->_client->allDocs( 25 ),
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
