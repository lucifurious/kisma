<?php
/**
 * ServiceEvents.php
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 *         Copyright (c) 2012 Silverpop Systems, Inc.
 *         http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 * @filesource
 */
namespace Kisma\Core\Interfaces;

/**
 * ServiceEvents
 * Defines an interface the base service class knows how to deal with
 */
interface ServiceEvents
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var string
     */
    const BeforeServiceCall = 'cis.services.before_service_call';
    /**
     * @var string
     */
    const AfterServiceCall = 'cis.services.after_service_call';

}
