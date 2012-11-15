<?php
/**
 * Document.php
 */
namespace Kisma\Core\Containers;
/**
 * Document
 * A base container for key => value store documents (i.e. CouchDB, Mongo, etc.). Does nothing, like the goggles.
 *
 * @Document
 */
abstract class Document
{
	//*************************************************************************
	//* Fields
	//*************************************************************************

	/** @Id */
	private $_id;
	/** @Attachments */
	private $_attachments;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param mixed $attachments
	 *
	 * @return Document
	 */
	public function setAttachments( $attachments )
	{
		$this->_attachments = $attachments;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAttachments()
	{
		return $this->_attachments;
	}

	/**
	 * @param string $id
	 *
	 * @return Document
	 */
	public function setId( $id )
	{
		$this->_id = $id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}

}
