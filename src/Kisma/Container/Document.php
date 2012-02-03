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
namespace Kisma\Container;

/**
 * Document
 * A base for key => value store documents (i.e. CouchDB, Mongo, etc.)
 *
 * @Document
 * @MappedSuperclass
 *
 * @property string $id
 * @property string $version
 * @property int $createTime
 * @property int $updateTime
 * @property int $expireTime
 */
abstract class Document extends \Kisma\Components\Seed
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

	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	public function __set( $name, $value )
	{
		if ( property_exists( $this, $name ) )
		{
			$this->{$name} = $value;
		}
	}

	//*************************************************************************
	//* Default Event Handlers
	//*************************************************************************

	/**
	 * @param $event
	 *
	 * @return bool
	 */
	public function onBeforeValidate( $event )
	{
		return true;
	}

	/**
	 * @param $event
	 *
	 * @return bool
	 */
	public function onAfterValidate( $event )
	{
		return true;
	}

	/**
	 * @param $event
	 *
	 * @return bool
	 */
	public function onBeforeFind( $event )
	{
		return true;
	}

	/**
	 * @param $event
	 *
	 * @return bool
	 */
	public function onAfterFind( $event )
	{
		return true;
	}

	/**
	 * @param $event
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

	/**
	 * @param $event
	 *
	 * @return bool
	 */
	public function onAfterSave( $event )
	{
		return true;
	}

	/**
	 * @param $event
	 *
	 * @return bool
	 */
	public function onBeforeDelete( $event )
	{
		return true;
	}

	/**
	 * @param $event
	 *
	 * @return bool
	 */
	public function onAfterDelete( $event )
	{
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
