<?php
/**
 * SeedDocument.php
 * A base container for key => value store documents (i.e. CouchDB, Mongo, etc.). Does nothing, like the goggles.
 */
namespace Kisma\Core\Containers\Documents;
use Doctrine\ODM\CouchDB\Mapping\Annotations;

/**
 * @Document
 * @MappedSuperclass
 */
abstract class SeedDocument
{
	//*************************************************************************
	//* Fields
	//*************************************************************************

	/** @var string
	 * @Id
	 */
	private $_id;
	/** @var string
	 * @Version
	 */
	private $_version;
	/** @var mixed
	 * @Attachments
	 */
	private $_attachments;
	/**
	 * @var string
	 */
	private $_created_at = null;
	/**
	 * @var string
	 */
	private $_updated_at = null;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Set defaults
	 */
	public function __construct()
	{
		$this->_created_at = time();
	}

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

	/**
	 * @param string $version
	 *
	 * @return \Kisma\Core\Containers\Documents\SeedDocument
	 */
	public function setVersion( $version )
	{
		$this->_version = $version;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return $this->_version;
	}

	/**
	 * @param string $created_at
	 *
	 * @return SeedDocument
	 */
	protected function setCreatedAt( $created_at )
	{
		$this->_created_at = $created_at;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCreatedAt()
	{
		return $this->_created_at;
	}

	/**
	 * @param string $updated_at
	 *
	 * @return SeedDocument
	 */
	protected function setUpdatedAt( $updated_at )
	{
		$this->_updated_at = $updated_at;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt()
	{
		return $this->_updated_at;
	}
}
