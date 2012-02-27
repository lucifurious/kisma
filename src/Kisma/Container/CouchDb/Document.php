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

//*************************************************************************
//* Aliases
//*************************************************************************

use Kisma\Kisma as K;

use Kisma\Provider\CouchDb\ServiceProvider;

use Doctrine\ODM\CouchDB\Event\LifecycleEventArgs;
use Doctrine\CouchDB\View\FolderDesignDocument;
use Doctrine\Common\EventSubscriber;

/**
 * Document
 * A Doctrine mapping for a CouchDB document
 *
 * @property string $databaseName
 *
 * @Document
 * @MappedSuperClass
 *
 * @property string $id
 * @property string $version
 * @property int $createTime
 * @property int $updateTime
 * @property int $expireTime
 */
abstract class Document extends \Kisma\Container\Document
{
	//*************************************************************************
	//* Document Fields
	//*************************************************************************

	/** @Id */
	public $id = null;
	/** @Version */
	public $version = null;
	/**
	 * @var \DateTime
	 * @Field(type="datetime")
	 */
	public $createTime = null;
	/**
	 * @var \DateTime
	 * @Field(type="datetime")
	 */
	public $updateTime = null;

	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string The name of this document's database
	 */
	protected $_databaseName = null;
	/**
	 * @var bool
	 */
	protected $_newDocument = true;

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * Set the document manager if we have a database
	 *
	 * @param \Kisma\Event\KismaEvent $event
	 *
	 * @return bool
	 */
	public function onAfterConstruct( \Kisma\Event\KismaEvent $event )
	{
		//	Autoset database name
		if ( null === $this->_databaseName )
		{
			$this->_databaseName = \Kisma\Utility\Inflector::tag( get_class( $this ) . 'Documents' );
		}

		return parent::onAfterConstruct( $event );
	}

	//*************************************************************************
	//* Default Event Handlers
	//*
	//* These are triggered by the document manager
	//*************************************************************************

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\ConflictEventArgs $event
	 */
	public function onConflict( \Doctrine\ODM\CouchDB\Event\ConflictEventArgs $event )
	{
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\OnFlushEventArgs $events
	 */
	public function onFlush( \Doctrine\ODM\CouchDB\Event\OnFlushEventArgs $events )
	{
		//	Not a new record
		$this->_newDocument = false;
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $events
	 */
	public function postLoad( \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $events )
	{
		//	Not a new record
		$this->_newDocument = false;
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $events
	 */
	public function postRemove( \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $events )
	{
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $events
	 */
	public function postUpdate( \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $events )
	{
		//	Not a new record
		$this->_newDocument = false;
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $event
	 */
	public function prePersist( $event )
	{
		$this->updateTime = new \DateTime( 'now' );

		if ( null === $this->createTime )
		{
			$this->createTime = $this->updateTime;
		}
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $events
	 */
	public function preRemove( \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $events )
	{
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $events
	 */
	public function preUpdate( \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $events )
	{
		$this->updateTime = new \DateTime( 'now' );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param string $databaseName
	 *
	 * @return \Kisma\Container\CouchDb\Document
	 */
	public function setDatabaseName( $databaseName )
	{
		$this->_databaseName = $databaseName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDatabaseName()
	{
		return $this->_databaseName;
	}

	/**
	 * @param boolean $newDocument
	 *
	 * @return \Kisma\Container\CouchDb\Document
	 */
	public function setNewDocument( $newDocument )
	{
		$this->_newDocument = $newDocument;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getNewDocument()
	{
		return $this->_newDocument;
	}
}
