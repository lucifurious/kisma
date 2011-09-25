<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright     Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link          http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license       http://github.com/Pogostick/kisma/licensing/
 * @author        Jerry Ablan <kisma@pogostick.com>
 * @category      Kisma_Aspects
 * @package       kisma.aspects
 * @namespace     \Kisma\Aspects
 * @since         v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma Kisma
 */
namespace Kisma\Aspects;

//*************************************************************************
//* Requirements
//*************************************************************************

/**
 * ProcessLock
 * Class description
 * 
 * @property-read string $lockFile
 * @property string $lockFileTemplate
 * @property string $lockFilePath
 * @property int $processId
 * @property string $hostName
 * @property string $tag
 * @property array $waitList
 */
class ProcessLock extends \Kisma\Components\Aspect
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @const int The number of minutes before complaining about an existing lock
	 */
	const DEFAULT_LOCK_AGE = 60;

	//*************************************************************************
	//* Private Members 
	//*************************************************************************

	/**
	 * @var string The name of the lock file
	 */
	protected $_lockFile = null;
	/**
	 * @var string The template for the lock file
	 */
	protected $_lockFileTemplate = '%%tag%%.lock';
	/**
	 * @var string The path to store the lock file
	 */
	protected $_lockFilePath = __DIR__;
	/**
	 * @var int The process ID
	 */
	protected $_processId = null;
	/**
	 * @var string The hostname
	 */
	protected $_hostName = null;
	/**
	 * @var string The tag for the lock. Defaults to the class name
	 */
	protected $_tag = null;
	/**
	 * @var array The list of wait processes
	 */
	protected $_waitList = array();

	//*************************************************************************
	//* Public Methods 
	//*************************************************************************

	/**
	 * Constructor
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
		//	Some quick system info
		$this->_processId = getmypid();
		$this->_hostName = php_uname( 'n' );
		$this->_tag = K::standardizeName( get_class( $this ) );

		//	Pass on to base...
		parent::__construct( $options );
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * @return bool
	 */
	protected function _createProcessLock()
	{
		//	Make a lock file
		try
		{
			//	Create a lock file name
			$this->_lockFile = $this->_lockFilePath . '/' . ( $this->_lockFile = $this->_createLockFileName() );

			//	check our lock file...
			if ( false !== ( $_age = $this->_shouldWait( $this->_tag ) ) )
			{
				//	Lock file exists...
				if ( true !== $_age )
				{
					//	Error, existing lock file over time threshold
					throw new \Kisma\ProcessLockAgeException( 'Lock file over time threshold of ' . self::DEFAULT_LOCK_AGE . ' minutes.' );
				}
				else
				{
					//	Error: Still running
					throw new \Kisma\ProcessLockExistsException( 'Lock file exists for this process.' );
				}
			}

			//	Create lock file
			if ( false === @file_put_contents( $this->_lockFile, $this->_processId ) )
			{
				//	Error: Cannot create lock file
				@unlink( $this->_lockFile );
				throw new \Kisma\ProcessLockFileException( 'Lock file exists for this process.' );
			}

			return true;
		}
		catch ( \Kisma\ProcessLockException $_ex )
		{
			//	Rethrow our own exceptions
			throw $_ex;
		}
		catch ( \Exception $_ex )
		{
			//	Translate and rethrow
			throw new \Kisma\ProcessLockException( $_ex );
		}
	}

	/**
	 * @return bool
	 */
	protected function _destroyLockFile()
	{
		try
		{
			//	No lock, we're good
			if ( !file_exists( $this->_lockFile ) )
			{
				return true;
			}

			if ( false !== @unlink( $this->_lockFile ) )
			{
				throw new \Kisma\ProcessLockFileException( 'Error deleting lock file "' . $this->_lockFile . '"' );
			}

			return true;
		}
		catch ( \Kisma\ProcessLockException $_ex )
		{
			//	Rethrow our own exceptions
			throw $_ex;
		}
		catch ( \Exception $_ex )
		{
			//	Translate and rethrow
			throw new \Kisma\ProcessLockFileException( $_ex );
		}
	}

	/**
	 * Creates a standardized lock file name
	 * @return string
	 */
	protected function _createLockFileName()
	{
		//	Create a lock file name
		return trim(
			str_ireplace(
				array(
					'%%hostName%%',
					'%%processId%%',
					'%%tag%%',
					'%%className%%',
				),
				array(
					$this->_hostName,
					$this->_processId,
					$this->_tag,
					K::standardizeName( get_class( $this ) ),
				),
				$this->_lockFileTemplate
			)
		);
	}

	/**
	 * Returns number of seconds old the lock is if it exists or false if no lock file.
	 * @param int $lockAge
	 * @return bool|int
	 */
	protected function _lockExists( $lockAge = self::DEFAULT_LOCK_AGE )
	{
		//	check our lock file...
		if ( @file_exists( $this->_lockFile ) )
		{
			//	If time is greater than age asked, let someone know...
			$_age = time() - filemtime( $this->_lockFile );
//LOG::trace( 'Lock-file exists and is ' . number_format( $_age / 60, 2 ) . ' minute(s) old: ' . $lockFileName );

			return ( $_age <= $lockAge );
		}

		//	It doesn't exist
		return false;
	}

	/**
	 * Determine if a process should be blocked by another process
	 * @param string $tag
	 * @return boolean
	 */
	protected function _shouldWait( $tag )
	{
		$_tagFound = false;

		foreach ( $this->_waitList as $_tag => $_options )
		{
			if ( $tag == $this->_tag )
			{
//				if ( $this->_lockExists( $_waitList['lockFileName'], $_waitProcess['lockAge'] ) )
//				{
//					//	Lock exists, so yes, please wait...
//					return true;
//				}

//				if ( ! $_tagFound )
//				{
//					$_tagFound = true;
//				}
			}
		}

		//	No waits requested? Then we need to check our process
		if ( ! $_tagFound )
		{
			return $this->_lockExists();
		}

		return false;
	}

	/**
	 * @param string $tag
	 * @param array $options
	 * @return \Kisma\Aspects\ProcessLock
	 */
	public function addWaitListProcess( $tag, $options = array() )
	{
		$this->_waitList[$tag] = $options;
		return $this;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param string $lockFilePath
	 * @return \Kisma\ProcessLock
	 */
	public function setLockFilePath( $lockFilePath )
	{
		$this->_lockFilePath = $lockFilePath;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLockFilePath()
	{
		return $this->_lockFilePath;
	}

	/**
	 * @param string $lockFileTemplate
	 * @return \Kisma\ProcessLock
	 */
	public function setLockFileTemplate( $lockFileTemplate )
	{
		$this->_lockFileTemplate = $lockFileTemplate;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLockFileTemplate()
	{
		return $this->_lockFileTemplate;
	}

	/**
	 * @param int $pid
	 * @return \Kisma\ProcessLock
	 */
	public function setPid( $pid )
	{
		$this->_processId = $pid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPid()
	{
		return $this->_processId;
	}

	/**
	 * @param string $tag
	 * @return \Kisma\ProcessLock
	 */
	public function setTag( $tag )
	{
		$this->_tag = $tag;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTag()
	{
		return $this->_tag;
	}

	/**
	 * @param string $lockFile
	 * @return \Kisma\Aspects\ProcessLock
	 */
	protected function _setLockFile( $lockFile )
	{
		$this->_lockFile = $lockFile;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLockFile()
	{
		return $this->_lockFile;
	}

	/**
	 * @param array $waitList
	 * @return \Kisma\Aspects\ProcessLock
	 */
	public function setWaitList( $waitList )
	{
		$this->_waitList = $waitList;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getWaitList()
	{
		return $this->_waitList;
	}

	/**
	 * @param int $processId
	 * @return \Kisma\Aspects\ProcessLock
	 */
	public function setProcessId( $processId )
	{
		$this->_processId = $processId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProcessId()
	{
		return $this->_processId;
	}

	/**
	 * @param string $hostName
	 * @return \Kisma\Aspects\ProcessLock
	 */
	public function setHostName( $hostName )
	{
		$this->_hostName = $hostName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getHostName()
	{
		return $this->_hostName;
	}
}
