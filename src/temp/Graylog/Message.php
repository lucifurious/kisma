<?php
/**
 * Message.php
 * A GELF message object for sending to the Logger
 *
 * @author      Joe Lafiosca <jlafiosca@silverpop.com>
 * @author      Jerry Ablan <jablan@silverpop.com>
 * @filesource
 */
namespace CIS\Services\Graylog;

use CIS\Utility\Validate;
use CIS\Utility\Environment;
use CIS\Enums\Zone;

/**
 * Message
 */
class Message implements Graylog
{
    //**********************************************************************
    //* Properties
    //**********************************************************************

    /**
     * @var array The GELF message data
     */
    protected $_data = null;
    /**
     * @var array The read-only fields
     */
    protected static $_readOnlyFields
        = array(
            'version',
            'host',
            'timestamp',
            '_id',
            'id',
        );
    /**
     * @var array Our standard logging fields
     */
    protected static $_standardFields
        = array(
            'short_message',
            'full_message',
            'facility',
            'level',
            'line',
            'file'
        );

    //**********************************************************************
    //* Public Methods
    //**********************************************************************

    /**
     * Constructor. Can take a single parameter which is an associative
     * array with the following values:
     *
     * short_message: a short descriptive message (string); required
     * full_message: a long message that can i.e. contain a backtrace and
     *               environment variables (string); optional
     * level: the message level (integer); optional, defaults to informational
     * facility: name of the facility the message pertains to (string);
     *           optional, defaults to 'cislib'
     * _[additional field]: any other field prefixed with an underscore
     *                      will be treated as an additional field
     *
     * @param array $data The message data as described above
     *
     * @throws \CIS\Exceptions\InvalidArgumentValueException
     */
    public function __construct( array $data = null )
    {
        $_host = posix_uname();
        $_host = $_host['nodename'];

        $_time = time();
        $_file = trim( $_SERVER['SCRIPT_FILENAME'] );

        if ( 'cli' == PHP_SAPI && $_file{0} != '/' )
        {
            $_file = $_SERVER['PWD'] . '/' . $_file;
        }

        $this->_data = array(
            'version'   => self::GelfVersion,
            'host'      => $_host,
            'timestamp' => $_time,
            'level'     => \CIS\Utility\Option::get( $data, 'level', self::DefaultLevel ),
            'facility'  => \CIS\Utility\Option::get( $data, 'facility', self::DefaultFacility ),
            'file'      => $_file,
            '_zone'     => Environment::getZone( true ),
        );

        if ( !empty( $data ) )
        {
            $this->_validateData( $data );
        }
    }

    /**
     * @param array $data
     *
     * @return Message
     */
    public static function create( array $data = null )
    {
        $_class = get_called_class();
        return new $_class( $data );
    }

    //**********************************************************************
    //* Protected Methods
    //**********************************************************************

    /**
     * @param string $key   Name of field to update
     * @param mixed  $value Value to update field with; null to unset
     *
     * @return Message
     */
    protected function _setData( $key, $value )
    {
        \CIS\Utility\Option::set( $this->_data, $key, $value );
        return $this;
    }

    /**
     * Static method for verifying that required GELF data fields exist
     *
     * @param array $data Associative array of GELF data
     *
     * @throws \CIS\Exceptions\PropertyException
     * @return boolean True if all required fields are populated; else false
     */
    protected function _validateData( $data )
    {
        foreach ( $data as $_key => $_value )
        {
            if ( in_array( $_key, self::$_readOnlyFields ) )
            {
                throw new \CIS\Exceptions\PropertyException( "Setting value of '{$_key}' is not permitted" );
            }

            if ( in_array( $_key, self::$_standardFields ) )
            {
                call_user_func( array( $this, 'set' . \CIS\Utility\Inflector::tag( $_key ) ), $_value );
                continue;
            }

            //	Otherwise...
            $this->setAdditionalField( $_key, $_value );
        }

        return true;
    }

    //**********************************************************************
    //* Properties
    //**********************************************************************

    /**
     * @param string $key If specified, return value of specific field
     *
     * @return mixed Value of field, null if it doesn't exist; or data array
     */
    public function getData( $key = null )
    {
        if ( null === $key )
        {
            return $this->_data;
        }

        return \CIS\Utility\Option::get( $this->_data[$key] );
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->getData( 'version' );
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->getData( 'host' );
    }

    /**
     * @return string
     */
    public function getShortMessage()
    {
        return $this->getData( 'short_message' );
    }

