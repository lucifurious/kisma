<?php
/**
 * Queue.php
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link		  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license	   http://github.com/Pogostick/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @package	   kisma.services.queue
 * @since		 v1.0.0
 * @filesource
 */
namespace Kisma\Extensions\Davenport
{
	//*************************************************************************
	//* Requirements
	//*************************************************************************

	/**
	 * Queue
	 * A pretty generic queue
	 *
	 * @property \Kisma\Components\Component $owner
	 * @property string $queueName
	 * @property \Kisma\Aspects\Storage\CouchDb $server
	 * @property string $pendingViewName
	 * @property string $lockedViewName
	 */
	class Queue extends \Kisma\Components\SubComponent
	{
		//*************************************************************************
		//* Constants
		//*************************************************************************

		/**
		 * @var string
		 */
		const DesignDocumentName = '_design/davenport';
		/**
		 * @var string
		 */
		const PendingViewName = 'pending';
		/**
		 * @var string
		 */
		const LockedViewName = 'locked';
		/**
		 * @var int
		 */
		const DefaultMaxItems = 25;

		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var string Our queue tag
		 */
		protected $_queueName = null;
		/**
		 * @var \Kisma\Extensions\Davenport\CouchDbQueueService Our queue service
		 */
		protected $_queueService = null;
		/**
		 * @var string The name of our 'pending' view
		 */
		protected $_pendingViewName = self::PendingViewName;
		/**
		 * @var string The name of our 'locked' view
		 */
		protected $_lockedViewName = self::LockedViewName;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * @param array $options
		 */
		public function __construct( $options = array() )
		{
			parent::__construct( $options );

			if ( null === $this->_keyPrefix )
			{
				$this->_keyPrefix = 'davenport.queue.item';
				$this->_queueService->setKeyPrefix( $this->_keyPrefix );
			}
		}

		/**
		 * Get work items from the queue
		 * The descending param is used to get LIFO (if true) or FIFO (if false) behavior.
		 * @param int $maxItems
		 * @param bool $fifo
		 * @internal param string $queueName
		 * @return array|false
		 */
		public function dequeue( $maxItems = self::DefaultMaxItems, $fifo = true )
		{
			\Kisma\Utility\Log::debug( 'Requesting pending view: ' . $this->_pendingViewName );

			$_pendingMessages = $this->_queueService->get(
				$this->_buildQueryUrl(
					array(
						'include_docs' => true,
						'limit' => $maxItems,
						'descending' => $fifo ? 'true' : 'false',
					),
					$this->_pendingViewName
				)
			);

			\Kisma\Utility\Log::debug( 'View results: ' . $_pendingMessages );

			$_lock = $this->_createLock();

			//	Lock each document
			foreach ( $_pendingMessages as &$_message )
			{
				$_message->lock = $_lock;
			}

			$_workItems = array();

			//	Now, try and lock them all
			foreach ( $this->_queueService->bulk( $_pendingMessages, true )->body as $_message )
			{
				if ( isset( $_message->error ) )
				{
					//	Skipping conflicts...
					continue;
				}

				//	Add successful lock to work item list
				$_workItems[] = $_message;
			}

			//	Return locked items
			return empty( $_workItems ) ? false : $_workItems;
		}

		/**
		 * Adds a work item to the queue
		 * @param string $id
		 * @param mixed $feedData Any kind of info you want to pass the dequeuer process
		 * @param int $expireTime How long to keep this guy around. -1 = until deleted@internal param int $timeToLive
		 * @return mixed The id of the message
		 */
		public function enqueue( $id = null, $feedData = null, $expireTime = -1 )
		{
			//	Create an id
			$_id = $this->_queueService->createKey( $id ?: $this->_queueName . microtime( true ) );

			$_item = new QueueItem(
				array(
					'_id' => $_id,
					'feed_data' => $feedData,
					'expire_time' => $expireTime,
				)
			);

			try
			{
				//	See if this key is already in the queue...
				if ( false !== ( $_document = $this->_queueService->documentExists( $_id, true ) ) )
				{
					//	Doc exists, read and update...
					$_document->update_time = microtime( true );
					$_document->feed_data = $feedData;
					\Kisma\Utility\Log::debug( 'Found prior queue item, _rev: ' . $_document->_rev );
				}
				else
				{
					$_document = $_item->getDocument();
				}

				$_response = $this->_queueService->put( $_id, $_document );

				if ( isset( $_response->body ) && $_response->body->ok )
				{
					\Kisma\Utility\Log::debug( 'Document queued: ' . print_r( $_response->body, true ) );
					return $_response->body->id;
				}

				return false;
			}
			catch ( \Exception $_ex )
			{
				throw new \Kisma\StorageException( $_ex );
			}
		}

		/**
		 * Create a queue lock object. Can be added to the message document to
		 * indicate that it was locked by this process.
		 * @return array
		 */
		protected function _createLock()
		{
			return array(
				'lock_time' => microtime( true ),
			);
		}

		/**
		 * Build a query url
		 * @param array $parameters
		 * @param string $viewName Optional view name to query
		 * @return string
		 */
		protected function _buildQueryUrl( $parameters = array(), $viewName = null )
		{
			$_url = null;

			if ( null !== $viewName )
			{
				$_url = self::DesignDocumentName . '/' . ( $viewName ?: $this->_queueName );
			}

			return $_url . '?' . http_build_query( $parameters );
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param string $lockedViewName
		 * @return $this
		 */
		public function setLockedViewName( $lockedViewName )
		{
			$this->_lockedViewName = $lockedViewName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getLockedViewName()
		{
			return $this->_lockedViewName;
		}

		/**
		 * @param string $pendingViewName
		 * @return $this
		 */
		public function setPendingViewName( $pendingViewName )
		{
			$this->_pendingViewName = $pendingViewName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getPendingViewName()
		{
			return $this->_pendingViewName;
		}

		/**
		 * @param string $queueName
		 * @return \Kisma\Extensions\Davenport\Queue
		 */
		public function setQueueName( $queueName )
		{
			$this->_queueName = $queueName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getQueueName()
		{
			return $this->_queueName;
		}

		/**
		 * @param \Kisma\Extensions\Davenport\CouchDbQueueService $queueService
		 * @return \Kisma\Extensions\Davenport\Queue
		 */
		public function setQueueService( $queueService )
		{
			$this->_queueService = $queueService;
			return $this;
		}

		/**
		 * @return \Kisma\Extensions\Davenport\CouchDbQueueService
		 */
		public function getQueueService()
		{
			return $this->_queueService;
		}

	}

}
