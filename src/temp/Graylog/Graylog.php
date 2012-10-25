<?php
/**
 * Graylog.php
 *
 * @author    Jerry Ablan <jablan@silverpop.com>
 * @copyright Copyright (c) 2012 Silverpop Systems, Inc.
 * @link      http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @filesource
 */
namespace CIS\Services\Graylog;

/**
 * Graylog
 */
interface Graylog
{
    //**************************************************************************
    //* Constants
    //**************************************************************************

    /**
     * @var string Hostname of graylog2 server
     */
    const DefaultHost = 'cislog.atlis1';
    /**
     * @const integer Port that graylog2 server listens on
     */
    const DefaultPort = 12201;
    /**
     * @const integer Maximum message size before splitting into chunks
     */
    const MaximumChunkSize = 2048;
    /**
     * @const integer Maximum number of chunks allowed by GELF
     */
    const MaximumChunksAllowed = 128;
    /**
     * @const string GELF version
     */
    const GelfVersion = '1.0';
    /**
     * @const integer Default GELF message level
     */
    const DefaultLevel = \CIS\Enums\GraylogLevel::Notice;
    /**
     * @const string Default facility value for messages
     */
    const DefaultFacility = 'cislib';

}
