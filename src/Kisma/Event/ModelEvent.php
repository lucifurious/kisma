<?php
/**
 * @file
 * Model event class
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/lucifurious/kisma/)
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
 * ModelEvent
 * Contains the events triggered by a model
 */
class ModelEvent extends KismaEvent
{
	//*************************************************************************
	//* Class Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const AfterValidate = 'after_validate';
	/**
	 * @var string
	 */
	const BeforeValidate = 'before_validate';
	/**
	 * @var string
	 */
	const AfterFind = 'after_find';
	/**
	 * @var string
	 */
	const BeforeFind = 'before_find';
	/**
	 * @var string
	 */
	const AfterSave = 'after_save';
	/**
	 * @var string
	 */
	const BeforeSave = 'before_save';
	/**
	 * @var string
	 */
	const BeforeDelete = 'before_delete';
	/**
	 * @var string
	 */
	const AfterDelete = 'after_delete';

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var mixed
	 */
	protected $_response;

	/**
	 * @param	  $target
	 * @param null $response
	 *
	 * @internal param mixed|null $result
	 */
	public function __construct( $target, $response = null )
	{
		parent::__construct( $target );

		$this->_response = $response;
	}

	/**
	 * @return mixed
	 */
	public function getResponse()
	{
		return $this->_response;
	}

}
