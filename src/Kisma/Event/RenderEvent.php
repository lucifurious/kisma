<?php
/**
 * @file
 * Render event class
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Events
 * @package kisma.events
 * @since 1.0.0
 *
 * @ingroup events
 */

namespace Kisma\Event;

/**
 * RenderEvent
 * Contains the events triggered by rendering objects
 */
class RenderEvent extends KismaEvent
{
	//*************************************************************************
	//* Class Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const AfterRender = 'after_render';

	/**
	 * @var string
	 */
	const BeforeRender = 'before_render';

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * @var null|string
	 */
	protected $_viewFile;
	/**
	 * @var null|string
	 */
	protected $_payload;
	/**
	 * @var null|string
	 */
	protected $_output;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param \Kisma\Components\Seed|\Silex\Application|array $target
	 * @param string|null									 $viewFile
	 * @param array|null									  $payload
	 * @param string|null									 $output
	 */
	public function __construct( $target, $viewFile = null, &$payload = null, &$output = null )
	{
		parent::__construct( $target );

		$this->_viewFile = $viewFile;
		$this->_payload = $payload;
		$this->_output = $output;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param $output
	 *
	 * @return RenderEvent
	 */
	public function setOutput( $output )
	{
		$this->_output = $output;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getOutput()
	{
		return $this->_output;
	}

	/**
	 * @param $payload
	 *
	 * @return RenderEvent
	 */
	public function setPayload( $payload )
	{
		$this->_payload = $payload;
		return $this;
	}

	/**
	 * @return array|null
	 */
	public function getPayload()
	{
		return $this->_payload;
	}

	/**
	 * @param $viewFile
	 *
	 * @return RenderEvent
	 */
	public function setViewFile( $viewFile )
	{
		$this->_viewFile = $viewFile;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getViewFile()
	{
		return $this->_viewFile;
	}

}
