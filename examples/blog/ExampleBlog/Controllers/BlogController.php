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

namespace ExampleBlog\Controllers;

use Kisma\K;
use Kisma\Components;
use ExampleBlog\Document\BlogPost;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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
	 * @var string
	 */
	const DocumentName = 'ExampleBlog\\Documents\\BlogPost';

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Doctrine\CouchDB\CouchDBClient
	 */
	protected $_client = null;
	/**
	 * @var \Doctrine\ODM\CouchDB\DocumentManager
	 */
	protected $_documentManager = null;
	/**
	 * @var string
	 */
	protected $_documentName = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
		parent::__construct( $options );

		if ( null === $this->_documentName )
		{
			$this->_documentName = self::DocumentName;
		}
	}

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * @param \Kisma\Event\ControllerEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeAction( \Kisma\Event\ControllerEvent $event )
	{
		//	Open zee db...
		\Kisma\Kisma::app()->register( new \Kisma\Provider\CouchDb\ServiceProvider() );
		$_app = \Kisma\Kisma::app();

		$this->_documentManager = $_app['couchdbs']['db.blog'];
		$this->_client = $this->_documentManager->getCouchDBClient();

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

		$_docs = $this->_client->allDocs( 25 );

		/**  */
		$_data = array(
			'blogs' => ( 200 == $_docs->status ) ? $_docs->body : array(),
		);

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
	protected function _edit( Application $app, Request $request )
	{
		$_model = new BlogPost();

		if ( isset( $_POST, $_POST['BlogPost'] ) )
		{
			$_payload = $_POST['BlogPost'];
			$_id = \Kisma\Utility\FilterInput::get( INPUT_GET, 'id', null, FILTER_SANITIZE_STRING );

			if ( empty( $_id ) )
			{
				$_model = new BlogPost();
				$_model->postDate = date( 'c' );
			}
			else
			{
				$_response = $this->_documentManager->find( $this->_documentName, $_id );

				if ( 200 == $_response->status )
				{
					$_model = new BlogPost( $_response->body );
				}
			}

			foreach ( $_payload as $_key => $_value )
			{
				$_model->{$_key} = $_value;
			}

			$this->_documentManager->persist( $_model );
			$this->_documentManager->flush();
		}

		$_editor = new \Kisma\Components\Widget\CkEditorWidget(
			array(
				'id' => 'BlogPost_body',
				'name' => 'BlogPost[body]',
			)
		);

		//	Render the editor
		$app->render(
			'edit.twig',
			array(
				'post' => $_model,
				'ckeditor' => $_editor->render( null, true ),
			)
		);
	}

	/**
	 * @param \Silex\Application|\Kisma\Kisma						$app
	 * @param \Symfony\Component\HttpFoundation\Request			  $request
	 */
	public function updateAction( Application $app, Request $request )
	{
		return $this->_edit( $app, $request );
	}

	/**
	 * @param \Silex\Application|\Kisma\Kisma						$app
	 * @param \Symfony\Component\HttpFoundation\Request			  $request
	 */
	public function createAction( Application $app, Request $request )
	{
		return $this->_edit( $app, $request );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param \Doctrine\CouchDB\CouchDBClient $client
	 *
	 * @return \BlogController
	 */
	public function setClient( $client )
	{
		$this->_client = $client;
		return $this;
	}

	/**
	 * @return \Doctrine\CouchDB\CouchDBClient
	 */
	public function getClient()
	{
		return $this->_client;
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\DocumentManager $documentManager
	 *
	 * @return \BlogController
	 */
	public function setDocumentManager( $documentManager )
	{
		$this->_documentManager = $documentManager;
		return $this;
	}

	/**
	 * @return \Doctrine\ODM\CouchDB\DocumentManager
	 */
	public function getDocumentManager()
	{
		return $this->_documentManager;
	}

}
