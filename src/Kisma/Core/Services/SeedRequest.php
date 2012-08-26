<?php
/**
 * SeedRequest.php
 */
namespace Kisma\Core\Services;

use Kisma\Core\Utility\Option;

/**
 * SeedRequest
 * A base class for all service requests
 */
abstract class SeedRequest extends \Kisma\Core\Seed implements \Kisma\Core\Interfaces\RequestSource
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var int The source of this request
	 */
	protected $_source = self::Http;
	/**
	 * @var array Depending on the source, contains the command line arguments
	 */
	protected $_arguments = null;
	/**
	 * @var int Depending on the source, contains the count of command line arguments
	 */
	protected $_argumentCount = null;
	/**
	 * @var array
	 */
	protected $_raw = null;
	/**
	 * @var mixed The result of the request, if any
	 */
	protected $_result = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function __construct( $settings = array() )
	{
		$this->_loadRequest();
		parent::__construct( $settings );
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * Pull some stuff from the environment/system
	 */
	protected function _loadRequest()
	{
		//	Command line request...
		if ( isset( $_SERVER['argc'] ) )
		{
			$this->_source = self::Cli;
			$this->_arguments = Option::get( $_SERVER, 'argv' );
			$this->_argumentCount = Option::get( $_SERVER, 'argc' );
		}

		//	Save off the raw body
		$this->_raw = @file_get_contents( 'php://input' );

		return true;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param int $argumentCount
	 *
	 * @return SeedRequest
	 */
	public function setArgumentCount( $argumentCount )
	{
		$this->_argumentCount = $argumentCount;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getArgumentCount()
	{
		return $this->_argumentCount;
	}

	/**
	 * @param array $arguments
	 *
	 * @return SeedRequest
	 */
	public function setArguments( $arguments )
	{
		$this->_arguments = $arguments;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getArguments()
	{
		return $this->_arguments;
	}

	/**
	 * @param int $source
	 *
	 * @return SeedRequest
	 */
	public function setSource( $source )
	{
		$this->_source = $source;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getSource()
	{
		return $this->_source;
	}

	/**
	 * @param array $raw
	 *
	 * @return SeedRequest
	 */
	public function setRaw( $raw )
	{
		$this->_raw = $raw;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getRaw()
	{
		return $this->_raw;
	}

	/**
	 * @param mixed $result
	 *
	 * @return SeedRequest
	 */
	public function setResult( $result )
	{
		$this->_result = $result;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getResult()
	{
		return $this->_result;
	}

}
