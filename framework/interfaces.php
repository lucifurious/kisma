<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license			http://github.com/Pogostick/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @category		Kisma_Interfaces
 * @package			kisma
 * @namespace		\Kisma
 * @since			v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * Global namespace pollution
 */
namespace
{
	/**
	 * A special global interface for use with our K alias
	 */
	interface KismaSettings
	{
		//*************************************************************************
		//* Constants
		//*************************************************************************

		/**
		 * @const string Kisma's base path
		 */
		const BasePath = 'kisma.base_path';
		/**
		 * @const string The default application event class
		 */
		const DefaultEventClass = 'kisma.components.event';
		/**
		 * @const string The default application event class
		 */
		const DefaultAppEventClass = 'kisma.aspects.reactors.app_event';
		/**
		 * @const string The default object storage class
		 */
		const DefaultObjectStorageClass = 'kisma.aspects.storage.object_storage';
		/**
		 * @const string The default CouchDb aspect class
		 */
		const CouchDbClass = 'kisma.aspects.storage.couch_db';
	}

	/**
	 * These constants are the names of the option keys used by Kisma
	 */
	interface KismaOptions
	{
		//*************************************************************************
		//* Constants
		//*************************************************************************

		/**
		 * @const string
		 */
		const AutoBindEvents = 'auto_bind_events';
		const AutoBindOptions = 'auto_bind_options';

		const IgnoreEvents = 'ignore_events';

		const AppEventClass = 'app_event_class';
		const ObjectStorageClass = 'object_storage_class';

		const AspectOptions = 'aspect.options';
		const ComponentOptions = 'component.options';
		const Options = 'options';

		const CreateIfNotFound = 'create_if_not_found';
	}

	/**
	 * A special global interface for use with our K alias
	 * Provides HTTP method names
	 */
	interface HttpMethods
	{
		//*************************************************************************
		//* Constants
		//*************************************************************************

		/**
		 * @const string
		 */
		const Get = 'GET';
		/**
		 * @const string
		 */
		const Post = 'POST';
		/**
		 * @const string
		 */
		const Put = 'PUT';
		/**
		 * @const string
		 */
		const Delete = 'DELETE';
		/**
		 * @const string
		 */
		const Head = 'HEAD';
	}

}

/**
 * The Kisma Interfaces
 */
namespace Kisma
{
	//*************************************************************************
	//*	Foundation Interfaces
	//*************************************************************************

	/**
	 *
	 */
	interface IKisma
	{
	}

	/**
	 * This interface is for components that support debugging levels
	 */
	interface IObservable extends IKisma
	{
		//*************************************************************************
		//* Requirements
		//*************************************************************************

		/**
		 * Gets the debug level
		 * @return integer
		 */
		public function getLogging();

		/**
		 * Sets the debug level
		 * @param $logging
		 *
		 * @internal param $ #P#C\kisma\interfaces\IObservable.DEVELOPMENT|int|? $value
		 * @return integer The previous value
		 */
		public function setLogging( $logging );
	}

	/**
	 *
	 */
	interface IAspectable extends IObservable
	{
		//*************************************************************************
		//* Requirements
		//*************************************************************************

		/**
		 * @abstract
		 * @param string $aspectName
		 * @param bool $returnAspect If true, the aspect object will be returned instead of the name
		 * @return false|string
		 */
		public function hasAspect( $aspectName, $returnAspect = false );

		/**
		 * @abstract
		 * @return \Kisma\IAspect[]|array
		 */
		public function getAspects();

		/**
		 * @abstract
		 * @param \Kisma\IAspect[]|array $value
		 * @return \Kisma\IAspect[]|array
		 */
		public function setAspects( $value );
	}

	/**
	 * Defines an object that has options
	 */
	interface IOptions extends IKisma
	{
		/**
		 * Gets all options
		 * @return array
		 */
		public function getOptions();

		/**
		 * Sets an array of options at once
		 * @param array $options
		 * @return \Kisma\IOptions
		 */
		public function setOptions( $options = array() );

	}

	/**
	 * Defines an object that has static options
	 */
	interface ISettings extends IKisma
	{
		/**
		 * Gets a Kisma setting
		 *
		 * @param string $name
		 * @param null $defaultValue
		 * @return array
		 */
		public static function getSetting( $name, $defaultValue = null );

	}

