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
namespace Kisma\Core\Utility;

/**
 * Renderer
 * View renderer for Twig
 */
class Render
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var array The base view path(s)
	 */
	protected static $_viewPaths = array();
	/**
	 * @var \Twig_Loader_Filesystem
	 */
	protected static $_twigLoader;
	/**
	 * @var \Twig_Environment
	 */
	protected static $_twigEnvironment;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Adds a path to the view path
	 *
	 * @param string $path
	 *
	 * @return array
	 */
	public static function addViewPath( $path )
	{
		self::$_viewPaths = array_merge(
			Option::clean( self::$_viewPaths ),
			array( $path )
		);

		self::$_twigLoader = new \Twig_Loader_Filesystem( self::$_viewPaths );
		self::$_twigEnvironment = new \Twig_Environment( self::$_twigLoader );
	}

	/**
	 * Renders a Twig template view
	 *
	 * @param string $viewFile The name of the view file or view tag to render
	 * @param array  $payload  The data to pass to the view
	 * @param bool   $returnString
	 *
	 * @return string
	 */
	public static function twigView( $viewFile, $payload = array(), $returnString = false )
	{
		if ( null !== self::$_twigEnvironment )
		{
			$_output = self::$_twigEnvironment->render(
				$viewFile,
				self::_getBaseRenderPayload( $viewFile, $payload )
			);

			if ( false !== $returnString )
			{
				return $_output;
			}

			echo $_output;
		}
	}

	/**
	 * Returns an array of standard values passed to all views
	 *
	 * @param null  $viewFile
	 * @param array $additional
	 *
	 * @return array
	 */
	protected static function _getBaseRenderPayload( $viewFile = null, $additional = array() )
	{
		$additional = array_merge(
			$additional,
			\Kisma::get( 'view.defaults', array() )
		);

		if ( null !== $viewFile )
		{
			$additional = array_merge(
				$additional,
				\Kisma::get( 'view.config.' . $viewFile, array() )
			);
		}

		$_payload = array(
			'app_name'    => \Kisma::get( 'app.name' ),
			'app_root'    => \Kisma::get( 'app.root' ),
			'app_version' => \Kisma::get( 'app.version' ),
			'page_date'   => date( 'Y-m-d H:i:s' ),
			'vendor_path' => \Kisma::get( 'app.base_path' ) . '/vendor',
			'navbar'      => \Kisma::get( 'app.navbar' ),
		);

		return array_merge( $_payload, $additional );
	}

	/**
	 * @param \Twig_Environment $twigEnvironment
	 */
	public static function setTwigEnvironment( $twigEnvironment )
	{
		self::$_twigEnvironment = $twigEnvironment;
	}

	/**
	 * @return \Twig_Environment
	 */
	public static function getTwigEnvironment()
	{
		return self::$_twigEnvironment;
	}

	/**
	 * @param \Twig_Loader_Filesystem $twigLoader
	 */
	public static function setTwigLoader( $twigLoader )
	{
		self::$_twigLoader = $twigLoader;
	}

	/**
	 * @return \Twig_Loader_Filesystem
	 */
	public static function getTwigLoader()
	{
		return self::$_twigLoader;
	}

	/**
	 * @param array $viewPaths
	 */
	public static function setViewPaths( $viewPaths )
	{
		self::$_viewPaths = $viewPaths;
	}

	/**
	 * @return array
	 */
	public static function getViewPaths()
	{
		return self::$_viewPaths;
	}
}

/**
 * Energize!
 */
Render::addViewPath( dirname( __DIR__ ) . '/Views' );
