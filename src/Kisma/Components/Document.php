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
	 * A magical bare-bones generic document. It's a thinly veiled wrapper around a \stdClass.
	 * This is the basis for document storage objects.
	 *
	 * You can read and write document properties just like using a \stdClass.
	 *
	 * In addition, IF you create a getter or setter for a property, it will always be called. This allows
	 * you to enforce property restrictions in a single place.
	 *
	 * @property \stdClass $document
	 */
	class Document extends Seed
	{
		//*************************************************************************
		//* Private Members 
		//*************************************************************************

		/**
		 * @var \stdClass|array The document contents
		 */
		protected $_document = null;

		//*************************************************************************
		//* Magic
		//*************************************************************************

		/**
		 * @param array $options
		 */
		public function __construct( $options = array() )
		{
			parent::__construct( $options );

			if ( null === $this->_document )
			{
				$this->setDocument();
			}
		}

		/**
		 * Checks if a document property is set
		 *
		 * @param string $property
		 *
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
		 *
		 * @param string $property
		 *
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
		 *
		 * @param string $property
		 *
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

			try
			{
				return parent::__get( $property );
			}
			catch ( \Kisma\UndefinedPropertyException $_ex )
			{
				//	Set the property in the document if it wasn't found
				return $this->_document->{$property} = null;
			}
		}

		/**
		 * Sets a property within the document
		 *
		 * @param string $property
		 * @param mixed  $value
		 *
		 * @return Document
		 */
		public function __set( $property, $value )
		{
			$_setter = 'set' . $property;

			if ( method_exists( $this, $_setter ) )
			{
				return $this->{$_setter}( $value );
			}

			if ( isset( $this->_document ) )
			{
				$this->_document->{$property} = $value;
				return $this;
			}
		}

		/**
		 * @return \stdClass
		 */
		public function toObject()
		{
			$_obj = new \stdClass();
			$_obj->_id = $this->_document->_id;

			if ( null !== $this->_document->_rev )
			{
				$_obj->_rev = $this->_document->_rev;
			}

			return $_obj;
		}

		/**
		 * @return array
		 */
		public function __sleep()
		{
			return array(
				'document',
			);
		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * Set the current document. Calling with no parameters resets the object
		 *
		 * @param array|\stdClass $document
		 *
		 * @return \Kisma\Components\Document
		 */
		public function setDocument( $document = null )
		{
			//	set the default stuff
			if ( null === $document )
			{
				$document = new \stdClass();
				$document->create_time = microtime( true );
			}

			$this->_document = $document;

			return $this;
		}

		/**
		 * @return \stdClass
		 */
		public function &getDocument()
		{
			//	Should always have an object
			if ( null === $this->_document )
			{
				$this->setDocument();
			}

			return $this->_document;
		}
	}
}