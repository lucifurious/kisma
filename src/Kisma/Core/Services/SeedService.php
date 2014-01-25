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
namespace Kisma\Core\Services;

use Kisma\Core\Interfaces\ConsumerLike;
use Kisma\Core\Interfaces\RequestLike;
use Kisma\Core\Interfaces\ServiceLike;
use Kisma\Core\Interfaces\ServiceState;
use Kisma\Core\SeedBag;

/**
 * SeedService
 * The base class for services provided
 *
 * Provides three event handlers:
 *
 * @event onSuccess Raised after a success
 * @event onFailure Raised after a failure
 * @event onComplete Raised upon completion of a service call
 */
abstract class SeedService extends SeedBag implements ServiceLike, ServiceState
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var ConsumerLike
	 */
	protected $_consumer = null;
	/**
	 * @var bool|int The current state of the service
	 */
	protected $_state = self::Uninitialized;
	/**
	 * @var RequestLike
	 */
	protected $_request = null;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Create the service
	 *
	 * @param ConsumerLike $consumer
	 * @param array        $settings
	 */
	public function __construct( ConsumerLike $consumer, $settings = array() )
	{
		parent::__construct( $settings );

		$this->_consumer = $consumer;
		$this->_state = self::Initialized;
	}

	/**
	 * Default implementation
	 */
	public function perform()
	{
		//	Service complete
		$this->publish( self::Complete );
	}

	/**
	 * @param ConsumerLike $consumer
	 *
	 * @return \Kisma\Core\Services\SeedService
	 */
	public function setConsumer( $consumer )
	{
		$this->_consumer = $consumer;

		return $this;
	}

	/**
	 * @return ConsumerLike
	 */
	public function getConsumer()
	{
		return $this->_consumer;
	}

	/**
	 * @param bool|int $state
	 *
	 * @return \Kisma\Core\Services\SeedService
	 */
	public function setState( $state )
	{
		$this->_state = $state;

		return $this;
	}

	/**
	 * @return bool|int
	 */
	public function getState()
	{
		return $this->_state;
	}

	/**
	 * @param RequestLike $request
	 *
	 * @return \Kisma\Core\Services\SeedService
	 */
	public function setRequest( $request )
	{
		$this->_request = $request;

		return $this;
	}

	/**
	 * @return RequestLike
	 */
	public function getRequest()
	{
		return $this->_request;
	}
}
