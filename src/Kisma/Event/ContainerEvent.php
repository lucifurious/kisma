<?php
/**
 * @file
 * A generic Kisma event class
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
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
 * KismaEvent
 * Wrapper for an event triggered within Kisma
 *
 * @property-read \Kisma\Components\Seed $target
 */
class ContainerEvent extends KismaEvent
{
	//*************************************************************************
	//* Class Constants
	//*************************************************************************

	/**
	 * @var string Triggered with the contents of the container are modified
	 */
	const ContentsModified = 'contents_modified';

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var mixed
	 */
	protected $_property;
	/**
	 * @var mixed
	 */
	protected $_value;

	/**
	 * @param mixed  $target
	 * @param string $property
	 * @param mixed  $value
	 *
	 * @internal param mixed|null $result
	 */
	public function __construct( $target, $property = null, $value = null )
	{
		parent::__construct( $target );

		$this->_property = $property;
		$this->_value = $value;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * @return mixed
	 */
	public function getProperty()
	{
		return $this->_property;
	}

}
