<?php
/**
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/lucifurious/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2009-2012, Jerry Ablan, All Rights Reserved
 * @link			http://github.com/lucifurious/kisma/ Kisma(tm)
 * @license			http://github.com/lucifurious/kisma/licensing/
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
 * @Document @Index
 *
 * @property string $ownerId
 * @property string $accountId
 * @property string $providerName
 * @property string $updated
 * @property mixed $queueData
 * @property bool $locked
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
	 * @Field @Index
	 * @var string|null
	 */
	public $ownerId = null;
	/**
	 * @Field @Index
	 * @var string|null
	 */
	public $accountId = null;
	/**
	 * @Field @Index
	 * @var string|null
	 */
	public $providerName = null;
	/**
	 * @Field(type="datetime")
	 * @var \DateTime
	 */
	public $updated = null;
	/**
	 * @Field @Index
	 * @var string
	 */
	public $queueType = 'raw';
	/**
	 * @Field(type="mixed")
	 */
	public $queueData = null;
	/**
	 * @Field(type="boolean")
	 */
	public $locked = false;

}
