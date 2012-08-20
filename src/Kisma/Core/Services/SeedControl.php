<?php
/**
 * SeedControl.php
 */
namespace Kisma\Core\Services;
/**
 * Control
 */
abstract class SeedControl extends \Kisma\Core\Service implements \Kisma\Core\Interfaces\Reactors\ControlEvent
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var \Kisma\Core\Path[] The paths I know about
	 */
	protected $_paths = array();
	/**
	 * @var array
	 */
	protected $_tasks = array();

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param string            $tag
	 * @param \Kisma\Core\Path  $path
	 */
	public function addPath( $tag, $path )
	{
		\Kisma\Core\Utility\Option::set( $this->_paths, $tag, $path );
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * @return array
	 */
	protected function _discoverTasks()
	{
		if ( null !== $this->_tasks )
		{
			return $this->_tasks;
		}

		$_tasks = array();
		$_mirror = new \ReflectionClass( $this );

		foreach ( $_mirror->getMethods( \ReflectionMethod::IS_PUBLIC ) as $_method )
		{
			if ( 'task' == strtolower( substr( $_method->name,
				strlen( $_method->name ) - 6,
				6 ) ) && 'on' != strtolower( substr( $_method->name, 0, 2 ) )
			)
			{
				$_pathName =
					lcfirst( \Kisma\Core\Utility\Inflector::camelize( str_ireplace( 'Task', null, $_method->name ) ) );
				$_tasks[$_pathName] = $_method->name;
			}
		}

		return $this->_tasks = $_tasks;
	}

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function onAfterConstruct( $event = null )
	{
		return $this->_discoverTasks();
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param \Kisma\Core\Path[] $paths
	 *
	 * @return \Kisma\Core\Services\SeedControl
	 */
	public function setPaths( $paths )
	{
		$this->_paths = $paths;
		return $this;
	}

	/**
	 * @return array|\Kisma\Core\Path[]
	 */
	public function getPaths()
	{
		return $this->_paths;
	}

	/**
	 * @param array $tasks
	 *
	 * @return \Kisma\Core\Services\SeedControl
	 */
	public function setTasks( $tasks )
	{
		$this->_tasks = $tasks;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getTasks()
	{
		return $this->_tasks;
	}

}