<?php
/**
 * Route.php
 */
namespace Kisma\Strata\Routing;
/**
 * Route
 * Defines a route
 */
class Route extends \Kisma\Core\Seed implements \Kisma\Strata\Interfaces\RouteLike
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var callable The handler method for this route
	 */
	private $_handler;
	/**
	 * @var \Kisma\Strata\Interfaces\DispatcherLike
	 */
	protected $_owner;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param string                                  $tag
	 * @param \Kisma\Strata\Interfaces\DispatcherLike $owner
	 * @param callable|\ReflectionMethod              $handler
	 * @param array                                   $options
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( $tag, $owner, $handler, $options = array() )
	{
		$this->setHandler( $handler );

		$this->_tag = $tag;
		$this->_owner = $owner;

		parent::__construct( $options );
	}

	/**
	 * @param \Kisma\Core\Interfaces\RequestLike $request
	 *
	 * @throws \Kisma\Core\Exceptions\InvalidEventHandlerException
	 * @return mixed
	 */
	public function process( \Kisma\Core\Interfaces\RequestLike &$request )
	{
		//	Don't process if a subscriber stops/handles/augments for us
		if ( false !== ( $_result = $this->publish( self::PreProcess, $request ) ) )
		{
			$_result = call_user_func( $this->_handler, $request );
			$this->publish( self::PostProcess, $_result );
		}

		return $_result;
	}

	/**
	 * @param callable|\ReflectionMethod $handler
	 *
	 * @throws \InvalidArgumentException
	 * @return Route
	 */
	public function setHandler( $handler )
	{
		if ( !is_callable( $handler ) && !( $handler instanceof \ReflectionMethod ) )
		{
			throw new \InvalidArgumentException( 'The argument "$handler" must be a callable or instance of \Reflection Method.' );
		}

		$this->_handler = ( $handler instanceof \ReflectionMethod ? array( $this->_owner, $handler->name ) : $handler );

		return $this;
	}

	/**
	 * @return callable|\ReflectionMethod
	 */
	public function getHandler()
	{
		return $this->_handler;
	}

	/**
	 * @param \Kisma\Strata\Interfaces\DispatcherLike $owner
	 *
	 * @return Route
	 */
	public function setOwner( $owner )
	{
		$this->_owner = $owner;

		return $this;
	}

	/**
	 * @return \Kisma\Strata\Interfaces\DispatcherLike
	 */
	public function getOwner()
	{
		return $this->_owner;
	}

}
