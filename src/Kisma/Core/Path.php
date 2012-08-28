<?php
/**
 * Route.php
 */
namespace Kisma\Core;

/**
 * Route
 * A route defines a path that an inbound request takes on its journey through the application.
 */
abstract class Route extends Seed implements \Kisma\Core\Interfaces\Events\Route
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Kisma\Core\Services\SeedRequest The inbound request
	 */
	protected $_request = null;
	/**
	 * @var array The request handlers
	 */
	protected $_handlers = array();

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param mixed $request
	 * @param array $handler
	 *
	 * @return void
	 */
	public function addHandler( $request, $handler )
	{
		\Kisma\Core\Utility\Option::set( $this->_handlers, $request, $handler );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param array $handlers
	 *
	 * @return \Kisma\Core\Route
	 */
	public function setHandlers( $handlers )
	{
		$this->_handlers = $handlers;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getHandlers()
	{
		return $this->_handlers;
	}

	/**
	 * @param mixed $request
	 *
	 * @return \Kisma\Core\Route
	 */
	public function setRequest( $request )
	{
		$this->_request = $request;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRequest()
	{
		return $this->_request;
	}

}