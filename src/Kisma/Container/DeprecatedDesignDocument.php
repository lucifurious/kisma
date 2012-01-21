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

namespace Kisma\Components;

/**
 * DesignDocument
 */
class DesignDocument implements \Doctrine\CouchDB\View\DesignDocument
{
	//*************************************************************************
	//* Public Methods 
	//*************************************************************************

	/**
	 * Get design doc code
	 *
	 * Return the view (or general design doc) code, which should be
	 * committed to the database, which should be structured like:
	 *
	 * <code>
	 *  array(
	 *	"views" => array(
	 *	  "name" => array(
	 *		  "map"	 => "code",
	 *		  ["reduce" => "code"],
	 *	  ),
	 *	  ...
	 *	)
	 *  )
	 * </code>
	 *
	 * @return array
	 */
	public function getData()
	{
		return array(
			'language' => 'javascript',
			'views' => array(
				'by_create_time' => array(
					'map' => 'function(doc) {emit(doc.create_time,null);}',
				),
			),
		);
	}
}
