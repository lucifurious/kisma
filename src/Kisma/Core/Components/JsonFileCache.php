<?php
namespace Kisma\Core\Components;

use Doctrine\Common\Cache\FilesystemCache;

/**
 * Cache driver for Doctrine that stores data in JSON format
 */
class JsonFileCache extends FilesystemCache
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string
     */
    const EXTENSION = '.flexistore.json';

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * {@inheritdoc}
     */
    protected $extension = self::EXTENSION;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * {@inheritdoc}
     */
    protected function doFetch( $id )
    {
        if ( false === ( $_data = $this->_decodeFile( $_fileName = $this->getFilename( $id ), $_ttl ) ) )
        {
            return false;
        }

        if ( 0 !== $_ttl && $_ttl < time() )
        {
            //  Expired file, kill!
            @unlink( $_fileName );

            return false;
        }

        return $_data;
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains( $id )
    {
        if ( false === $this->_decodeFile( $_fileName = $this->getFilename( $id ), $_ttl ) )
        {
            return false;
        }

        $_contains =
            0 === $_ttl || $_ttl > time();

        // Expired file, kill!
        if ( !$_contains )
        {
            @unlink( $_fileName );
        }

        return $_contains;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave( $id, $data, $ttl = 0 )
    {
        if ( $ttl > 0 )
        {
            $ttl = time() + $ttl;
        }

        $_payload = array(
            '.ttl'  => $ttl,
            '.data' => serialize( $data ),
        );

        return $this->_encodeFile( $this->getFilename( $id ), $_payload );
    }

    /**
     * Given a file name, open it and JSON decode it.
     *
     * @param string $fileName
     * @param int    $ttl
     *
     * @return bool|mixed False on missing file or error
     */
    protected function _decodeFile( $fileName, &$ttl = null )
    {
        if ( !is_file( $fileName ) || false === ( $_json = file_get_contents( $fileName ) ) || empty( $_json ) )
        {
            return false;
        }

        if ( false === ( $_stored = json_decode( $_json, true ) ) || JSON_ERROR_NONE != json_last_error() )
        {
            return false;
        }

        if ( array_key_exists( '.ttl', $_stored ) )
        {
            $ttl = (integer)$_stored['.ttl'];
        }
        else
        {
            $ttl = -1;
        }

        return
            array_key_exists( '.data', $_stored )
                ? ( @unserialize( $_stored['.data'] ) ?: $_stored['.data'] )
                : null;
    }

    /**
     * Given a file name and some data, encode and save.
     *
     * @param string $fileName
     * @param mixed  $data
     *
     * @return bool
     */
    protected function _encodeFile( $fileName, $data )
    {
        $_filePath = dirname( $fileName );

        if ( !is_dir( $_filePath ) )
        {
            if ( false === @mkdir( $_filePath, 0777, true ) && !is_dir( $_filePath ) )
            {
                return false;
            }
        }
        elseif ( !is_writable( $_filePath ) )
        {
            return false;
        }

        $_tempFile = tempnam( $_filePath, basename( $fileName ) );

        if ( false !== file_put_contents( $_tempFile, json_encode( $data ) ) && @rename( $_tempFile, $fileName ) )
        {
            @chmod( $fileName, 0666 & ~umask() );

            return true;
        }

        return false;
    }
}
