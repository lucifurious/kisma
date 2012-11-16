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
	//*************************************************************************
	//* Fields
	//*************************************************************************

	/**
	 * @Field(type="mixed")
	 * @var mixed
	 */
	protected $_session_data;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param mixed $sessionData
	 *
	 * @return Session
	 */
	public function setSessionData( $sessionData )
	{
		$this->_session_data = $sessionData;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSessionData()
	{
		return $this->_session_data;
	}

}