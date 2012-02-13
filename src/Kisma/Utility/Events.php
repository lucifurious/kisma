<?php
/**
 * @file
 * Provides event manipulation utilities
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Utilities
 * @package kisma.utility
 * @since 1.0.0
 *
 * @ingroup utilities
 */

namespace Kisma\Utility;

/**
 * Property
 * Provides event manipulation routines
 */
class Events implements \Kisma\IUtility
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The default event handler signature
	 */
	const DefaultEventHandlerSignature = 'on';

	//*************************************************************************
	//* Public Methods 
	//*************************************************************************

	/**
	 * Wires up any event handlers automatically
	 *
	 * @param \Kisma\Components\Seed|\Silex\Application		 $object
	 * @param array|null										$listeners
	 * @param string											$signature
	 *
	 * @return
	 */
	public static function subscribe( $object, $listeners = null, $signature = self::DefaultEventHandlerSignature )
	{
		//	No event dispatcher? No subscriptions...
		/** @var $_dispatcher \Symfony\Component\EventDispatcher\EventDispatcher */
		if ( null === ( $_dispatcher = \Kisma\K::app( 'dispatcher' ) ) )
		{
			return;
		}

		//	Allow for passed in listeners
		if ( null === ( $_listeners = $listeners ) )
		{
			$_listeners = self::discover( $object, $signature );
		}

		//	Nothing to do? Bail
		if ( !is_array( $_listeners ) || empty( $_listeners ) )
		{
			return;
		}

		//	And wire them up...
		foreach ( $_listeners as $_eventName => $_callback )
		{
			$_dispatcher->addListener(
				$_eventName,
				function( \Symfony\Component\EventDispatcher\Event $event ) use( $_callback )
				{
					if ( false === call_user_func( $_callback, $event ) )
					{
						$event->stopPropagation();
					}
				}
			);
		}
	}

	/**
	 * Builds a hash of events and handlers that are present in this object based on the event handler signature.
	 * This merely builds the hash, nothing is done with it.
	 *
	 * @param		$object
	 * @param string $signature
	 *
	 * @internal param bool $appendToList
	 *
	 * @return array
	 */
	public static function discover( $object, $signature = self::DefaultEventHandlerSignature )
	{
		$_listeners = array();

		$_mirror = new \ReflectionClass( $object );
		$_class = $_mirror->name;

		//	Check each method for the event handler signature
		foreach ( $_mirror->getMethods() as $_method )
		{
			//			if ( $_class != $_method->class )
			//			{
			//				continue;
			//			}

			$_realMethodName = $_method->name;
			$_length = strlen( $signature );
			$_stub = substr( $_realMethodName, 0, $_length );

			if ( 0 == strcasecmp( $_stub, $signature ) )
			{
				$_eventKey = \Kisma\Utility\Inflector::untag( substr( $_realMethodName, $_length ) );

				//	Map the callback to the key
				$_listeners[$_eventKey] = array( $object, $_realMethodName );
			}
		}

		//	Return the current map
		return $_listeners;
	}

}
