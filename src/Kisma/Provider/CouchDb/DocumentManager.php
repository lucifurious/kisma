<?php
/**
 * @file
 * Doctrine/CouchDB document management
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Framework
 * @package kisma
 * @since 1.0.0
 *
 * @ingroup framework
 */
namespace Kisma\Provider\CouchDb;

//*************************************************************************
//* Aliases
//*************************************************************************

use Kisma\Kisma as K;
use Kisma\Event\DocumentEvent;

use Doctrine\CouchDB\CouchDBClient;
use Doctrine\Common\EventManager;
use Doctrine\ODM\CouchDB\Configuration;
use Doctrine\ODM\CouchDB\Event as CouchDbEvent;
use Doctrine\Common\EventSubscriber;

/**
 * DocumentManager.php
 */
class DocumentManager extends \Doctrine\ODM\CouchDB\DocumentManager
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string The path to the design document structure (usually just here)
	 */
	protected $_designPath = '/';

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Copy of factory method for a Document Manager. This returns our own DM
	 *
	 * @param array																		  $couchParams
	 * @param \Doctrine\ODM\CouchDB\Configuration|null									   $config
	 * @param \Doctrine\Common\EventManager|\Doctrine\Common\EventManager|null			   $evm
	 *
	 * @return \Kisma\Provider\CouchDb\DocumentManager
	 */
	public static function create( $couchParams, Configuration $config = null, EventManager $evm = null )
	{
		if ( is_array( $couchParams ) )
		{
			$couchClient = CouchDBClient::create( $couchParams );
		}
		else if ( $couchParams instanceof CouchDBClient )
		{
			$couchClient = $couchParams;
		}
		else
		{
			throw new \InvalidArgumentException( "Expecting array of instance of CouchDBClient as first argument to DocumentManager::create()." );
		}

		//	Create
		$_dm = new DocumentManager( $couchClient, $config, $evm );

		//	Register event listener
		$_dm->getEventManager()->addEventListener(
			array(
				CouchDbEvent::onConflict,
				CouchDbEvent::onFlush,
				CouchDbEvent::postLoad,
				CouchDbEvent::postRemove,
				CouchDbEvent::postUpdate,
				CouchDbEvent::prePersist,
				CouchDbEvent::preRemove,
				CouchDbEvent::preUpdate,
			),
			//	We are our event handler
			$_dm
		);

		return $_dm;
	}

	/**
	 * Saves the model
	 *
	 * @param \Kisma\Container\CouchDb\Document $document
	 * @param bool							  $autoCommit
	 *
	 * @return mixed
	 */
	public function save( $document, $autoCommit = true )
	{
		//	Persist the document
		$this->persist( $document );

		//	Flush
		if ( true === $autoCommit )
		{
			$this->flush();
		}
	}

	/**
	 * Register the design document
	 *
	 * @param \Doctrine\CouchDB\View\DesignDocument $designDocument
	 *
	 * @return \Doctrine\CouchDB\HTTP\Response
	 */
	public function registerDesignDocument( \Doctrine\CouchDB\View\DesignDocument $designDocument )
	{
		return $this->getCouchDBClient()->createDesignDocument( 'document', $designDocument );
	}

	//*************************************************************************
	//* Default Event Handlers
	//*************************************************************************

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\ConflictEventArgs $event
	 */
	public function onConflict( \Doctrine\ODM\CouchDB\Event\ConflictEventArgs $event )
	{
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\OnFlushEventArgs $event
	 */
	public function onFlush( \Doctrine\ODM\CouchDB\Event\OnFlushEventArgs $event )
	{
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $event
	 *
	 * @return mixed
	 */
	public function postLoad( \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $event )
	{
		return $event->getDocument()->postLoad( $event );
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $event
	 *
	 * @return mixed
	 */
	public function postRemove( \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $event )
	{
		return $event->getDocument()->postRemove( $event );
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $event
	 *
	 * @return mixed
	 */
	public function postUpdate( \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $event )
	{
		return $event->getDocument()->postUpdate( $event );
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $event
	 *
	 * @return mixed
	 */
	public function prePersist( $event )
	{
		return $event->getDocument()->prePersist( $event );
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $event
	 *
	 * @return mixed
	 */
	public function preRemove( \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $event )
	{
		return $event->getDocument()->preRemove( $event );
	}

	/**
	 * @param \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $event
	 *
	 * @return mixed
	 */
	public function preUpdate( \Doctrine\ODM\CouchDB\Event\LifecycleEventArgs $event )
	{
		return $event->getDocument()->preUpdate( $event );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param string $designPath
	 *
	 * @return \Kisma\Container\Document
	 */
	public function setDesignPath( $designPath )
	{
		$this->_designPath = $designPath;

//		$this->registerDesignDocument( new FolderDesignDocument( __DIR__ . $this->_designPath ) );

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDesignPath()
	{
		return $this->_designPath;
	}

}