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

//*************************************************************************
//* Aliases
//*************************************************************************

use Kisma\Utility as Utility;

use Doctrine\CouchDB\View\FolderDesignDocument;
use Doctrine\ODM\CouchDB\Event as CouchDbEvent;
use Doctrine\Common\EventSubscriber;

/**
 * Document
 * A base container for key => value store documents (i.e. CouchDB, Mongo, etc.)
 *
 * @property string $documentName
 */
abstract class Document extends \Kisma\Components\Seed implements \Kisma\IContainer
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string The name of this document
	 */
	protected $_documentName = null;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array $options
	 *
	 * @return \Kisma\Container\Document
	 */
	public function __construct( $options = array() )
	{
		foreach ( $options as $_key => $_value )
		{
			if ( property_exists( $this, $_key ) )
			{
				Utility\Property::set( $this, $_key, $_value );
				Utility\Option::unsetOption( $options, $_key );
			}
		}

		parent::__construct( $options );
	}

	/**
	 * A generic mapping method
	 *
	 * @param array[] $map
	 * @param string  $source
	 * @param string  $mappedId
	 * @param bool	$singleValue
	 *
	 * @param bool	$singleValue
	 *
	 * @return \Kisma\Container\Document
	 */
	public function mapValue( &$map, $source, $mappedId = null, $singleValue = false )
	{
		//	make sure it's an array
		if ( empty( $map ) )
		{
			$map = array();
		}

		//	Make sure source array is there
		if ( !isset( $map[$source] ) )
		{
			$map[$source] = $singleValue ? null : array();
		}

		if ( !$singleValue )
		{
			//	Remove mapping?
			if ( null === $mappedId && false !== ( $_key = array_search( $mappedId, $map[$source] ) ) )
			{
				unset( $map[$source][$_key] );
			}
			//	Add mapping
			else if ( !in_array( $mappedId, $map[$source] ) )
			{
				array_push( $map[$source], $mappedId );
			}
		}
		else
		{
			if ( null === $mappedId )
			{
				unset( $map[$source] );
			}
			else
			{
				$map[$source] = $mappedId;
			}
		}

		return $this;
	}

	/**
	 * A generic mapping method
	 *
	 * @param array[] $map
	 * @param string  $source
	 * @param string  $mappedId
	 *
	 * @return \Kisma\Container\Document
	 */
	public function isMapped( &$map, $source, $mappedId = null, $singleValue = false )
	{
		return
			//	No id, true if there is a mapping in general
			( null === $mappedId && isset( $map, $map[$source] ) ) ||
			//	No id, true if there is a mapping in general
			( null !== $mappedId && isset( $map, $map[$source] ) && $map[$source] == $mappedId );
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
		if ( null === $this->_documentName )
		{
			$this->_documentName = get_class( $this );
		}

		return $this->_documentName;
	}
}
