<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license			http://github.com/Pogostick/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @category		Kisma_Services
 * @package			kisma.services
 * @namespace		\Kisma\Services
 * @since			v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Components Kisma components
 */
namespace Kisma\Services;

/**
 *
 */
abstract class CliService extends \Kisma\Components\Service
{
	//********************************************************************************
	//* Member Variables
	//********************************************************************************

	/**
	 * @var string The command name
	 */
	protected $_serviceName;
	/**
	 * @var string
	 */
	protected $_basePath = null;

	//*************************************************************************
	//* Public Methods 
	//*************************************************************************

	/**
	 * Executes the command.
	 * @param array $arguments command-line parameters for this command.
	 */
	public abstract function run( $arguments = array() );

	/**
	 * Processes the command line arguments
	 * @param array $arguments
	 * @return array
	 */
	public function getArguments( $arguments = array() )
	{
		$_results = array(
			'original' => $arguments,
			'rebuilt' => array(),
			'options' => array(),
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
	 * @return array
	 */
	protected function _processArguments( $arguments )
	{
		//	Process command line arguments
		$_className = array_shift( $arguments );
		$_options = $this->getArguments( $arguments );
		$arguments = array_merge( array( $_className ), $_options['rebuilt'] );

		//	Set our values based on options...
		foreach ( $_options['options'] as $_key => $_value )
		{
			switch ( strtolower( trim( $_key ) ) )
			{
				default:
					$this->__set( $_key, $_value );
					break;
			}
		}

		return $arguments;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param string $basePath
	 * @return \CliService
	 */
	public function setBasePath( $basePath = null )
	{
		$this->_basePath = $basePath;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getBasePath()
	{
		return $this->_basePath;
	}

	/**
	 * @param string $serviceName
	 * @return \CliService
	 */
	public function setServiceName( $serviceName = __CLASS__ )
	{
		$this->_serviceName = $serviceName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getServiceName()
	{
		return $this->_serviceName;
	}
}
