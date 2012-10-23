<?php
/**
 * Dispatcher.php
 */
namespace Kisma\Strata\Routing;
use \Kisma\Core\Utility\Inflector;

/**
 * Dispatcher
 * A base class for controller-like functionality.
 */
class Dispatcher extends \Kisma\Core\Seed implements \Kisma\Strata\Interfaces\DispatcherLike
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var bool If true, routes will be discovered and used automatically if the prefix matches {@see Dispatcher::$autoRoutePrefix}
	 */
	protected $_autoRoute = true;
	/**
	 * @var string The prefix to look for in method names to perform auto-routing.
	 */
	protected $_autoRoutePrefix = 'route';
	/**
	 * @var \Kisma\Strata\Interfaces\ModuleLike
	 */
	protected $_owner;
	/**
	 * @var Route[] The routes for this controller
	 */
	protected $_routes = array();

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param string                              $tag
	 * @param \Kisma\Strata\Interfaces\ModuleLike $owner
	 * @param array                               $options
	 */
	public function __construct( $tag, $owner = null, $options = array() )
	{
		$this->_tag = $tag;
		$this->_owner = $owner;

		parent::__construct( $options );

		if ( false !== $this->_autoRoute && !empty( $this->_autoRoutePrefix ) )
		{
			$this->_discoverRoutes();
		}
	}

	/**
	 * Easy-peasy little request dispatcher
	 *
	 * Subclassing, overriding this method, or handling the events will let you customize it
	 * to dispatch any thing (that implements {@see \Kisma\Strata\Interfaces\RequestLike})
	 * to any thing (that implements {@see \Kisma\Strata\Interfaces\RouteLike})
	 *
	 * @param string     $tag
	 * @param mixed|null $payload
	 *
	 * @return bool|int|mixed
	 * @throws \Kisma\Core\Exceptions\UnknownRouteException
	 */
	public function dispatchRequest( $tag, $payload = null )
	{
		/** @var $_route Route */
		if ( null === ( $_route = \Kisma\Core\Utility\Option::get( $this->_routes, $tag ) ) )
		{
			throw new \Kisma\Core\Exceptions\UnknownRouteException( 'The route "' . $tag . '" was not found.' );
		}

		$_request = new Request( $tag, $payload );

		if ( false === $this->publish( self::PreProcess, $_request ) )
		{
			return false;
		}

		$_result = $_route->processRequest( $_request );

		$this->publish( self::PostProcess, $_result );

		return $_request->setResult( $_result );
	}

	/**
	 * @param string                     $tag
	 * @param callable|\ReflectionMethod $handler
	 * @param bool                       $overwrite
	 *
	 * @throws \Kisma\Strata\Exceptions\DuplicateRouteException
	 * @return \Kisma\Strata\Routing\Route The newly created route object
	 */
	public function addRoute( $tag, $handler, $overwrite = true )
	{
		$_key = Inflector::tag( $tag, true );

		if ( isset( $this->_routes[$_key] ) && false === $overwrite )
		{
			throw new \Kisma\Strata\Exceptions\DuplicateRouteException( 'Duplicate route "' . $tag . '" found. Adding route failed.' );
		}

		return $this->_routes[$_key] = new Route( $_key, $this, $handler );
	}

	/**
	 * @param string $tag
	 *
	 * @return \Kisma\Strata\Routing\Route|null The route that was removed or null if not found
	 */
	public function removeRoute( $tag )
	{
		return \Kisma\Core\Utility\Option::remove( $this->_routes, $tag );
	}

	/**
	 * @param boolean $autoRoute
	 *
	 * @return Dispatcher
	 */
	public function setAutoRoute( $autoRoute )
	{
		$this->_autoRoute = $autoRoute;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getAutoRoute()
	{
		return $this->_autoRoute;
	}

	/**
	 * @param string $autoRoutePrefix
	 *
	 * @return Dispatcher
	 */
	public function setAutoRoutePrefix( $autoRoutePrefix )
	{
		$this->_autoRoutePrefix = $autoRoutePrefix;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getAutoRoutePrefix()
	{
		return $this->_autoRoutePrefix;
	}

	/**
	 * @return \Kisma\Strata\Interfaces\ModuleLike
	 */
	public function getOwner()
	{
		return $this->_owner;
	}

	/**
	 * @return array|Route[]
	 */
	public function getRoutes()
	{
		return $this->_routes;
	}

	/**
	 * @param string $prefix If specified, used to discover routes instead of $this->_autoRoutePrefix
	 *
	 * @return array The discovered routes
	 */
	protected function _discoverRoutes( $prefix = null )
	{
		$_prefix = strtolower( trim( $prefix ? : $this->_autoRoutePrefix ) );
		$_prefixLength = strlen( $_prefix );

		$_routes = array();
		$_mirror = new \ReflectionClass( $this );

		foreach ( $_mirror->getMethods( \ReflectionMethod::IS_PUBLIC ) as $_method )
		{
			$_name = $_method->name;
			$_prefixCheck = strtolower( substr( $_name, strlen( $_name ) - $_prefixLength, $_prefixLength ) );

			if ( $_prefix == $_prefixCheck )
			{
				$_routeTag = Inflector::tag(
					lcfirst( Inflector::camelize( substr( $_name, $_prefixLength ) ) ),
					true
				);

				$_routes[$_routeTag] = new Route( $_routeTag, $this, $_method );
			}
		}

		return $this->_routes = $_routes;
	}

}
