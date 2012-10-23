<?php
/**
 * SeedRequest.php
 */
namespace Kisma\Strata\Routing;
use Kisma\Core\Utility\Option;

/**
 * SeedRequest
 * A base class for all service requests
 */
class Request extends \Kisma\Core\SeedBag implements \Kisma\Strata\Interfaces\RequestLike, \Kisma\Core\Interfaces\RequestSource
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var int The source of this request
	 */
	protected $_source = self::Http;
	/**
	 * @var mixed The data of the request
	 */
	protected $_payload = null;
	/**
	 * @var string Anything in that came in on the php://input stream
	 */
	protected $_raw = null;
	/**
	 * @var mixed The result of the request, if any
	 */
	protected $_result = null;
	/**
	 * @var string
	 */
	protected $_method = null;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function __construct( $tag, $payload = null, $options = array() )
	{
		$this->_tag = $tag;
		$this->_payload = $payload;

		parent::__construct( $options );
	}

	/**
	 * Pull some stuff from the environment/system
	 */
	protected function _loadRequest()
	{
		$this->_payload = array();

		//	Command line request...
		if ( isset( $_SERVER['argc'] ) )
		{
			$this->_source = self::Cli;
			$_values = Option::get( $_SERVER, 'argv', array() );
		}
		//	HTTP request...
		elseif ( false === ( $_method = \Kisma\Core\Enums\HttpMethod::defines( $_SERVER['REQUEST_METHOD'], true ) ) )
		{
			throw new \Kisma\Core\Exceptions\InvalidRequestException( 'The server method "' . $_SERVER['REQUEST_METHOD'] . '" is not valid.' );
		}
		else
		{
			if ( \Kisma\Core\Enums\HttpMethod::Post == $_method )
			{
				$_values = Option::clean( $_POST );
			}
			elseif ( \Kisma\Core\Enums\HttpMethod::Get == $_method )
			{
				$_values = Option::clean( $_GET );
			}
			else
			{
				$_values = Option::clean( $_REQUEST );
			}
		}

		//@todo Consider payload detection based on content-type here
		$this->_payload = $_values;

		//	Save off the raw...
		$this->_raw = @file_get_contents( 'php://input' );

		return true;
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return $this->_method;
	}

	/**
	 * @param mixed $payload
	 *
	 * @return Request
	 */
	public function setPayload( $payload )
	{
		$this->_payload = $payload;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPayload()
	{
		return $this->_payload;
	}

	/**
	 * @param string $raw
	 *
	 * @return Request
	 */
	public function setRaw( $raw )
	{
		$this->_raw = $raw;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRaw()
	{
		return $this->_raw;
	}

	/**
	 * @param mixed $result
	 *
	 * @return Request
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

	/**
	 * @return int
	 */
	public function getSource()
	{
		return $this->_source;
	}

}
