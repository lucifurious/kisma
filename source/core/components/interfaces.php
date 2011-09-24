<?php
/**
 * YiiXL(tm) : The Yii Extension Library of Doom! (http://github.com/Pogostick/yiixl/)
 * Copyright 2009-2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://www.pogostick.com/licensing/} for complete information.
 *
 * @copyright		Copyright 2009-2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			https://github.com/Pogostick/yiixl/ The Yii Extension Library of Doom!
 * @license			http://www.pogostick.com/licensing
 * @author			Jerry Ablan <yiixl@pogostick.com>
 *
 * @package			yiixl
 * @category		yiixl
 * @since			v1.0.0
 *
 * @brief 			This file contains all the YiiXL interfaces
 *
 * @filesource
 */
namespace kisma\interfaces;

//*************************************************************************
//*	Foundation Interfaces
//*************************************************************************

/**
 * This interface defines constants for, and identifies an object as, a YiiXL component
 */
interface IKisma
{
}

/**
 * This interface is for components that support debugging levels
 */
interface kIDebuggable extends kIComponent
{
	/**
	 * Gets the debug level
	 * @return integer
	 */
	public function getDebugLevel();

	/**
	 * Sets the debug level
	 * @param integer $value
	 * @return integer The previous value
	 */
	public function setDebugLevel( $value );

}

/**
 * This interface is for components that support debugging levels
 */
interface kIGlobalDebuggable extends kIComponent
{
	/**
	 * Gets the debug level
	 * @return integer
	 */
	public static function getDebugLevel();

	/**
	 * Sets the debug level
	 * @param integer $value
	 * @return integer The previous value
	 */
	public static function setDebugLevel( $value );

}

/**
 * This interface does nothing other than indicate that an exception is
 * part of the YiiXL package.
 */
interface kIException extends kIComponent
{
}

/**
 * This identifies an object as a streamable object
 */
interface kIStreamable extends kIComponent, kIDebuggable
{
}

/**
 * This interface defines an object as a provider of constant values
 */
interface kIConstantProvider  extends kIComponent
{
}

//*************************************************************************
//*	Constant Providers
//*************************************************************************

/**
 * This interface defines an object as an access control object
 */
interface kIAccess extends kIConstantProvider
{
	/**
	 * @const integer The available access levels for access filtering/control
	 */
	const
		ACCESS_TO_NONE = -1,
		ACCESS_TO_ANY = 0,
		ACCESS_TO_GUEST = 1,
		ACCESS_TO_AUTH = 2,
		ACCESS_TO_ADMIN = 3,
		ACCESS_TO_ADMIN_LEVEL_0 = 3,
		ACCESS_TO_ADMIN_LEVEL_1 = 4,
		ACCESS_TO_ADMIN_LEVEL_2 = 5
	;
}

/**
 * Constant provider of where a pager can be placed in relation to a grid/data view
 */
interface kIPagerLocation extends kIConstantProvider
{
	/**
	* @const integer Where a pager can be placed in relation to a grid view
	*/
	const
		PL_TOP_LEFT		= 0,
		PL_TOP_RIGHT	= 1,
		PL_BOTTOM_LEFT	= 2,
		PL_BOTTOM_RIGHT	= 3
	;
}

/**
 * The various predefined actions that can be used on an xlForm
 */
interface kIFormAction extends kIConstantProvider
{
	/***
	* @const integer The predefined action types for xlForm
	*/
	const
		ACTION_NONE 	= 0,
		ACTION_CREATE 	= 1,
		ACTION_VIEW 	= 2,
		ACTION_EDIT 	= 3,
		ACTION_SAVE 	= 4,
		ACTION_DELETE 	= 5,
		ACTION_ADMIN 	= 6,
		ACTION_LOCK 	= 7,
		ACTION_UNLOCK 	= 8,
		ACTION_PREVIEW 	= 996,
		ACTION_RETURN 	= 997,
		ACTION_CANCEL 	= 998,
		ACTION_GENERIC 	= 999
	;

}

/**
 * Constants for forms
 */
interface kIForm extends kIPagerLocation, kIFormAction
{
	/**
	* @const integer The number of items to display per page
	*/
	const
		PAGE_SIZE = 10
	;

	/**
	 * @const string The name of our command form field
	 */
	const
		COMMAND_FIELD_NAME = '__yxl'
	;

	/**
	 * @const string Standard search text for rendering
	 */
	const
		SEARCH_HELP_TEXT =<<<HTML
You may optionally enter a comparison operator (<strong>&lt;</strong>, <strong>&lt;=</strong>, <strong>&gt;</strong>, <strong>&gt;=</strong>, <strong>&lt;&gt;</strong>or <strong>=</strong>) at the beginning of each
search value to specify how the comparison should be done.
HTML;
}

/**
 * This interface defines a logger
 */
interface kISeconds extends kIConstantProvider
{
	/**
	 * The number of seconds in various periods
	 */
	const
		PER_DAY = 86400,
		PER_HALF_DAY = 43200,
		PER_QUARTER_DAY = 21600,
		PER_EIGHTH_DAY = 10800
	;

}

/**
 * This interface defines status constants
 */
