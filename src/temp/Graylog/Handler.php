<?php
/**
 * Handler.php
 * Class with static method for sending GELF messages to the graylog server
 *
 * @author      Joe Lafiosca <jlafiosca@silverpop.com>
 * @author      Jerry Ablan <jablan@silverpop.com>
 */
namespace CIS\Services\Graylog;

require_once 'cislib/GELFLogger.php';

use CIS\Services\Graylog\Message;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Handler
 */
class Handler extends AbstractProcessingHandler implements Graylog
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var bool
	 */
	protected $_lastResult = null;
	/**
	 * @var \CIS\Exceptions\MissingParameterException
	 */
	protected $_lastError = null;

	//**********************************************************************
	//* Public Methods
	//**********************************************************************

	/**
	 * Static method for sending a GELF message to graylog2
	 * Expects a single parameter which is an array to pass to the
	 * Message constructor
	 *
	 * @param array $message The GELF message to log
	 *
	 * @return void
	 */
	protected function write( array $message )
	{
		try
		{
			/**
			 * $message comes from Monolog as an array with the following keys:
			 *
			 * message      string
			 * context      array
			 * level        int
			 * level_name   string
			 * channel      string
			 * datetime     string
			 * extra        array
			 */
			$_level = \CIS\Utility\Option::get( $message, 'level', self::DefaultLevel );
			$_facility = \CIS\Utility\Option::get( $message, 'channel', self::DefaultFacility );
			$_text = \CIS\Utility\Option::get( $message, 'message', 'Empty message text', true );

			$this->_lastResult = \GELFLogger::logMessage(
				array(
					'short_message'   => $_text,
					'full_message'    => \CIS\Utility\Option::get( $message, 'formatted', $_text, true ),
					'level'           => \CIS\Enums\GraylogLevel::fromMonologLevel( $_level ),
					'facility'        => $_facility,
					'_request'        => json_encode( $_REQUEST ),
					'_server'         => json_encode( $_SERVER ),
				)
			);
		}
		catch ( \Exception $_ex )
		{
			$this->_lastError = $_ex;
		}
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @return \CIS\Exceptions\MissingParameterException
	 */
	public function getLastError()
	{
		return $this->_lastError;
	}

	/**
	 * @return boolean
	 */
	public function getLastResult()
	{
		return $this->_lastResult;
	}
}
