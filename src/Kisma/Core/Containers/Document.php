<?php
/**
 * Document.php
 */
namespace Kisma\Containers;
/**
 * Document
 * A base container for key => value store documents (i.e. CouchDB, Mongo, etc.)
 *
 * @property string $documentName
 */
abstract class Document extends \Kisma\Core\SeedBag
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
	 * @param string $name
	 * @param array  $settings
	 *
	 * @return \Kisma\Containers\Document
	 */
	public function __construct( $name, $settings = array() )
	{
		if ( is_array( $name ) )
		{
			$settings = $name;
			$name = get_class( $this );
		}

		$this->_documentName = $name;

		parent::__construct( $settings );
	}

	/**
	 * @param string $documentName
	 *
	 * @return Document
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
