<?php
/**
 * Cli.php
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2012 Jerry Ablan
 * @license   http://github.com/lucifurious/kisma/blob/master/LICENSE
 * @author    Jerry Ablan <get.kisma@gmail.com>
 */
namespace Kisma\Core\Services;

/**
 * Cli
 * A base class for CLI services
 */
abstract class Cli extends \Kisma\Core\Service
{
	//********************************************************************************
	//* Member Variables
	//********************************************************************************

	/**
	 * @var string The current working directory
	 */
	protected $_workingDirectory = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
		parent::__construct( $options );

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

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param string $workingDirectory
	 *
	 * @return Cli
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

}
