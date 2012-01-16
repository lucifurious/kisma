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

namespace Kisma\Components\Widget;

//*************************************************************************
//* Aliases 
//*************************************************************************

//*************************************************************************
//* Requirements 
//*************************************************************************

/**
 * CkEditorWidget
 */
class CkEditorWidget extends \Kisma\Components\Widget
{
	//*************************************************************************
	//* Private Members 
	//*************************************************************************

	/**
	 * @var object
	 */
	protected $_widget;

	//*************************************************************************
	//* Public Methods 
	//*************************************************************************

	/**
	 * @param \Kisma\Event\RenderEvent $event
	 */
	public function onRender( $event )
	{
		$this->render( parent::onRender( $event ) );
	}

	/**
	 * @param string|null $html
	 * @param bool $returnString
	 *
	 * @return null|string
	 */
	public function render( $html = null, $returnString = false )
	{
		//	Render my html...
		$html .= \Kisma\Kisma::app()->render( 'widget/_layout_widget_ckeditor.twig',
			array(
				'widget' => array(
					'id' => $this->_id,
					'name' => $this->_name,
				),
			),
			true
		);

		if ( false !== $returnString )
		{
			return $html;
		}

		echo $html;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param object $widget
	 *
	 * @return \Kisma\Components\Widget\CkEditorWidget
	 */
	public function setWidget( $widget )
	{
		$this->_widget = $widget;
		return $this;
	}

	/**
	 * @return object
	 */
	public function getWidget()
	{
		return $this->_widget;
	}

}
