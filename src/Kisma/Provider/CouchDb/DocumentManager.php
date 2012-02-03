<?php
/**
 * @file
 * Doctrine/CouchDB document management
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
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

use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\CouchDB\HTTP\Client;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\CouchDB\View;
use Doctrine\CouchDB\View\Query;
use Doctrine\ODM\CouchDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\CouchDB\View\ODMQuery;
use Doctrine\ODM\CouchDB\View\ODMLuceneQuery;

/**
 * DocumentManager.php
 */
class DocumentManager extends \Doctrine\ODM\CouchDB\DocumentManager
{
	/**
	 * @param mixed $object
	 */
	public function remove( $object )
	{
		\Kisma\Kisma::app()->dispatch( \Kisma\Event\ModelEvent::BeforeDelete, $object );
		$this->getUnitOfWork()->scheduleRemove( $object );
		\Kisma\Kisma::app()->dispatch( \Kisma\Event\ModelEvent::AfterDelete, $object );
	}

	/**
	 * @deprecated Use commit() instead
	 */
	public function flush()
	{
		$this->commit();
	}

	/**
	 * Commit the transaction
	 */
	public function commit()
	{
		\Kisma\Kisma::app()->dispatch(
			\Kisma\Event\ModelEvent::BeforeSave,
			new \Kisma\Event\ModelEvent( $this->getUnitOfWork() )
		);

		$this->getUnitOfWork()->flush();

		\Kisma\Kisma::app()->dispatch(
			\Kisma\Event\ModelEvent::AfterSave,
			new \Kisma\Event\ModelEvent( $this )
		);
	}
}
