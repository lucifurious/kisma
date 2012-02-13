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

/**
 * QueueItem
 * A queue item. Nothing more than a subclass that sets some standard queue item properties
 *
 * @Document
 *
 * @property mixed $queueData
 * @property mixed $locked
 */
class QueueItem extends \Kisma\Container\CouchDb\Document
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The document name
	 */
	const DocumentName = 'Kisma\\Container\\CouchDb\\QueueItem';

	//*************************************************************************
	//* Document Fields
	//*************************************************************************

	/**
	 * @Field(type="mixed")
	 */
	public $queueData = null;
	/**
	 * @Field(type="boolean")
	 */
	public $locked = false;
	/**
	 * @Index
	 * @Field(type="string")
	 * @var string|null
	 */
	public $ownerId = null;
	/**
	 * @Index
	 * @Field(type="string")
	 * @var string|null
	 */
	public $providerName = null;
	/**
	 * @var \DateTime
	 * @Field(type="datetime")
	 */
	public $updated = null;

}
