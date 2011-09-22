<?php
namespace Kisma;

//*************************************************************************
//*	Enums
//*************************************************************************

/**
 *
 */
class AccessLevel extends \SplEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	const __default = self::Guest;

	/**
	 * @const int Predefined Access Levels
	 */
	const None = -1;
	const Guest = 0;
	const UnconfirmedUser = 1;
	const ConfirmedUser = 2;
	const AuthorizedUser = 3;
	const Admin = 4;
}

/**
 */
class AccessorMode extends \SplEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	const __default = self::Get;

	/**
	 * @const int Property Accessor Modes
	 */
	const Has = 0;
	const Get = 1;
	const Set = 2;

	/**
	 * @const int PropertyException codes
	 */
	const Undefined = 1000;
	const WriteOnly = 1001;
	const ReadOnly = 100e2;
}

/**
 * The various predefined actions that can be used on an xlForm
 */
class ActionButton extends \SplEnum
{
	//*************************************************************************
	//* Constants 
	//*************************************************************************

	const __default = self::None;

	/**
	 * @const int The predefined action types
	 */
	const Generic = -1;
	const None = 0;
	const Create = 1;
	const Edit = 2;
	const Save = 3;
	const Delete = 4;
	const Manager = 5;
	const Lock = 6;
	const Unlock = 7;
	const Preview = 8;
	const Back = 9;
	const Cancel = 10;
}

/**
 *
 */
class OperationMode extends \SplEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	const __default = self::Development;

	/**
	 * @const int Predefined operation modes
	 */
	const Development = 0;
	const Testing = 1;
	const Integration = 2;
	const Production = 3;
}

/**
 */
class PagerLocation extends \SplEnum
{
	//*************************************************************************
	//* Constants 
	//*************************************************************************

	const __default = self::TopLeft;
	
	/**
	 * @const int Where a pager may be located 
	 */
	const TopLeft = 0;
	const TopRight = 1;
	const BottomLeft = 2;
	const BottomRight = 3;
}

/**
 */
class Seconds extends \SplEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	const __default = self::PerMinute;

	/**
	 * @const int The number of seconds in various periods
	 */
	const PerMinute = 60;
	const PerHour = 3600;
	const PerEighthDay = 10800;
	const PerQuarterDay = 21600;
	const PerHalfDate = 43200;
	const PerDay = 86400;
	const PerWeek = 604800;
}

/**
 */
class QueueStatus extends \SplEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	const __default = self::Queued;

	/**
	 * @const int The status a queued item may have
	 */
	const IgnorableError = -2;
	const Error = -1;
	const Queued = 0;
	const Processing = 1;
	const InProgress = 2;
	const CheckingStatus = 3;
	const Complete = 4;
	const Archiving = 5;
	const Archived = 6;
}

/**
 */
class LogLevel extends \SplEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	const __default = self::Info;

	/**
	 * @const int Individual log entry levels
	 */
	const Emergency = 0;
	const Alert = 1;
	const Critical = 2;
	const Error = 3;
	const Warning = 4;
	const Notice = 5;
	const Info = 6;
	const Debug = 7;
	const User = 8;

	/**
	 * @const int Log level modifiers  */
	const Auth = 0x20;
	const Syslog = 0x28;
	const AuthPriv = 0x50;

}

/**
 * Defines the available output formats for objects that return or display output.
 */
class OutputFormat extends \SplEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	const __default = self::JSON;

	/**
	 * @const int output formats
	 */
	const JSON = 0;
	const HTTP = 1;
	const Hash = 2;
	const XML = 3;
	const CSV = 4;
	const CommaSeparated = 4;
	const PSV = 5;
	const PipeSeparated = 5;
	const TSV = 6;
	const TabSeparated = 6;
}

/**
 * Defines the available output formats for objects that return or display output.
 */
class ServiceType extends \SplEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	const __default = self::AllPurpose;

	/**
	 * @const int service types
	 */
	const Sapi = 0x0000;
	const Cli = 0x0001;
	const AllPurpose = 0x0003;
	const Ajax = 0x0004;
}

/**
 * Defines the available validation modes.
 */
class ValidationMode extends \SplEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @const int The modes of validation
	 */
	const __default = self::AllFields;
	const AllFields = 0;
	const OneField = 1;
	const StopOnError = 2;
}

/**
 * Validation types and modifiers
 */
class ValidationType extends \SplEnum
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @const int The validations of Kisma
	 */
	const Alpha = 0x0001;
	const Numeric = 0x0002;
	const Alphanumeric = 0x0003;
	const Within = 0x0004;
	const Between = 0x0008;
	const Date = 0x0010;
	const DateTime = 0x0020;
	const Character = 0x0040;
	const Digit = 0x0080;
	const MaxLength = 0x0800;
	const MinLength = 0x1000;
	const In = 0x2000;

	/**
	 * @const int Modifiers
	 */
	const EqualTo = 0xffff0001;
	const NotEqualTo = 0xffff0002;
	const GreaterThan = 0xffff0004;
	const LessThan = 0xffff0008;
	const GreaterThanOrEqualTo = 0xffff0005;
	const LessThanOrEqualTo = 0xffff0009;
}
