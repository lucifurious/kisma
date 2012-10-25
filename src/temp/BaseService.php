<?php
/**
 * BaseService.php
 *
 * Copyright (c) 2012 Silverpop Systems, Inc.
 * http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 * @filesource
 */
namespace CIS\Services;

/**
 * BaseService
 * The base class for services provided. Provides built-in logging and two event handlers:
 *
 * onBeforeServiceCall and onAfterServiceCall which are called before and after
 * the service is run, respectively.
 *
 * @property string                  $serviceName The name of this service
 * @property \CIS\Utility\Lumberjack $logger      The logging facility
 * @property string                  $logChannel
 * @property string                  $logFile
 * @property ServiceSettings         $settings
 */
abstract class BaseService extends \CIS\Components\BaseObject implements \CIS\Interfaces\ServiceEvents, \CIS\Interfaces\LogLevel, \CIS\Interfaces\Reactor
{
	//********************************************************************************
	//* Private Members
	//********************************************************************************

	/**
	 * @var string The name of this service
	 */
	protected $_serviceName = null;
	/**
	 * @var string|false Set to false to disable logging otherwise the log tag
	 */
	protected $_logChannel = null;
	/**
	 * @var string The full path to the log file
	 */
	protected $_logFile = null;
	/**
	 * @var bool If true, graylogging will happen
	 */
	protected $_graylog = false;
	/**
	 * @var ServiceSettings
	 */
	protected $_settings = null;

	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * Constructor
	 *
	 * @param array $options
	 *
	 * @return \CIS\Services\BaseService
	 */
	public function __construct( $options = array() )
	{
		if ( null === $this->_logChannel && !isset( $options, $options['logChannel'] ) )
		{
			$this->_logChannel = \CIS\CIS::getRepoTag();
		}

		if ( $options instanceof ServiceSettings )
		{
			$this->_settings = $options;
			$options = array();
		}

		parent::__construct( $options );
	}

	//*************************************************************************
	//* Logging Helpers
	//*************************************************************************

	/**
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function logInfo( $message, $context = array() )
	{
		return $this->_log( self::Info, $message, $context );
	}

	/**
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function logCritical( $message, $context = array() )
	{
		return $this->_log( self::Critical, $message, $context );
	}

	/**
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function logWarning( $message, $context = array() )
	{
		return $this->_log( self::Warning, $message, $context );
	}

	/**
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function logAlert( $message, $context = array() )
	{
		return $this->_log( self::Alert, $message, $context );
	}

	/**
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function logError( $message, $context = array() )
	{
		return $this->_log( self::Error, $message, $context );
	}

	/**
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function logDebug( $message, $context = array() )
	{
		return $this->_log( self::Debug, $message, $context );
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * Check if there is a logger available
	 *
	 * @return bool
	 */
	protected function _checkLogger()
	{
		if ( null === $this->_logger )
		{
			$_tag = \CIS\Utility\Lumberjack::cleanLogFileName( get_class( $this ) );

			//	No log file given, can we determine where to place this log file?
			if ( null === $this->_logFile && null !== \CIS\CIS::getRepoTag() )
			{
				$this->_logFile = $this->_createLogFileName();
			}

			$this->_logger = new \CIS\Utility\Lumberjack(
				array(
					'graylog'    => $this->_graylog,
					'logChannel' => $this->_logChannel ? : \CIS\CIS::getRepoTag(),
					'logFile'    => $this->_logFile
						? : \CIS\CisPath::log( 'libs.internal.CIS' ) . DIRECTORY_SEPARATOR .
							( 'cli' == PHP_SAPI ? 'cli' : 'web' ) . '.' . $_tag . '.log',
				)
			);
		}

		//	Fail-safe logger...
		if ( null === $this->_logger )
		{
			$this->_logger = \CIS\CIS::getLumberjack();
		}

		return null !== $this->_logger;
	}

	/**
	 * @param int    $level
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	protected function _log( $level = self::Info, $message, $context = array() )
	{
		if ( !$this->_checkLogger() )
		{
			return false;
		}

		return $this->_logger->log( $message, $level, $context );
	}

	/**
	 * Creates a uniform log file name
	 *
	 * @return string
	 */
	protected function _createLogFileName()
	{
		$_logFileName = ( 'cli' == PHP_SAPI ? 'console' : 'web' ) . '.' . gethostname() . '.log';
		return \CIS\CisPath::log( \CIS\CIS::getRepoTag() ) . DIRECTORY_SEPARATOR . $_logFileName;
	}

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * @param \CIS\Services\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeServiceCall( $event )
	{
		//	Default implementation
		return true;
	}

	/**
	 * @param \CIS\Services\ServiceEvent $event
	 *
	 * @return bool
	 */
	public function onAfterServiceCall( $event )
	{
		//	Default implementation
		return true;
	}

	//********************************************************************************
	//* Property Accessors
	//********************************************************************************

	/**
	 * @param string $serviceName
	 *
	 * @return \CIS\Services\BaseService
	 */
	public function setServiceName( $serviceName )
	{
		$this->_serviceName = $serviceName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getServiceName()
	{
		return $this->_serviceName;
	}

	/**
	 * @param \CIS\Utility\Lumberjack $logger
	 *
	 * @return \CIS\Components\BaseObject|\CIS\Services\BaseService
	 */
	public function setLogger( $logger )
	{
		$this->_logger = $logger;
		return $this;
	}

	/**
	 * @return \CIS\Utility\Lumberjack|\Monolog\Logger|null
	 */
	public function getLogger()
	{
		return $this->_logger;
	}

	/**
	 * @param string $logFile
	 *
	 * @return \CIS\Services\BaseService
	 */
	public function setLogFile( $logFile )
	{
		$this->_logFile = $logFile;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLogFile()
	{
		return $this->_logFile;
	}

	/**
	 * @param \CIS\Services\false|string $logChannel
	 *
	 * @return \CIS\Services\BaseService
	 */
	public function setLogChannel( $logChannel )
	{
		$this->_logChannel = $logChannel;
		return $this;
	}

	/**
	 * @return \CIS\Services\false|string
	 */
	public function getLogChannel()
	{
		return $this->_logChannel;
	}

	/**
	 * @param boolean $graylog
	 *
	 * @return BaseService
	 */
	public function setGraylog( $graylog )
	{
		$this->_graylog = $graylog;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getGraylog()
	{
		return $this->_graylog;
	}

	/**
	 * @param \CIS\Services\ServiceSettings $settings
	 *
	 * @return \CIS\Services\ServiceSettings
	 */
	public function setSettings( $settings )
	{
		$this->_settings = $settings;
		return $this;
	}

	/**
	 * @return \CIS\Services\ServiceSettings
	 */
	public function getSettings()
	{
		return $this->_settings;
	}
}
