<?php
namespace Kisma\Core\Services\Network;

use Kisma\Core\Services\SeedRequest;
use Kisma\Core\Enums\HttpMethod;
use Kisma\Core\Utility\FilterInput;
use Kisma\Core\Utility\Inflector;
use Kisma\Core\Interfaces\RequestSource;

/**
 * HttpRequest
 * Encapsulates an HTTP application request
 */
class HttpRequest extends SeedRequest
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param array           $contents
	 * @param RequestSource   $source
	 */
	public function __construct( $contents = array(), $source = null )
	{
		parent::__construct( $contents, $source );
		$this->_loadRequest();
	}

	/**
	 * @return array
	 */
	public function headers()
	{
		return $this->get( 'server.headers', array() );
	}

	/**
	 * @return \Kisma\Core\Enums\HttpMethod
	 */
	public function requestMethod()
	{
		return $this->get( 'request.request_method', false );
	}

	/**
	 * @param bool $defaultValue
	 *
	 * @return HttpMethod
	 */
	public function requestUri( $defaultValue = false )
	{
		return $this->get( 'request.request_uri', $defaultValue );
	}

	/**
	 * Load up with presents
	 */
	protected function _loadRequest()
	{
		$_goodies = array();

		//	Fill up the bag
		if ( isset( $_SERVER ) && !empty( $_SERVER ) )
		{
			foreach ( $_SERVER as $_key => $_value )
			{
				if ( false !== stripos( $_key, 'HTTP_', 0 ) )
				{
					if ( !isset( $_goodies['server.headers'] ) || !is_array( $_goodies['server.headers'] ) )
					{
						$_goodies['server.headers'] = array();
					}

					$_tag = Inflector::tag( $_key, true, 'HTTP_' );
					$_goodies['server.headers'][$_tag] = $_value;
				}
				else
				{
					$_tag = 'server.' . Inflector::tag( $_key, true );
					$_goodies[$_tag] = $_value;
				}
			}
		}

		if ( isset( $_REQUEST ) && !empty( $_REQUEST ) )
		{
			foreach ( $_REQUEST as $_key => $_value )
			{
				$_tag = 'request.' . Inflector::tag( $_key, true, 'REQUEST_' );
				$_goodies[$_tag] = $_value;
			}
		}

		$this->merge( $_goodies );

		return true;
	}

}