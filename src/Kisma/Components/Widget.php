<?php
/**
 * @file
 * Provides ...
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Framework
 * @package kisma
 * @since 1.0.0
 *
 * @ingroup framework
 */

namespace Kisma\Components;

//*************************************************************************
//* Aliases
//*************************************************************************

//*************************************************************************
//* Requirements
//*************************************************************************

/**
 * Widget
 */
class Widget extends Seed implements \Kisma\IWidgetService
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string
	 */
	protected $_id;
	/**
	 * @var string
	 */
	protected $_name;
	/**
	 * @var array
	 */
	protected $_attributes;
	/**
	 * @var string
	 */
	protected $_html;
	/**
	 * @var array
	 */
	protected $_scripts = array();
	/**
	 * @var array
	 */
	protected $_stylesheets = array();

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param \Kisma\Event\RenderEvent $event
	 *
	 * @return string
	 */
	public function onRender( $event )
	{
		//	spit out something
		echo <<<HTML
<div>
	<H1>HI!</H1>
	<h2>I'm a widget</h2>
</div>
HTML;

	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param array $attributes
	 *
	 * @return \Kisma\Components\Widget
	 */
	public function setAttributes( $attributes )
	{
		$this->_attributes = $attributes;
		return $this;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return \Kisma\Components\Widget
	 */
	public function setAttribute( $key, $value )
	{
		\Kisma\Utility\Option::set( $this->_attributes, $key, $value );
		return $this;
	}

	/**
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}

	/**
	 * @param string $key
	 * @param mixed  $defaultValue
	 *
	 * @return mixed
	 */
	public function getAttribute( $key, $defaultValue = null )
	{
		return \Kisma\Utility\Option::get( $this->_attributes, $key, $defaultValue );
	}

	/**
	 * @param string $html
	 *
	 * @return \Kisma\Components\Widget
	 */
	public function setHtml( $html )
	{
		$this->_html = $html;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getHtml()
	{
		return $this->_html;
	}

	/**
	 * @param string $id
	 *
	 * @return \Kisma\Components\Widget
	 */
	public function setId( $id )
	{
		$this->_id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param string $name
	 *
	 * @return \Kisma\Components\Widget
	 * @return \Kisma\Components\Widget
	 */
	public function setName( $name )
	{
		$this->_name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param array $scripts
	 *
	 * @return \Kisma\Components\Widget
	 */
	public function setScripts( $scripts )
	{
		$this->_scripts = $scripts;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getScripts()
	{
		return $this->_scripts;
	}

	/**
	 * @param array $stylesheets
	 *
	 * @return \Kisma\Components\Widget
	 */
	public function setStylesheets( $stylesheets )
	{
		$this->_stylesheets = $stylesheets;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getStylesheets()
	{
		return $this->_stylesheets;
	}
}
