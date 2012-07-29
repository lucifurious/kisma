<?php
/**
 * JobEvents.php
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 *         Copyright (c) 2012 Silverpop Systems, Inc.
 *         http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 * @filesource
 */
namespace Kisma\Core\Interfaces;

use CIS\Utility\Curl;

/**
 * JobEvents
 * Defines an interface the Api class knows how to deal with
 */
interface JobEvents
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const BeforeJobRun = 'cis.integration.before_job_run';
	/**
	 * @var string
	 */
	const AfterJobRun = 'cis.integration.after_job_run';
	/**
	 * @var string
	 */
	const AfterFailedJobRun = 'cis.integration.after_failed_job_run';
	/**
	 * @var string
	 */
	const AfterSuccessfulJobRun = 'cis.integration.after_successful_job_run';

	//**************************************************************************
	//* Event Handlers
	//**************************************************************************

	/**
	 * @abstract
	 *
	 * @param JobEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeJobRun( $event );

	/**
	 * @abstract
	 *
	 * @param JobEvent $event
	 *
	 * @return bool
	 */
	public function onAfterJobRun( $event );

	/**
	 * @abstract
	 *
	 * @param JobEvent $event
	 *
	 * @return bool
	 */
	public function onAfterFailedJobRun( $event );

	/**
	 * @abstract
	 *
	 * @param JobEvent $event
	 *
	 * @return bool
	 */
	public function onAfterSuccessfulJobRun( $event );

}
