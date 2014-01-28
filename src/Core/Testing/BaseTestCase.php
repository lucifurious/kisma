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
namespace Kisma\Core\Testing;

/**
 * Testing base class
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
	//*************************************************************************
	//	Members
	//*************************************************************************

	/**
	 * @var array
	 */
	protected $_firedEvents = array();

	//*************************************************************************
	//	Methods
	//*************************************************************************

	/**
	 * @param string $name
	 * @param array  $data
	 * @param string $dataName
	 */
	public function __construct( $name = null, array $data = array(), $dataName = '' )
	{
		$this->_firedEvents = array();

		parent::__construct( $name, $data, $dataName );
	}

	/**
	 * @param string $which
	 *
	 * @return $this
	 */
	public function eventFired( $which )
	{
		if ( !isset( $this->_firedEvents[$which] ) )
		{
			$this->_firedEvents[$which] = 0;
		}

		$this->_firedEvents[$which] += 1;

		return $this;
	}

	/**
	 * @param string $which
	 *
	 * @return bool|int
	 */
	public function wasFired( $which )
	{
		return isset( $this->_firedEvents[$which] ) ? $this->_firedEvents[$which] : false;
	}

	/**
	 * @param array $firedEvents
	 *
	 * @return BaseTestCase
	 */
	public function setFiredEvents( $firedEvents )
	{
		$this->_firedEvents = $firedEvents;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getFiredEvents()
	{
		return $this->_firedEvents;
	}

}
