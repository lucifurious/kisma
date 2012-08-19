<?php
/**
 * @file
 *            Provides ...
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license   http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author    Jerry Ablan <kisma@pogostick.com>
 * @category  Framework
 * @package   kisma
 * @since     1.0.0
 *
 * @ingroup   framework
 */

namespace Kisma\Core\Utility;

//*************************************************************************
//* Aliases
//*************************************************************************

use \Kisma\Core\Seed;

/**
 * FileSystem
 */
class FileSystem extends Seed implements \Kisma\Core\Interfaces\SeedUtility
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Builds a path from arguments and validates existence.
	 *
	 * @param bool $validate    If true, will check path with is_dir.
	 * @param bool $forceCreate If true, and result path doesn't exist, it will be created
	 * @param int  $umask
	 *
	 * @internal param int $mode
	 *
	 * @return bool|null|string
	 */
	public static function makePath( $validate = true, $forceCreate = false )
	{
		$_arguments = func_get_args();
		$_path = null;

		foreach ( $_arguments as $_part )
		{
			if ( is_bool( $_part ) )
			{
				$_validate = $_part;
				continue;
			}

			$_path .= DIRECTORY_SEPARATOR . trim( $_part, DIRECTORY_SEPARATOR . ' ' );
		}

		if ( !is_dir( $_path = realpath( $_path ) ) )
		{
			if ( $validate && !$forceCreate )
			{
				return false;
			}

			if ( $forceCreate )
			{
				if ( false === @mkdir( $_path, 0, true ) )
				{
					throw new \Kisma\UtilityException( 'The result path "' . $_path . '" could not be created.' );
				}
			}
		}

		return $_path;
	}

}
