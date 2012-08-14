<?php
/**
 * Control.php
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/lucifurious/kisma/licensing/} for complete information.
 *
 * @copyright          Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 * @link               http://github.com/lucifurious/kisma/ Kisma(tm)
 * @license            http://github.com/lucifurious/kisma/licensing/
 * @author             Jerry Ablan <get.kisma@gmail.com>
 * @filesource
 */
namespace Kisma\Core\Services;

/**
 * Control
 */
abstract class Control extends \Kisma\Core\Service implements \Kisma\Core\Interfaces\ControlEvents
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string Storage key/nickname/short name for this controller
	 */
	protected $_tag = null;
	/**
	 * @var Route[] The routes I know about
	 */
	protected $_routes = array();

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param string $tag
	 * @param Route  $route
	 */
	public function addRoute( $tag, $route )
	{
		\Kisma\Utility\Option::set( $this->_routes, $tag, $route );
	}

	/**
	 * actions requests to actions
	 *
	 * @param \Kisma\Core\Services\Application|\Kisma\Kisma|\Silex\Application $app
	 *
	 * @return ControllerCollection A ControllerCollection instance
	 */
	public function connect( Application $app )
	{
		$_tag = $this->_tag;
		$_defaultRoute = null;
		$_controllers = new \Silex\ControllerCollection();
		$_actions = $this->_discoverActions();

		//	Set the controller into the app
		$app[$_tag] = $this->setApp( $app );

		//	Set up a route for each discovered action...
		foreach ( $_actions as $_action => $_method )
		{
			//	Build the route, along with default if specified...
			$_route = ( '/' != $_action ? '/' . $_action . '/' : '/' );

			$_controllers->match( $_route,
				function ( Application $app, Request $request ) use ( $_action, $_method, $_tag )
				{
					$_event = new \Kisma\Event\ControllerEvent( $app[$_tag] );

					$app[$_tag]->setIsPost(
						( \Kisma\HttpMethod::Post == $request->getMethod() )
					)->dispatch( \Kisma\Event\ControllerEvent::BeforeAction, $_event );

					$_event->setResult(
						$_result = call_user_func( array( $app[$_tag], $_method ), $app, $request )
					);

					$app[$_tag]->dispatch(
						\Kisma\Event\ControllerEvent::AfterAction,
						$_event
					);

					return $_result;
				}
			);
		}

		//	Return the collection...
		return $_controllers;
	}

	/**
	 * @return array
	 */
	protected function _discoverActions()
	{
		if ( null !== $this->_actions )
		{
			return $this->_actions;
		}

		$_actions = array();
		$_mirror = new \ReflectionClass( $this );

		foreach ( $_mirror->getMethods( \ReflectionMethod::IS_PUBLIC ) as $_method )
		{
			if ( 'action' == strtolower( substr( $_method->name,
				strlen( $_method->name ) - 6,
				6 ) ) && 'on' != strtolower( substr( $_method->name, 0, 2 ) )
			)
			{
				$_routeName =
					lcfirst( \Kisma\Utility\Inflector::camelize( str_ireplace( 'Action', null, $_method->name ) ) );

				$_actions[$_routeName] = $_method->name;

				//	Add a default action/route to the discovered list if wanted
				if ( !empty( $this->_defaultAction ) && 0 == strcasecmp( $this->_defaultAction, $_routeName ) )
				{
					$_actions['/'] = $_method->name;
				}
			}
		}

		$this->setControllerName(
			lcfirst(
				\Kisma\Utility\Inflector::camelize(
					str_ireplace( array( 'ControllerProvider', 'Controller' ), null, $_mirror->getShortName() )
				)
			)
		);

		return $this->_actions = $_actions;
	}

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Events\SeedEvent $event
	 *
	 * @return bool
	 */
	public function onAfterConstruct( \Kisma\Core\Events\SeedEvent $event )
	{
		$this->_discoverActions();

		$this->_app = K::app();
		$this->setTag( 'controller.' . $this->_controllerName );
		$this->_tag = 'controller.' . $this->_controllerName;
		return parent::onAfterConstruct( $event );
	}

	/**
	 * @param \Kisma\Event\ControllerEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeAction( \Kisma\Event\ControllerEvent $event )
	{
		//	Default implementation
		return true;
	}

	/**
	 * @param \Kisma\Event\ControllerEvent $event
	 *
	 * @return bool
	 */
	public function onAfterAction( \Kisma\Event\ControllerEvent $event )
	{
		//	Default implementation
		return true;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param array $actions
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setActions( $actions )
	{
		$this->_actions = $actions;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getActions()
	{
		return $this->_actions;
	}

	/**
	 * @param string $controllerName
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setControllerName( $controllerName )
	{
		$this->_controllerName = $controllerName;
		$this->_tag = 'controller.' . $controllerName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getControllerName()
	{
		return $this->_controllerName;
	}

	/**
	 * @param array|null $routes
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setRoutes( $routes )
	{
		$this->_routes = $routes;
		return $this;
	}

	/**
	 * @return array|null
	 */
	public function getRoutes()
	{
		return $this->_routes;
	}

	/**
	 * @param boolean $isPost
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setIsPost( $isPost )
	{
		$this->_isPost = $isPost;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getIsPost()
	{
		return $this->_isPost;
	}

	/**
	 * @return boolean
	 */
	public function isPost()
	{
		return $this->_isPost;
	}

	/**
	 * @param string $defaultAction
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setDefaultAction( $defaultAction )
	{
		$this->_defaultAction = $defaultAction;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDefaultAction()
	{
		return $this->_defaultAction;
	}

	/**
	 * @param \Silex\Application $app
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setApp( $app )
	{
		$this->_app = $app;
		return $this;
	}

	/**
	 * @return \Silex\Application
	 */
	public function getApp()
	{
		return $this->_app;
	}

	/**
	 * @param string $tag
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setTag( $tag )
	{
		$this->_tag = $tag;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTag()
	{
		return $this->_tag;
	}

}