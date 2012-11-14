<?php
/**
 * QueueItem.php
 */
namespace Kisma\Core\Containers;
/**
 * QueueItem
 * A queue item. Nothing more than a subclass that sets some standard queue item properties
 *
 * @property-read string $_id
 * @property string      $owner_id
 * @property string      $queue_type
 * @property string      $provider_name
 * @property string      $updated
 * @property mixed       $queue_data
 * @property bool        $locked
 */
class QueueItem extends \Kisma\Core\Containers\Document
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param string $documentName
	 */
	public function __construct( $documentName = null )
	{
		array_merge(
			$this->_defaultFields,
			array(
				'queue_type',
				'owner_id',
				'provider_name',
				'queue_data',
				'updated',
				'locked',
			)
		);

		parent::__construct( $documentName );
	}
}
