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

/**
 * Document
 * A base for documents
 *
 * @Document
 * @MappedSuperclass
 *
 * @property string $id
 * @property string $version
 * @property int $create_time
 * @property int $update_time
 * @property int $expire_time
 */
class Document extends \Kisma\Components\Seed
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string The name of this document
	 */
	protected $_documentName = null;

	//*************************************************************************
	//* Document Fields
	//*************************************************************************

	/** @Id */
	public $id = null;

	/** @Version */
	public $version = null;

	/** @Attachments */
	public $attachments;

	/** @Field(type="date") */
	public $createTime = null;

	/** @Field(type="date") */
	public $updateTime = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
		parent::__construct( $options );

		if ( null === $this->_documentName )
		{
			$this->_documentName = str_replace( '\\', '.', \get_class( $this ) );
		}
	}

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * @param ModelEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeSave( $event )
	{
		$this->updateTime = date( 'c' );

		if ( null === $this->createTime )
		{
			$this->createTime = $this->updateTime;
		}

		return true;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param string $documentName
	 *
	 * @return \Kisma\Container\CouchDb\Document
	 */
	public function setDocumentName( $documentName )
	{
		$this->_documentName = $documentName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDocumentName()
	{
		return $this->_documentName;
	}

}
