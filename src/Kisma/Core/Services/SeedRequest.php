<?php
namespace Kisma\Core\Services;

/**
 * SeedRequest
 * A basic service request
 */
use Kisma\Core\SeedBag;
use Kisma\Core\Interfaces\RequestLike;
use Kisma\Core\Interfaces\RequestSource;

abstract class SeedRequest extends SeedBag implements RequestLike, RequestSource
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var int
	 */
	protected $_source = self::Http;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param array         $contents
	 * @param RequestSource $source
	 */
	public function __construct( $contents = array(), $source = null )
	{
		//	Set the request type properly...
		if ( null === $source && PHP_SAPI == 'cli' )
		{
			$this->_source = static::Cli;
		}

		parent::__construct( $contents );
	}

	/**
	 * @return int
	 */
	public function getSource()
	{
		return $this->_source;
	}

	/**
	 * @param int $source
	 *
	 * @return SeedRequest
	 */
	public function setSource( $source )
	{
		$this->_source = $source;

		return $this;
	}

}