    /**
     * @param string $value A short descriptive message
     *
     * @return \CIS\Services\Graylog\Message
     */
    public function setShortMessage( $value )
    {
        return $this->_setData( 'short_message', Validate::isString( 'Short Message', $value ) );
    }

    /**
     * @return string
     */
    public function getFullMessage()
    {
        return $this->getData( 'full_message' );
    }

    /**
     * @param string $value A long message that can i.e. contain a backtrace
     *                      and environment variables; null to omit
     *
     * @return \CIS\Services\Graylog\Message
     */
    public function setFullMessage( $value )
    {
        return $this->_setData( 'full_message', Validate::isString( 'Full Message', $value, false ) );
    }

    /**
     * @return integer
     */
    public function getTimestamp()
    {
        return $this->getData( 'timestamp' );
    }

    /**
     * @return integer
     */
    public function getLevel()
    {
        return $this->getData( 'level' );
    }

    /**
     * @param integer $value The message level; null for default
     *
     * @return \CIS\Services\Graylog\Message
     * @throws \CIS\Exceptions\InvalidArgumentValueException
     */
    public function setLevel( $value = self::DefaultLevel )
    {
        $_value = Validate::isInteger( 'Level', $value, false );

        if ( $_value < \CIS\Enums\GraylogLevel::Emergency || $_value > \CIS\Enums\GraylogLevel::Debug )
        {
            $_value = \CIS\Enums\GraylogLevel::fromMonologLevel( $_value );
        }

        return $this->_setData( 'level', $_value );
    }

    /**
     * @return string
     */
    public function getFacility()
    {
        return $this->getData( 'facility' );
    }

    /**
     * @param string $value Facility the message pertains to; null for default
     *
     * @return \CIS\Services\Graylog\Message
     */
    public function setFacility( $value )
    {
        $_value = Validate::isString( 'Facility', $value, false );

        if ( $_value === null )
        {
            $_value = self::DefaultFacility;
        }

        return $this->_setData( 'facility', $_value );
    }

    /**
     * @return integer
     */
    public function getLine()
    {
        return $this->getData( 'line' );
    }

    /**
     * @param integer $value The line in the file to look at; null to omit
     *
     * @return \CIS\Services\Graylog\Message
     */
    public function setLine( $value )
    {
        return $this->_setData( 'line', Validate::isInteger( 'Line', $value, false ) );
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->getData( 'file' );
    }

    /**
     * @param integer $value The file to look at; null to omit
     *
     * @return \CIS\Services\Graylog\Message
     */
    public function setFile( $value )
    {
        return $this->_setData( 'file', Validate::isString( 'File', $value, false ) );
    }

    /**
     * @param string  $key     The key of the additional field (w/o underscore)
     * @param boolean $prepend Whether or not to prepend underscore to key
     *
     * @throws \CIS\Exceptions\InvalidArgumentValueException
     * @return string
     */
    public function getAdditionalField( $key, $prepend = true )
    {
        $_key = Validate::isString( 'Additional Field Key', $key, true, false );
        $_key = '_' . $_key;

        if ( $prepend )
        {
            $_key = '_' . $_key;
        }
        else
        {
            if ( $_key{0} != '_' )
            {
                throw new \CIS\Exceptions\InvalidArgumentValueException( "Additional Field Key '{$_key}' does not begin with an underscore" );
            }
        }

        return $this->getData( $_key );
    }

    /**
     * @param string  $key     The key of the additional field
     * @param string  $value   The value of the additional field; null to unset
     * @param boolean $prepend Whether or not to prepend underscore to key
     *
     * @return \CIS\Services\Graylog\Message
     * @throws \CIS\Exceptions\InvalidArgumentValueException
     */
    public function setAdditionalField( $key, $value, $prepend = true )
    {
        $_key = Validate::isString( 'Additional Field Key', $key, true, false );

        if ( !preg_match( '#^\\w+$#', $_key ) )
        {
            throw new \CIS\Exceptions\InvalidArgumentValueException( "Additional Field Key '{$_key}' contains non-word characters" );
        }

        if ( $prepend )
        {
            $_key = '_' . $_key;
        }
        else
        {
            if ( $_key{0} != '_' )
            {
                throw new \CIS\Exceptions\InvalidArgumentValueException( "Additional Field Key '{$_key}' does not begin with an underscore" );
            }
        }

        if ( $_key == '_id' )
        {
            throw new \CIS\Exceptions\InvalidArgumentValueException( 'Additional Field Key \'_id\' is not allowed' );
        }

        return $this->_setData( $_key, Validate::isString( 'Additional Field Value', $value, false, false ) );
    }

}
