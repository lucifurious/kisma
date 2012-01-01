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
	require_once '/opt/sag/src/Sag.php';
}

namespace Kisma\Extensions\Davenport\Utility
{
	//*************************************************************************
	//* Requirements 
	//*************************************************************************

	use Kisma\Utility\CouchDbClient;

	/**
	 * CouchHelper
	 */
	class CouchHelper extends \Kisma\Components\Seed implements \Kisma\IUtility
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
		 * @return CouchDbClient
		 */
		public static function getClient( &$options = array() )
		{
			return CouchDbClient::create( $options );
		}

	}
}