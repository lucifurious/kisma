<?php
/**
 * EventHandler.php
 *
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
 * @category		Kisma_Components
 * @package			kisma.components
 * @since			v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Components
 */
namespace Kisma\Components
{
	//*************************************************************************
	//* Imports
	//*************************************************************************

	use \Kisma\Utility as Utility;

	/**
	 * Event
	 * The mother of all events!
	 */
	class EventHandler extends SubComponent
	{
		//*************************************************************************
		//* Private Members
		//*************************************************************************

		/**
		 * @var string The handler ID
		 */
		protected $_handlerId = null;
		/**
		 * @var callback The callback
		 */
		protected $_callback = null;
		/**
		 * @var mixed The data to pass to the callback
		 */
		protected $_callbackData = null;

		/**
		 * Encapsulate calling an event handler
		 * @param \Kisma\Components\Event $event
		 * @return mixed
		 */
		public function handle( $event )
		{
			Utility\Log::debug( 'Handling "' . $event->getEventName() . '" with handler ID: ' . $this->_handlerId );

			return call_user_func_array(
				$this->_callback,
				array(
					$event,
					$this->_callbackData,
				)
			);

		}

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param callback $callback
		 * @return $this
		 */
		public function setCallback( $callback )
		{
			$this->_callback = $callback;
			$this->_handlerId = spl_object_hash( (object)$callback );
			return $this;
		}

		/**
		 * @return callback
		 */
		public function getCallback()
		{
			return $this->_callback;
		}

		/**
		 * @param mixed $callbackData
		 * @return $this
		 */
		public function setCallbackData( $callbackData )
		{
			$this->_callbackData = $callbackData;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getCallbackData()
		{
			return $this->_callbackData;
		}

		/**
		 * @return string
		 */
		public function getHandlerId()
		{
			return $this->_handlerId;
		}
	}
}