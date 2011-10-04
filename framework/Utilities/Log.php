<?php
/**
 * Log.php
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link		  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license	   http://github.com/Pogostick/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @category	  Kisma_Utilities
 * @package	   kisma.utilities
 * @namespace	 \Kisma\Utilities
 * @since		 v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Utilities
 */
namespace Kisma\Utilities
{
	/**
	 * Log
	 * It's better than bad! It's GOOD! All kids love Log!
	 */
	class Log extends \Kisma\Components\SubComponent implements \Kisma\IUtility
	{
		//********************************************************************************
		//* Private Members
		//********************************************************************************

		/**
		 * @var boolean If true, all applicable log entries will be echoed to STDOUT as well as logged
		 */
		protected static $_echoData = false;
		/**
		 * @var string Prepended to each log entry before writing.
		 */
		protected static $_prefix = null;
		/**
		 * @var integer The base level for getting source of log entry
		 */
		protected static $_baseLevel = 2;
		/**
		 * @var integer The current indent level
		 */
		protected static $_currentIndent = 0;
		/**
		 * @var string
		 */
		protected static $_defaultLevelIndicator = '.';
		/**
		 * @var array
		 */
		protected static $_levelIndicators = array(
			'info'    => '*',
			'notice'  => '?',
			'warning' => '-',
			'error'   => '!',
		);

		//********************************************************************************
		//* Public Methods
		//********************************************************************************

		/**
		 * Creates an 'info' log entry
		 *
		 * @param string $message The message to log
		 * @param string $tag The message category. Please use only word letters. Note, category 'yii' is reserved for Yii framework core
		 * code use. See {@link CPhpMessageSource} for more interpretation about message category.
		 * @param string $level The message level
		 * @return bool
		 */
		public static function log( $message, $tag = null, $level = 'info' )
		{
			if ( null === $tag )
			{
				$tag = self::_getCallingMethod();
			}

			//	Get the indent, if any
			$_unindent  = ( 0 > ( $_newIndent = self::_processMessage( $message ) ) );

			$_levelList = explode( '|', $level );

			//	Handle writing to multiple levels at once.
			foreach ( $_levelList as $_level )
			{
				$_indicator = \K::o( self::$_levelIndicators, $_level, self::$_defaultLevelIndicator );
				$_logEntry  = self::$_prefix . $message;

				if ( self::$_echoData )
				{
					echo date( 'Y.m.d h.i.s' ) .
						'[' . strtoupper( $_level[0] ) . '] ' .
						'[' . sprintf( '%-40s', $tag = null ) . '] ' .
						$_logEntry . '<br />';

					flush();
				}

				//	Indent...
				$_tempIndent = self::$_currentIndent;

				if ( $_unindent )
				{
					$_tempIndent--;
				}

				if ( $_tempIndent < 0 )
				{
					$_tempIndent = 0;
				}

				$_logEntry = str_repeat( '  ', $_tempIndent ) . $_indicator . ' ' . $message;

				\K::log( $_logEntry, $tag, $_level );
			}

			//	Set indent level...
			self::$_currentIndent += $_newIndent;

			return ( 'error' != $level );
		}

		/**
		 * Creates an 'info' log entry
		 * @param mixed $tag The message category. Please use only word letters. Note, category 'yii' is reserved for Yii framework core
		 * code use. See {@link CPhpMessageSource} for more interpretation about message category.
		 * @param mixed $message The message to log
		 */
		public static function info( $message, $tag = null )
		{
			self::log( $message, $tag, 'info' );
		}

		/**
		 * Creates an 'error' log entry
		 * @param mixed $tag The message category. Please use only word letters. Note, category 'yii' is reserved for Yii framework core
		 * code use. See {@link CPhpMessageSource} for more interpretation about message category.
		 * @param mixed $message The message to log
		 */
		public static function error( $message, $tag = null )
		{
			self::log( $message, $tag, 'error' );
		}

		/**
		 * Creates an 'warning' log entry
		 * @param mixed $tag The message category. Please use only word letters. Note, category 'yii' is reserved for Yii framework core
		 * code use. See {@link CPhpMessageSource} for more interpretation about message category.
		 * @param mixed $message The message to log
		 * @param mixed $options Parameters to be applied to the message using <code>strtr</code>.
		 * @param mixed $source Which message source application component to use.
		 * @param mixed $language The target language. If null (default), the {@link CApplication::getLanguage application language} will be used.
		 */
		public static function warning( $message, $tag = null )
		{
			self::log( $message, $tag, 'warning' );
		}