	/**
	 * This interface defines a configurable object
	 */
	interface IConfigurable extends IOptions
	{
		/**
		 * Gets a configuration option
		 *
		 * @param string $name
		 * @param null $defaultValue
		 * @return array
		 */
		public function getOption( $name, $defaultValue = null );

		/**
		 * Sets a single option
		 *
		 * @param string $name
		 * @param mixed|null $value
		 * @return mixed
		 */
		public function setOption( $name, $value = null );
	}

	/**
	 * Defines a data model class
	 */
	interface IDataModel extends IKisma
	{
	}

	/**
	 * The reactor interface defines an aspect that reacts to events.
	 */
	interface IReactor extends IKisma
	{
	}

	/**
	 * Defines an object that is an Aspect
	 */
	interface IAspect extends IConfigurable, IObservable
	{
		/**
		 * Returns the currently linked parent
		 *
		 * @param \Kisma\IAspectable $linker
		 * @return \Kisma\IAspect
		 */
		public function setLinker( IAspectable $linker );

		/**
		 * Returns the currently linked parent
		 * @return Components\Component
		 */
		public function getLinker();
	}

	/**
	 * The dispatcher interface defines an aspect that dispatches events.
	 */
	interface IDispatcher extends IAspect
	{
		//*************************************************************************
		//* Requirements
		//*************************************************************************

		/**
		 * Bind a callback to an event
		 * @param string $eventName
		 * @param callback $callback
		 * @return boolean
		 */
		public function bind( $eventName, $callback );

		/**
		 * Unbind from an event
		 *
		 * @param string $eventName
		 * @param callback $callback
		 * @return boolean
		 */
		public function unbind( $eventName, $callback );

		/**
		 * Returns a standardized event name if this component has the requested
		 * event, otherwise false
		 *
		 * @param string $eventName The name of the event
		 * @param bool $returnEvent If true, the event object will be returned instead of the name
		 * @return false|string|\Kisma\Components\Event
		 */
		public function hasEvent( $eventName, $returnEvent = false );

		/**
		 * Triggers an event
		 *
		 * @param string $eventName
		 * @param mixed|null $eventData
		 * @param callback|null $callback
		 * @return bool Returns true if the $eventName has no handlers.
		 */
		public function trigger( $eventName, $eventData = null, $callback = null );
	}

	/**
	 * Defines the interface for components
	 */
	interface IComponent extends IConfigurable, IAspectable
	{
	}

	/**
	 *
	 */
	interface IModel extends IComponent
	{
	}

	/**
	 * This interface is for components that support debugging levels
	 */
	interface IGlobalDebuggable extends IKisma
	{
		//*************************************************************************
		//* Requirements
		//*************************************************************************

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
	interface IException extends IComponent
	{
	}

	/**
	 * Indicates that the component is a module
	 */
	interface IModule extends IComponent
	{
	}

	/**
	 * Indicates that the component provides access to a service
	 */
	interface IService extends IComponent
	{
	}

	/**
	 * This identifies an object as a streamable object
	 */
	interface IStreamable extends IComponent
	{
	}

	/**
	 * This interface defines an object as a provider of constant values
	 */
	interface IConstantProvider  extends IKisma
	{
	}

	/**
	 * An interface for event classes
	 */
	interface IEvent extends IComponent
	{
	}

	//*************************************************************************
	//*	Constant Providers
	//*************************************************************************

	/**
	 * This interface defines an object as an access control object
	 */
	interface IAccess extends IConstantProvider
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
	interface IPagerLocation extends IConstantProvider
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
	interface IFormAction extends IConstantProvider
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
	interface IForm extends IPagerLocation, IFormAction
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
	interface ISeconds extends IConstantProvider
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
	interface IStatus extends IConstantProvider
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
	interface ILockable extends IConstantProvider
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
	interface ILog extends IConstantProvider
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
	interface ITransform extends IConstantProvider
	{
		/**
		 * @param mixed|object|array $input
		 * @param mixed|null $options
		 * @return mixed
		 */
	//	public function transform( $input, $options = null );
	}

	/**
	 * This interface defines the output transformations
	 */
	interface IOutputFormat extends ITransform
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
	interface IUtility extends IConstantProvider
	{
	}

	//*************************************************************************
	//*	Aspects
	//*************************************************************************

	/**
	 *
	 */
	interface IStorage extends IAspect
	{
	}

	/**
	 *
	 */
	interface IRouter extends IAspect
	{
	}

	/**
	 * Provides constants for controller classes
	 */
	interface IController extends IRouter
	{
	}
}
