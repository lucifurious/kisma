<?php
/**
 * @file
 * Widget Services
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

use Silex\Application;

/**
 * WidgetServiceProvider
 */
class WidgetServiceProvider extends SilexServiceProvider
{
	//*************************************************************************
	//* Class Constants 
	//*************************************************************************

	//*************************************************************************
	//* Private Members 
	//*************************************************************************

	/**
	 * @var array
	 */
	protected $_widgets = array();

	//*************************************************************************
	//* Public Methods 
	//*************************************************************************

	/**
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
		parent::__construct( $options );

		$this->_serviceName = 'widgets';
	}

	/**
	 * Registers services on the given app.
	 *
	 * @param \Kisma\Kisma|\Silex\Application $app An Application instance
	 */
	public function register( Application $app )
	{
		$this->_widgets = isset( $app['widgets'] ) ? $app['widgets'] : array();
		$app[$this->_serviceName] = $this;
	}

	/**
	 * @param \Kisma\Event\RenderEvent $event
	 * @return bool
	 */
	public function onRender( $event )
	{
		return true;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param array $widgets
	 * @return \Kisma\Provider\WidgetServiceProvider
	 */
	public function setWidgets( $widgets )
	{
		$this->_widgets = $widgets;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getWidgets()
	{
		return $this->_widgets;
	}

}
