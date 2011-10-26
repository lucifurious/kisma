<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link	  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license   http://github.com/Pogostick/kisma/licensing/
 * @author	Jerry Ablan <kisma@pogostick.com>
 * @category  Kisma_Components
 * @package   kisma.components
 * @namespace \Kisma\Components
 * @since	 v1.0.0
 * @filesource
 */
namespace Kisma\Components;
{
	/**
	 * Document
	 * A magical bare-bones CouchDB document wrapper. Read and write document properties just like using
	 * a \stdClass. _id and _rev had dedicated getters and setters.
	 *
	 * In addition, IF you create a getter or setter for a property, it will always be called. This allows
	 * you to enforce property restrictions in a single place.
	 *
	 * @property \stdClass $document
	 *
	 * @property string $id The document _id
	 * @property string $rev The document _rev
	 */
	class Document extends Component
	{
		//*************************************************************************
		//* Constants
		//*************************************************************************

		/**
		 * @var string
		 */
		const DefaultContentType = 'application/octet-stream';

		//*************************************************************************
		//* Private Members 
		//*************************************************************************

		/**
		 * @var \stdClass|array The document contents
		 */
		protected $_document = null;

		/**
		 * @param array $options
		 */
		public function __construct( $options = array() )
		{
			parent::__construct( $options );

			if ( null === $this->_document )
			{
				$this->_document = new \stdClass();
			}
		}

		//*************************************************************************
		//* Magic
		//*************************************************************************

		/**
		 * Checks if a document property is set
		 * @param string $property
		 * @return boolean
		 */
		public function __isset( $property )
		{
			if ( isset( $this->_document ) )
			{
				return isset( $this->_document->{$property} );
			}

			return parent::__isset( $property );
		}

		/**
		 * Unsets a document property
		 * @param string $property
		 * @return \Kisma\Components\Document
		 */
		public function __unset( $property )
		{
			if ( isset( $this->_document, $this->_document->{$property} ) )
			{
				unset( $this->_document->{$property} );
			}
			else
			{
				parent::__unset( $property );
			}

			return $this;
		}

		/**
		 * Gets a property from the document
		 * @param string $property
		 * @return mixed
		 */
		public function __get( $property )
		{
			$_getter = 'get' . $property;

			if ( method_exists( $this, $_getter ) )
			{
				return $this->{$_getter}( $property );
			}

			if ( isset( $this->_document, $this->_document->{$property} ) )
			{
				return $this->_document->{$property};
			}

			return parent::__get( $property );
		}

		/**
		 * Sets a property within the document
		 * @param string $property
		 * @param mixed $value
		 * @return Document
		 */
		public function __set( $property, $value )
		{
			$_setter = 'set' . $property;

			if ( method_exists( $this, $_setter ) )
			{
				return $this->{$_setter}( $property, $value );
			}

			if ( isset( $this->_document ) )
			{
				$this->_document->{$property} = $value;
				return $this;
			}
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * Set the current document. Calling with no parameters resets the object
		 * @param array|\stdClass $document
		 * @return \Kisma\Components\Document
		 */
		public function setDocument( $document = null )
		{
			//	blanket overwrite
			$this->_document = ( $document ?: new \stdClass() );
			return $this;
		}

		/**
		 * @return \stdClass
		 */
		public function getDocument()
		{
			//	Should always have an object
			if ( null === $this->_document )
			{
				$this->_document = new \stdClass();
			}

			return $this->_document;
		}

		/**
		 * @return string
		 */
		public function getId()
		{
			return $this->_document->_id;
		}

		/**
		 * @param string $id
		 * @return \Kisma\Components\Document
		 */
		public function setId( $id )
		{
			$this->getDocument()->_id = $id;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getRev()
		{
			return $this->_document->_rev;
		}

		/**
		 * @param string $rev
		 * @return \Kisma\Components\Document
		 */
		public function setRev( $rev )
		{
			$this->_document->_rev = $rev;
			return $this;
		}

		/**
		 * @param \Kisma\Aspects\Storage\CouchDb $db
		 * @return \Kisma\Components\Document
		 */
		public function setDb( $db )
		{
			$this->_db = $db;
			return $this;
		}

		/**
		 * @return \Kisma\Aspects\Storage\CouchDb
		 */
		public function getDb()
		{
			return $this->_db;
		}

		/**
		 * @param \Kisma\Services\Remote\CouchDbServer $dbServer
		 * @return \Kisma\Components\Document
		 */
		public function setDbServer( $dbServer )
		{
			$this->_dbServer = $dbServer;
			return $this;
		}

		/**
		 * @return \Kisma\Services\Remote\CouchDbServer
		 */
		public function getDbServer()
		{
			return $this->_dbServer;
		}
	}
}