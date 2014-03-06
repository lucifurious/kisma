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

use Kisma\Core\Interfaces\RequestLike;
use Kisma\Core\Interfaces\RequestSource;

/**
 * SeedCli
 * A base class for CLI services
 */
abstract class SeedCli extends SeedService implements RequestSource
{
	//********************************************************************************
	//* Members
	//********************************************************************************

	/**
	 * @var string The current working directory
	 */
	protected $_workingDirectory = null;
	/**
	 * @var RequestLike
	 */
	protected $_request = null;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
		parent::__construct( null, $options );

		//	Set working directory
		if ( null === $this->_workingDirectory )
		{
			$this->_workingDirectory = getcwd();
		}
	}

	/**
	 * Executes the command.
	 *
	 * @param array $arguments command-line parameters for this command.
	 */
	public abstract function run( $arguments = array() );

	/**
	 * Processes the command line arguments
	 *
	 * @param array $arguments
	 *
	 * @return array
	 */
	public function getArguments( $arguments = array() )
	{
		$_results = array(
			'original' => $arguments,
			'rebuilt'  => array(),
			'options'  => array(),
		);

		//	Our return options array...
		$_options = array();

		//	Rebuild args...
		for ( $_i = 0, $_count = count( $arguments ); $_i < $_count; $_i++ )
		{
			$_argument = $arguments[$_i];
			$_value = trim( substr( $_argument, 0, strpos( $_argument, '=' ) ) );

			if ( $_value && $_value[0] == '-' && $_value[1] == '-' )
			{
				$_options[substr( $_value, 2 )] = str_replace( $_value . '=', '', $_argument );
			}
			elseif ( $_value && $_value[0] == '-' )
			{
				$_options[substr( $_value, 1 )] = str_replace( $_value . '=', '', $_argument );
			}
			else
			{
				$_results['rebuilt'][] = $arguments[$_i];
			}
		}

		$_results['options'] = $_options;

		//	Return the processed results...
		return $_results;
	}

	/**
	 * Process arguments passed in
	 *
	 * @param array $arguments
	 *
	 * @return array
	 */
	protected function _processArguments( $arguments )
	{
		//	Process command line arguments
		$_className = array_shift( $arguments );
		$_options = $this->getArguments( $arguments );
		$arguments = array_merge( array( $_className ), $_options['rebuilt'] );

		//	Set our values based on options...
		$this->set( $_options );

		return $arguments;
	}

	/**
	 * @param string $workingDirectory
	 *
	 * @return \Kisma\Core\Services\SeedCli
	 */
	public function setWorkingDirectory( $workingDirectory )
	{
		$this->_workingDirectory = $workingDirectory;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getWorkingDirectory()
	{
		return $this->_workingDirectory;
	}

	/**
	 * @param RequestLike $request
	 *
	 * @return \Kisma\Core\Services\SeedCli
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
