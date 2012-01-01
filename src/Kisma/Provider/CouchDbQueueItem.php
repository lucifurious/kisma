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
class CouchDbQueueItem extends \Kisma\Components\Document
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
		if ( null !== ( $this->_document->_id = K::o( $options, '_id', null, true ) ) )
		{
			if ( null !== ( $_rev = K::o( $options, '_rev', null, true ) ) )
			{
				$this->_document->_rev = $_rev;
			}
		}
		else
		{
			$this->setDocument( K::o( $options, 'document', null, true ) );
		}

		$this->_document->create_time = K::o( $options, 'create_time', microtime( true ), true );
		$this->_document->expire_time = K::o( $options, 'expire_time', -1, true );
		$this->_document->update_time = K::o( $options, 'update_time', null, true );
		$this->_document->feed_data = K::o( $options, 'feed_data', null, true );

		parent::__construct( $options );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param $create_time
	 *
	 * @return CouchDbQueueItem
	 */
	public function setCreateTime( $create_time )
	{
		$this->create_time = $create_time ? : microtime( true );
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCreateTime()
	{
		return $this->create_time;
	}

	/**
	 * @param $feedData
	 *
	 * @return CouchDbQueueItem
	 */
	public function setFeedData( $feedData = null )
	{
		$this->_document->feed_data = $feedData;
		return $this;
	}

	/**
	 * @return \stdClass
	 */
	public function getFeedData()
	{
		return $this->_document->feed_data;
	}

	/**
	 * @param $expire_time
	 *
	 * @return CouchDbQueueItem
	 */
	public function setExpireTime( $expire_time = -1 )
	{
		$this->expire_time = $expire_time;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getExpireTime()
	{
		return $this->expire_time;
	}

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
