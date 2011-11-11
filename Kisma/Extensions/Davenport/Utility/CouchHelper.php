<?php
/**
 * CouchHelper.php
 * Kisma(tm) : PHP Nanoframework Extension (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link		  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license	   http://github.com/Pogostick/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @package	   kisma.extensions.davenport
 * @since		 v1.0.0
 * @filesource
 */
namespace
{
	//*************************************************************************
	//* Requirements
	//*************************************************************************

	/**
	 *  Require the Sag library, but keep it outside of Kisma
	 * @todo Finish up the alias system and replace this nonsense
	 */
	/** @noinspection PhpIncludeInspection */
	require_once \K::glue( DIRECTORY_SEPARATOR, \K::getSetting( \KismaSettings::BasePath ), 'vendors', 'sag', 'src', 'Sag.php' );
}

namespace Kisma\Extensions\Davenport\Utility
{
	//*************************************************************************
	//* Use-ages and Aliases 
	//*************************************************************************

	//*************************************************************************
	//* Requirements 
	//*************************************************************************

	/**
	 * CouchHelper
	 */
	class CouchHelper extends \Kisma\Components\SubComponent implements \Kisma\IUtility
	{
		//*************************************************************************
		//* Public Methods 
		//*************************************************************************

		/**
		 * @static
		 * @param array $options
		 *
		 * Options are:
		 *
		 * Name                Default
		 * -------------       -------------
		 * host_name		   localhost
		 * host_port		   5984
		 * user_name		   null
		 * password			   null
		 * database_name	   null
		 *
		 * @return \Sag
		 */
		public static function getSagClient( &$options = array() )
		{
			$_hostName = \K::o( $options, 'host_name', 'localhost', true );
			$_hostPort = \K::o( $options, 'host_port', 5984, true );
			$_userName = \K::o( $options, 'user_name', null, true );
			$_password	 = \K::o( $options, 'password', null, true );
			$_databaseName = \K::o( $options, 'database_name', null, true );

			$_client = new \Sag( $_hostName, $_hostPort );

			//	Defaults to CURL
//			$_client->setHTTPAdapter( \Sag::$HTTP_CURL );

			if ( null !== $_userName )
			{
				$_client->login( $_userName, $_password );
			}

			if ( null !== $_databaseName )
			{
				$_client->setDatabase( $_databaseName, true );
			}

			return $_client;
		}

	}
}