interface kIStatus extends kIConstantProvider
{
	/**
	 * The number of seconds in various periods
	 */
	const
		IGNORED = -2, 				//	Don't care
		ERROR = -1, 				//	Error row
		QUEUED = 0, 				//	Freshness
		PROCESSING = 1, 			//	Tagged for processing
		IN_PROGRESS = 2,			//	Job queued, waiting for completion
		CHECKING_STATUS = 3,		//	Job in progress, running/waiting for processing
		COMPLETE = 4, 				//	File processed, ready for archive
		ARCHIVING = 5, 				//	File archiving in process
		ARCHIVED = 6 				//	Job archived
	;

}

/**
 * This interface defines a lock file user
 */
interface kILockable extends kIConstantProvider
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @const string The default path for storing lock files
	 */
	const
		DEFAULT_LOCK_FILE_PATH = '/tmp'
	;

	/**
	 * @const string The template for lock files
	 */
	const
		DEFAULT_LOCK_FILE_TEMPLATE = '%%appName%%.%%serviceName%%.%%host%%.lock'
	;

	/**
	 * @const int The amount of time (in seconds) to let an existing lock slide before bitching
	 */
	const
		DEFAULT_LOCK_AGE = 60
	;

	//*************************************************************************
	//* Lock Methods
	//*************************************************************************

	/**
	 * @param string $host
	 * @param string $appName
	 * @param string $serviceName
	 * @param string $lockFileTemplate
	 * @return string
	 */
	function _createLockFileName( $host, $appName, $serviceName, $lockFileTemplate );
	/**
	 * Creates a lock file
	 * @param bool $noLock
	 */
	function _createLockFile( $noLock );
	/**
	 * @param string $lockFileName
	 * @param int $lockAge
	 */
	function _lockExists( $lockFileName, $lockAge );
	/**
	 */
	function _destroyLockFile();

}

/**
 * This interface defines a logger
 */
interface kILog extends kIConstantProvider
{
	/**
	 * Logging Constants
	 */
	const
		LOG_EMERG = 0,
		LOG_ALERT = 1,
		LOG_CRIT = 2,
		LOG_ERR = 3,
		LOG_WARNING = 4,
		LOG_NOTICE = 5,
		LOG_INFO = 6,
		LOG_DEBUG = 7,
		LOG_USER = 8,
		LOG_AUTH = 32,
		LOG_SYSLOG = 40,
		LOG_AUTHPRIV = 80;
}

/**
 * This interface defines the base transformation formats
 */
interface kITransform extends kIConstantProvider
{
}

/**
 * This interface defines the output transformations
 */
interface kIOutputFormat extends kITransform
{
	/**
	* @const int output formats
	*/
	const
		JSON 			= 0,		//	JSON
		HTTP 			= 1,		//	HTTP
		ASSOC_ARRAY 	= 2,		//	Associative array
		XML		 		= 3,		//	XML
		CSV				= 4,		//	Comma-separated values
		PSV				= 5			//	Pipe-separated values
	;
}

/**
 * A utility constants interface
 */
interface kIUtility extends kIConstantProvider
{
}

//*************************************************************************
//* Meaty Interfaces
//*************************************************************************

/**
 * This interface defines a configurable object
 */
interface kIConfigurable extends kIDebuggable
{
	/**
	 * Gets the configuration options
	 * @return array
	 */
	public function getOptions();

	/**
	 * Gets the configuration options
	 * @param array $options
	 * @return kIConfigurable
	 */
	public function setOptions( $options = array() );

	/**
	 * Loads the configuration options
	 * @param array $options
	 * @return kIConfigurable
	 */
	public function loadOptions( $options = array() );
}

/**
 * An interface for event classes
 */
interface kIEvent extends kIComponent, kILog
{
}

/**
 * This interface indicates that a class is a behavior in YiiXL
 */
interface kIBehavior extends IBehavior
{
	/**
	 * Bind a callback to an event
	 * @abstract
	 * @param kIEvent|kIComponent $caller
	 * @param string|null $eventName
	 * @param callback|null $callback
	 * @return boolean
	 */
	public function bind( $caller, $eventName = null, $callback = null );

	/**
	 * Unbind from an event
	 * @abstract
	 * @param string $eventName
	 * @return boolean
	 */
	public function unbind( $eventName );
}

/**
 * This interface defines constants for, and identifies an object as, a YiiXL component
 */
interface kIApplicationComponent extends kIConfigurable
{
}

//*************************************************************************
//*	Helpers
//*************************************************************************

/**
 * This interface defines a static helper class that can be mixed into the main
 * YiiXL system.
 */
interface kIHelper extends kIConfigurable
{
	/**
	 * Initializes the helper
	 */
	public static function initialize();
}

/**
 * This identifies a class as an object shifter.
 *
 * Object shifters are typical static function providers (helpers) that
 * shift the first argument off the parameter stack and use it as the target
 * object of the method called.
 *
 * Obviously, the sender must unshift itself onto the stack. The {@link YiiXL}
 * class provides methods to do this.
 *
 * @see YiiXL
 */
interface kIShifter extends kIHelper
{
}

/**
 * This interface defines a static UI helper class that can be mixed into the main
 * YiiXL system. It extends most constant providers
 */
interface kIUIHelper extends kIHelper, kIForm
{
}

/**
 * Provides constants for controller classes
 */
interface kIController extends kIDebuggable, kIForm
{
}

