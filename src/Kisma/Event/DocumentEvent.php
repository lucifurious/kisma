<?php
/**
 * @file
 * A document event class
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
 * DocumentEvent
 * Contains the events triggered by a document
 */
class DocumentEvent extends ModelEvent
{
	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @return \Kisma\Container\CouchDb\Document
	 */
	public function getDocument()
	{
		return $this->getTarget();
	}

	/**
	 * @return mixed
	 */
	public function getResponse()
	{
		return $this->_response;
	}

}
