<?php
/**
 * @file
 * CouchDb Queue Item Object
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Silex
 * @package kisma.provider
 * @since 1.0.0
 *
 * @ingroup silex
 */

namespace Kisma\Provider;

use Kisma\K;
use Kisma\Utility;

/**
 * CouchDbQueueItem
 * A queue item. Nothing more than a subclass that sets some standard queue item properties
 *
 * @property int $create_time
 * @property int $expire_time
 * @property mixed $feedData
 * @property mixed $lock
 */
class CouchDbQueueItem extends \Kisma\Container\CouchDbDocument
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array $options
	 *
	 * @return \Kisma\Provider\CouchDbQueueItem
	 */
	public function __construct( $options = array() )
	{
		//	Set our default fields
		if ( null !== ( $_id = Option::o( $options, '_id', null, true ) ) )
		{
			$this->setId( $_id );

			if ( null !== ( $_rev = Option::o( $options, '_rev', null, true ) ) )
			{
				$this->setRev( $_rev );
			}
		}
		else
		{
			$this->setFields( Option::o( $options, 'document', null, true ) );
		}

		$this->create_time = Option::o( $options, 'create_time', date( 'c' ), true );
		$this->expire_time = Option::o( $options, 'expire_time', -1, true );
		$this->update_time = Option::o( $options, 'update_time', null, true );

		$this->feed_data = Option::o( $options, 'feed_data', null, true );

		parent::__construct( $options );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param $lock
	 *
	 * @return CouchDbQueueItem
	 */
	public function setLock( $lock = null )
	{
		$this->lock = $lock;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLock()
	{
		return $this->lock;
	}

}
