<?php
/**
 * Ftp.php
 *
 * Copyright (c) 2012 Silverpop Systems, Inc.
 * http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 * @filesource
 */
namespace CIS\Services\Delivery;

/**
 * Ftp
 *
 * Settings used:
 *
 * string        $hostName
 * int           $hostPort
 * string        $userName (optional if keys are used)
 * string        $password (optional if keys are used)
 * string        $sourcePath
 * string        $sourceFileName
 * string        $destinationPath
 * string        $destinationFileName
 */
class Ftp extends \CIS\Services\BaseDeliveryService
{
	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function deliver( $settings = null )
	{
		$_result = true;
		$_destFile = null;

		$_settings = parent::deliver( $settings );

		$this->logDebug( '>>FTP Delivery Service' );

		//	Provided by TrexSchedule when dispatching this delivery
		$_sourceFile
			= rtrim( $_settings->sourcePath, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR .
			  $_settings->sourceFileName;

		$_destFile
			= rtrim( $_settings->destinationPath, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR .
			  ( $this->_buildDestinationFileName( $_settings ) ? : $_settings->sourceFileName );

		try
		{
			$this->logDebug( 'FTP Deliver sourceFile: ' . $_sourceFile );
			$this->logDebug( '              destFile: ' . $_destFile );

			require_once 'cislib/FTP.php';

			$_ftp = new \FTP(
				$_settings->hostName,
				$_settings->userName,
				$_settings->password,
				$_settings->hostPort ? : 21,
				$_settings->passiveMode ? : false
			);

			$_ftp->put( $_sourceFile, $_destFile );

			$this->logInfo( 'File delivery via FTP complete.' );
		}
		catch ( \Exception $_ex )
		{
			$this->logError( 'Delivery Exception: ' . $_ex->getMessage() );
			$_result = false;
		}

		$this->logInfo( '<<FTP Delivery Service > ' . ( $_result ? 'Success' : 'Fail' ) );

		return $_result;
	}

}
