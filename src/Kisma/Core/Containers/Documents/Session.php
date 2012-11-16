<?php
/**
 * Session.php
 * A simple session document
 */
namespace Kisma\Core\Containers\Documents;
use \Doctrine\ODM\CouchDB\Mapping\Annotations;

/**
 * @Document
 */
class Session extends SeedDocument
{
	/**
	 * @Field(type="mixed",jsonName="session_data")
	 * @var mixed
	 */
	protected $_sessionData;

	/**
	 * @param mixed $sessionData
	 *
	 * @return Session
	 */
	public function setSessionData( $sessionData )
	{
		$this->_sessionData = $sessionData;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSessionData()
	{
		return $this->_sessionData;
	}

}