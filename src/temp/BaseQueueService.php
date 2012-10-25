<?php
/**
 * BaseQueueService.php
 *
 * Copyright (c) 2012 Silverpop Systems, Inc.
 * http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 * @filesource
 */
namespace CIS\Services;

/**
 * BaseQueueService
 * The base class for delivery services.
 *
 * Base properties:
 *
 * @property \CIS\Services\ServiceSettings $settings
 */
abstract class BaseQueueService extends \CIS\Services\BaseService
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Queues a job(s)
	 *
	 * @param \CIS\Components\BaseJob $job
	 *
	 * @return mixed
	 */
	abstract public function enqueue( $job );

	/**
	 * Dequeues a job(s)
	 *
	 * @param array $parameters
	 *
	 * @return \CIS\Components\BaseJob
	 */
	abstract public function dequeue( $parameters = array() );

}
