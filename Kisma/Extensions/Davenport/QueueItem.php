<?php
/**
 * QueueItem.php
 * Davenport : An extension for Kisma(tm) (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link		  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license	   http://github.com/Pogostick/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @category	  Kisma_Extensions
 * @package	   kisma.extensions.davenport
 * @since		 v1.0.0
 * @filesource
 */
namespace Kisma\Extensions\Davenport
{
	//*************************************************************************
	//* Requirements 
	//*************************************************************************

	/**
	 * QueueItem
	 * A queue item. Nothing more than a subclass that sets some standard queue item properties
	 *
	 * @property int $create_time
	 * @property int $expire_time
	 * @property mixed $feedData
	 * @property mixed $lock
	 */
	class QueueItem extends \Kisma\Components\Document
	{
		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * @param array $options
		 * @return \Kisma\Extensions\Davenport\QueueItem
		 */
		public function __construct( $options = array() )
		{
			//	Set our default fields
			if ( null !== ( $this->_document->_id = \K::o( $options, '_id', null, true ) ) )
			{
				if ( null !== ( $_rev = \K::o( $options, '_rev', null, true ) ) )
				{
					$this->_document->_rev = $_rev;
				}
			}
			else
			{
				$this->setDocument( \K::o( $options, 'document', null, true ) );
			}

			$this->_document->create_time = \K::o( $options, 'create_time', microtime( true ), true );
			$this->_document->expire_time = \K::o( $options, 'expire_time', -1, true );
			$this->_document->create_time = \K::o( $options, 'update_time', null, true );

			$_feedData = \K::o( $options, 'feed_data', null, true );

			if ( null !== $_feedData )
			{
				//	Turn off cleansing for me...
				if ( is_array( $_feedData ) )
				{
					$_feedData[\KismaOptions::CleanOptions] = false;
				}
			}

			$this->_document->feed_data = $_feedData;

			parent::__construct( $options );
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param $create_time
		 * @return QueueItem
		 */
		public function setCreateTime( $create_time )
		{
			$this->create_time = $create_time ?: microtime( true );
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
		 * @return QueueItem
		 */
		public function setFeedData( $feedData = null )
		{
			$this->feed_data = $feedData;
			return $this;
		}

		/**
		 * @return \stdClass
		 */
		public function getFeedData()
		{
			return $this->feed_data;
		}

		/**
		 * @param $expire_time
		 * @return QueueItem
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
		 * @return QueueItem
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
}