		/**
		 * Creates an 'trace' log entry
		 * @param mixed $tag The message category. Please use only word letters. Note, category 'yii' is reserved for Yii framework core
		 * code use. See {@link CPhpMessageSource} for more interpretation about message category.
		 * @param mixed $message The message to log
		 * @param mixed $options Parameters to be applied to the message using <code>strtr</code>.
		 * @param mixed $source Which message source application component to use.
		 * @param mixed $language The target language. If null (default), the {@link CApplication::getLanguage application language} will be used.
		 */
		public static function trace( $message, $tag = null )
		{
			self::log( $message, $tag, 'trace' );
		}

		/**
		 * Creates an 'api' log entry
		 * @param string $apiCall The API call made
		 * @param mixed $response The API response to log
		 */
		public static function api( $apiCall, $response = null )
		{
			self::log( PHP_EOL . print_r( $response, true ) . PHP_EOL, $apiCall, 'api' );
		}

		/**
		 * Creates a 'debug' log entry
		 * @param mixed $tag The message category. Please use only word letters. Note, category 'yii' is reserved for Yii framework core
		 * code use. See {@link CPhpMessageSource} for more interpretation about message category.
		 * @param mixed $message The message to log
		 */
		public static function debug( $message, $tag = null )
		{
			self::log( $message, $tag, 'debug' );
		}

		/**
		 * Returns the name of the method that made the call
		 * @param integer $level The level of the call
		 * @return string
		 */
		protected static function _getCallingMethod( $level = 1 )
		{
			$_className = null;

			//	Increment by one to account for myself
			$level++;

			$_backTrace = debug_backtrace();
			$_function = \K::o( $_backTrace[$level], 'method', \K::o( $_backTrace[$level], 'function', '__unknown__' ) );
			$_class = \K::o( $_backTrace[$level], 'class' );

			return ( null !== $_class ? $_class . \K::o( $_backTrace[$level], 'type' ) : null ) . $_function;
		}

		/**
		 * Safely decrements the current indent level
		 * @param int $howMuch
		 */
		public static function decrementIndent( $howMuch = 1 )
		{
			self::$_currentIndent -= $howMuch;

			if ( self::$_currentIndent < 0 )
			{
				self::$_currentIndent = 0;
			}
		}

		//*************************************************************************
		//* Protected Methods 
		//*************************************************************************

		/**
		 * Processes the indent level for the messages
		 * @param string $message
		 * @return integer The indent difference AFTER this message
		 */
		protected static function _processMessage( &$message )
		{
			$_newIndent = 0;

			switch ( substr( $message, 0, 2 ) )
			{
				case '>>':
					$_newIndent = 1;
					$message    = trim( substr( $message, 2 ) );
					break;

				case '<<':
					$_newIndent = -1;
					$message    = trim( substr( $message, 2 ) );
					break;
			}

			return $_newIndent;
		}

		/**
		 * @static
		 * @param $baseLevel
		 */
		public static function setBaseLevel( $baseLevel = 2 )
		{
			self::$_baseLevel = $baseLevel;
		}

		/**
		 * @static
		 * @return int
		 */
		public static function getBaseLevel()
		{
			return self::$_baseLevel;
		}

		/**
		 * @static
		 * @param $currentIndent
		 */
		public static function setCurrentIndent( $currentIndent = 0 )
		{
			self::$_currentIndent = $currentIndent;
		}

		/**
		 * @static
		 * @return int
		 */
		public static function getCurrentIndent()
		{
			return self::$_currentIndent;
		}

		/**
		 * @param string $defaultLevelIndicator
		 */
		public static function setDefaultLevelIndicator( $defaultLevelIndicator = '.' )
		{
			self::$_defaultLevelIndicator = $defaultLevelIndicator;
		}

		/**
		 * @return string
		 */
		public static function getDefaultLevelIndicator()
		{
			return self::$_defaultLevelIndicator;
		}

		/**
		 * @static
		 * @param $echoData
		 */
		public static function setEchoData( $echoData = false )
		{
			self::$_echoData = $echoData;
		}

		/**
		 * @static
		 * @return bool
		 */
		public static function getEchoData()
		{
			return self::$_echoData;
		}

		/**
		 * @param array $levelIndicators
		 */
		public static function setLevelIndicators( $levelIndicators )
		{
			self::$_levelIndicators = $levelIndicators;
		}

		/**
		 * @return array
		 */
		public static function getLevelIndicators()
		{
			return self::$_levelIndicators;
		}

		/**
		 * @static
		 * @param $prefix
		 */
		public static function setPrefix( $prefix = null )
		{
			self::$_prefix = $prefix;
		}

		/**
		 * @static
		 * @return null|string
		 */
		public static function getPrefix()
		{
			return self::$_prefix;
		}
	}
}