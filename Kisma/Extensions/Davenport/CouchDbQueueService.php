<?php
/**
 * CouchDbQueueService.php
 * Davenport : A Kisma(tm) Extension (http://github.com/Pogostick/kisma/)
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
	/**
	 * CouchDbQueueService
	 * Generic queue handling class
	 *
	 * @property string $queueName
	 */
	class CouchDbQueueService extends \Kisma\Aspects\Storage\CouchDb
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var string
		 */
		protected $_queueName = null;

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * Checks if a queue is defined or not
		 * @param string $name
		 * @return bool
		 */
		public function queueExists( $name )
		{
			$_dbName = \K::untag( $name );

			try
			{
				$this->_sag->head( $_dbName );
				$this->_sag->setDatabase( $_dbName, false );

				return true;
			}
			catch ( \SagCouchException $_ex )
			{
				if ( 404 == $_ex->getCode() )
				{
					//	Not found
					return false;
				}

				//	Something else...
				throw $_ex;
			}
		}

		/**
		 * Creates a new queue with the specified name.
		 * @param string $name
		 * @return Queue
		 */
		public function createQueue( $name )
		{
			$_dbName = \K::untag( $name );

			if ( $this->queueExists( $name ) )
			{
				return false;
			}

			$this->_sag->setDatabase( $_dbName );
			$this->_createDesignDocument();

			//	Create and return a new queue
			return new Queue(
				array(
					'queueName' => $_dbName,
					'queueService' => $this,
				)
			);
		}

		//*************************************************************************
		//* Private Methods
		//*************************************************************************

		/**
		 * Creates our design document
		 * @return bool
		 */
		protected function _createDesignDocument()
		{
			//	Build the design document
			$_doc = new \stdClass();
			$_doc->_id = Queue::DesignDocumentName;

			$_doc->views = new \stdClass();
			$_doc->views->pending = new \stdClass();
			$_doc->views->pending->map = 'function(doc) { if (!doc.lock) emit(doc.create_time,null); }';
			$_doc->views->locked = new \stdClass();
			$_doc->views->locked->map = 'function(doc) { if (doc.lock) emit(doc.lock.lock_time,null); }';

			try
			{
				//	Store it
				$this->_sag->put( $_doc->_id, $_doc );
			}
			catch ( \Exception $_ex )
			{
				/**
				 * Conflict-o-rama!
				 */
				if ( 409 == $_ex->getCode() )
				{
					//	I guess we don't care...
					return true;
				}
			}

			return false;
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param string $queueName
		 * @return $this
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

	}
}