<?php
/**
 * @file
 *            Provides ...
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license   http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author    Jerry Ablan <kisma@pogostick.com>
 * @category  Framework
 * @package   kisma
 * @since     1.0.0
 *
 * @ingroup   framework
 */

namespace Kisma;

//*************************************************************************
//* Aliases
//*************************************************************************

//*************************************************************************
//* Requirements
//*************************************************************************

/**
 * Twig
 */
class Twig
{
	//*************************************************************************
	//* Class Constants
	//*************************************************************************

	//*************************************************************************
	//* Private Members
	//*************************************************************************

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
	 * Renders a Twig template view
	 *
	 * @param string $viewFile The name of the view file or view tag to render
	 * @param array  $payload  The data to pass to the view
	 * @param bool   $returnString
	 *
	 * @return string
	 */
	public function render( $viewFile, $payload = array(), $returnString = false )
	{
		if ( !isset( self::$_app['twig'] ) )
		{
			//	No twig? No go...
			return;
		}

		$_payload = $this->_getBaseRenderPayload( $viewFile, $payload );
		$_renderEvent = new \Kisma\Event\RenderEvent( self::$_app, $viewFile, $_payload );
		$this->dispatch( Event\RenderEvent::BeforeRender, $_renderEvent );

		$_renderEvent->setOutput(
			$_output = self::$_app['twig']->render( $viewFile, $_payload )
		);

		$this->dispatch( Event\RenderEvent::AfterRender, $_renderEvent );

		if ( false !== $returnString )
		{
			return $_output;
		}

		echo $_output;
	}

}
