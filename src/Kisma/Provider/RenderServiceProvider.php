<?php
/**
 * @file
 * Provides ...
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Framework
 * @package kisma
 * @since 1.0.0
 *
 * @ingroup framework
 */

namespace Kisma\Provider;

//*************************************************************************
//* Aliases 
//*************************************************************************

use Kisma\Kisma;
use Kisma\Utility;
use Symfony\Component\HttpKernel\Exception\HttpException;

//*************************************************************************
//* Requirements 
//*************************************************************************

/**
 * RenderServiceProvider
 */
class RenderServiceProvider extends SilexServiceProvider
{
	//*************************************************************************
	//* Class Constants 
	//*************************************************************************

	//*************************************************************************
	//* Private Members 
	//*************************************************************************

	/**
	 * @var callback[] A hash of extension to renderers
	 */
	protected $_renderMap = array();

	//*************************************************************************
	//* Public Methods 
	//*************************************************************************

	//*************************************************************************
	//* Private Methods 
	//*************************************************************************

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * Registers services on the given app.
	 *
	 * @param Application $app An Application instance
	 */
	function register( Application $app )
	{
		// TODO: Implement register() method.
	}

	/**
	 * @var array The base view path(s)
	 */
	protected $_viewPaths = array();

	/**
	 * Renders a view file
	 *
	 * @param string $view
	 * @param array  $context
	 * @param bool   $returnString If true, result of render is returned as a string.
	 *
	 * @return closure|mixed|string
	 */
	public function render( $view, array $context = array(), $returnString = false )
	{
		if ( false === ( $_viewFile = $this->_locateViewFile( $view ) ) )
		{
			throw new \Symfony\Component\HttpKernel\Exception\HttpException( 404, 'The view "' . $view . '" was not found.' );
		}

		//	Get extension and check map
		$_ext = pathinfo( $_viewFile, PATHINFO_EXTENSION );

		if ( isset( $this->_renderMap[$_ext] ) )
		{
			if ( is_callable( $this->_renderMap[$_ext] ) )
			{
				/** @var $_handler callback */
				$_handler = $this->_renderMap[$_ext];
				return $_handler( $_viewFile, $context );
			}

			throw new HttpException( 500, 'The view handler defined for extension "' . $_ext . '" is invalid.' );
		}

		return function( $_viewFile, $context, $returnString )
		{
			if ( !is_array( $context ) )
			{
				$context = array( $context );
			}

			extract( $context );

			if ( false === $returnString )
			{
				return require( $_viewFile );
			}

			ob_start();
			ob_implicit_flush( false );

			require $_viewFile;

			return ob_get_clean();
		};
	}

	/**
	 * Generates the resulting view file path.
	 *
	 * @param string $file source view file path
	 *
	 * @return string resulting view file path
	 */
	protected function _locateViewFile( $view )
	{
		if ( empty( $viewName ) )
				{
					return false;
				}

		if ( $moduleViewPath === null )
			$moduleViewPath = $basePath;

		if ( ( $renderer = Yii::app()->getViewRenderer() ) !== null )
			$extension = $renderer->fileExtension;
		else
			$extension = '.php';
		if ( $viewName[0] === '/' )
		{
			if ( strncmp( $viewName, '//', 2 ) === 0 )
				$viewFile = $basePath . $viewName;
			else
				$viewFile = $moduleViewPath . $viewName;
		}
		else if ( strpos( $viewName, '.' ) )
			$viewFile = Yii::getPathOfAlias( $viewName );
		else
			$viewFile = $viewPath . DIRECTORY_SEPARATOR . $viewName;

		if ( is_file( $viewFile . $extension ) )
			return Yii::app()->findLocalizedFile( $viewFile . $extension );
		else if ( $extension !== '.php' && is_file( $viewFile . '.php' ) )
			return Yii::app()->findLocalizedFile( $viewFile . '.php' );
		else
			return false;
	}

	/**
	 * @param array $renderMap
	 *
	 * @return \Kisma\Provider\RenderServiceProvider
	 */
	public
	function setRenderMap( $renderMap )
	{
		$this->_renderMap = $renderMap;
		return $this;
	}

	/**
	 * Adds a single render mapping
	 *
	 * @param string  $extension
	 * @param closure $handler
	 *
	 * @return RenderServiceProvider
	 */
	public
	function addRenderMapping( $extension, $handler )
	{
		$this->_renderMap[$extension] = $handler;
		return $this;
	}

	/**
	 * @return array
	 */
	public
	function getRenderMap()
	{
		return $this->_renderMap;
	}

	/**
	 * @param array $viewPaths
	 *
	 * @return \Kisma\Provider\RenderServiceProvider
	 */
	public
	function setViewPaths( $viewPaths )
	{
		$this->_viewPaths = $viewPaths;
		return $this;
	}

	/**
	 * @return array
	 */
	public
	function getViewPaths()
	{
		return $this->_viewPaths;
	}

}
