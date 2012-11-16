<?php
/**
 * SeedDocument.php
 * A base container for key => value store documents (i.e. CouchDB, Mongo, etc.). Does nothing, like the goggles.
 */
namespace Kisma\Core\Containers\Documents;
use \Doctrine\ODM\CouchDB\Mapping\Annotations;

/**
 * @Document
 */
abstract class SeedDocument
{
	//*************************************************************************
	//* Fields
	//*************************************************************************

	/**
	 * @Id
	 * @var string
	 */
	private $_id;
	/**
	 * @Attachments
	 * @var mixed
	 */
	private $_attachments;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param mixed $attachments
	 *
	 * @return \Kisma\Core\Containers\Documents\SeedDocument
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
	 * @param string|array $id
	 *
	 * @return \Kisma\Core\Containers\Documents\SeedDocument
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
