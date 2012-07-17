<?php
/**
 * @file
 * Provides ...
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Framework
 * @package kisma
 * @since 1.0.0
 *
 * @ingroup framework
 */

namespace Kisma\Utility;

//*************************************************************************
//* Aliases
//*************************************************************************

use \Kisma\Components\Seed;

/**
 * FileSystem
 */
class FileSystem extends Seed implements \Kisma\IUtility
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Builds a path from arguments and validates existence.
	 * @param bool $validate If true, will check path with is_dir.
	 * @return bool|null|string
	 */
	public static function makePath( $validate = true )
	{
		$_arguments = func_get_args();
		$_path = null;
		$_validate = true;

		foreach ( $_arguments as $_part )
		{
			if ( is_bool( $_part ) )
			{
				$_validate = $_part;
				continue;
			}

			$_path .= '/' . trim( $_part, '/ ' );
		}

		if ( $_validate && !is_dir( $_path = realpath( $_path ) ) )
		{
			Log::error( 'Path "' . $_path . '" invalid.' );
			return false;
		}

		return $_path;
	}

}
