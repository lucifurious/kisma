<?php
/**
 * Document.php
 */
namespace Kisma\Core\Containers;
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
	//* Members
	//*************************************************************************

	/**
	 * @var array The default fields in this document
	 */
	protected $_defaultFields = array(
		'_id',
		'_rev',
		'.document_name',
	);

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param string $documentName
	 * @param array  $contents
	 */
	public function __construct( $documentName = null, $contents = array() )
	{
		//	Set the document name
		$this->_defaultFields['.document_name'] = $documentName ? : get_class( $this );

		foreach ( $this->_defaultFields as $_field )
		{
			$this->set( $_field, null );
		}

		parent::__construct();
	}
}
