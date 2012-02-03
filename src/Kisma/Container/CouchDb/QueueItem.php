<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license			http://github.com/Pogostick/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @package			kisma.container.couchdb
 * @since			v1.0.0
 * @filesource
 */
namespace Kisma\Container\CouchDb;

use Kisma\K;
use Kisma\Utility;
use Doctrine\ODM\CouchDB\Mapping\Annotations\Document;
use Doctrine\Common\Annotations\Annotation;

/**
 * QueueItem
 * A queue item. Nothing more than a subclass that sets some standard queue item properties
 *
 * @Document
 * @property int $create_time
 * @property int $update_time
 * @property int $expire_time
 * @property mixed $queueData
 * @property mixed $locked
 * @property string $version
 */
class QueueItem extends \Kisma\Container\Document
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The "name" of this document
	 */
	const DocumentName = 'Kisma.Container.CouchDb.QueueItem';

	//*************************************************************************
	//* Document Fields
	//*************************************************************************

	/**
	 * @Field(type="object")
	 */
	public $queueData = null;
	/**
	 * @Field(type="boolean")
	 */
	public $locked = false;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
		//	Set our object's name and let 'er go
		$this->setDocumentName( self::DocumentName );
		parent::__construct( $options );
	}
}
