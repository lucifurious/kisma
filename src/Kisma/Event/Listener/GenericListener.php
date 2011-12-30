<?php
/**
 * @file
 * A generic event listener
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2009-2011, Jerry Ablan/Pogostick, LLC., All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan/Pogostick, LLC.
 * @license http://github.com/Pogostick/Kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Framework
 * @package kisma
 * @since 1.0.0
 *
 * @ingroup framework
 */

namespace Kisma\Event\Listener;

//*************************************************************************
//* Aliases
//*************************************************************************

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kisma\Event as Event;

//*************************************************************************
//* Requirements
//*************************************************************************

/**
 * Kisma generic event listener
 */
class GenericListener implements EventSubscriberInterface
{
	//*************************************************************************
	//* Private Members 
	//*************************************************************************

	/**
	 * @var LoggerInterface
	 */
	protected $_logger;

	//*************************************************************************
	//* Public Methods 
	//*************************************************************************

	/**
	 * @param \Kisma\Kisma                                           $app
	 * @param null|\Symfony\Component\HttpKernel\Log\LoggerInterface $logger
	 */
	public function __construct( \Kisma\Kisma $app, $logger = null )
	{
		$this->_logger = $logger;

		$app['kisma_dispatcher'] = $app->share( function() use( $app )
		{
			$_dispatcher = $app[\Kisma\Kisma::Dispatcher];
			$dispatcher->addSubscriber($app);

   $urlMatcher = new LazyUrlMatcher(function () use ($app) {
       return $app['url_matcher'];
   });
   $dispatcher->addSubscriber(new RouterListener($urlMatcher));

   return $dispatcher;

		} );
	}

	/**
	 * @param \Kisma\Event\ApplicationEvent $event
	 *
	 * @return mixed
	 */
	public function onInitialize( Event\ApplicationEvent $event )
	{
		if ( false === $event->getApp()->initialize() )
		{
			$event->stopPropagation();
		}
	}

	/**
	 * @param \Kisma\Event\ApplicationEvent $event
	 *
	 * @return mixed
	 */
	public function onTerminate( Event\ApplicationEvent $event )
	{
		$event->getApp()->terminate();
	}

	/**
	 * Sets up event handlers
	 *
	 * @static
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return array(
			//	Initialization event
			Event\ApplicationEvent::INITIALIZE => array(
				array(
					'onInitialize', 32
				)
			),

			//	Terminate event
			Event\ApplicationEvent::TERMINATE => array(
				array(
					'onTerminate', 32
				)
			),
		);
	}
}
