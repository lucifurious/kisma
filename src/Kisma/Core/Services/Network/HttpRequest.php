<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
 *
 * Kisma(tm) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kisma(tm) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kisma(tm).  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Kisma\Core\Services\Network;

use Kisma\Core\Enums\HttpMethod;
use Kisma\Core\Interfaces\RequestSource;
use Kisma\Core\Services\SeedRequest;
use Kisma\Core\Utility\Inflector;

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
	 * @param array         $contents
	 * @param RequestSource $source
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