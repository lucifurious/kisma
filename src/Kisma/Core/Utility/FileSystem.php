<?php
/**
 * FileSystem.php
 */
namespace Kisma\Core\Utility;
use \Kisma\Core\Seed;
use Kisma\Core\Exceptions\UtilityException;

/**
 * FileSystem
 */
class FileSystem extends Seed implements \Kisma\Core\Interfaces\SeedUtility
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const GLOB_NODIR = 0x0100;
	/**
	 * @var int
	 */
	const GLOB_PATH = 0x0200;
	/**
	 * @var int
	 */
	const GLOB_NODOTS = 0x0400;
	/**
	 * @var int
	 */
	const GLOB_RECURSE = 0x0800;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Builds a path from arguments and validates existence.
	 *
	 * @param bool $validate    If true, will check path with is_dir.
	 * @param bool $forceCreate If true, and result path doesn't exist, it will be created
	 *
	 * @throws \Kisma\Core\Exceptions\UtilityException
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
					throw new UtilityException( 'The result path "' . $_path . '" could not be created.' );
				}
			}
		}

		return $_path;
	}

	/**
	 * As found on php.net posted by: BigueNique at yahoo dot ca 20-Apr-2010 07:15
	 * A safe empowered glob().
	 *
	 * Supported flags: GLOB_MARK, GLOB_NOSORT, GLOB_ONLYDIR
	 * Additional flags: self::GLOB_NODIR, self::GLOB_PATH, self::GLOB_NODOTS, self::GLOB_RECURSE (not original glob() flags, defined here)
	 *
	 * @author BigueNique AT yahoo DOT ca
	 *
	 * @param string $pattern
	 * @param int    $flags
	 *
	 * @return array|false
	 */
	public static function glob( $pattern, $flags = 0 )
	{
		$_split = explode( '/', str_replace( '\\', '/', $pattern ) );
		$_mask = array_pop( $_split );
		$_path = implode( '/', $_split );
		$_glob = false;

		if ( false !== ( $_directory = opendir( $_path ) ) )
		{
			$_glob = array();

			while ( false !== ( $_file = readdir( $_directory ) ) )
			{
				//	Recurse directories
				if ( ( $flags & self::GLOB_RECURSE ) && is_dir( $_file ) && ( !in_array( $_file, array( '.', '..' ) ) ) )
				{
					$_glob = array_merge(
						$_glob,
						Scalar::array_prepend(
							self::glob(
								$_path . '/' . $_file . '/' . $_mask,
								$flags
							),
							( $flags & self::GLOB_PATH ? '' : $_file . '/' )
						)
					);
				}

				// Match file mask
				if ( fnmatch( $_mask, $_file ) )
				{
					if ( ( ( !( $flags & GLOB_ONLYDIR ) ) || is_dir( "$_path/$_file" ) )
						&& ( ( !( $flags & self::GLOB_NODIR ) ) || ( !is_dir( $_path . '/' . $_file ) ) )
						&& ( ( !( $flags & self::GLOB_NODOTS ) ) || ( !in_array( $_file, array( '.', '..' ) ) ) )
					)
					{
						$_glob[] = ( $flags & self::GLOB_PATH ? $_path . '/' : '' ) . $_file . ( $flags & GLOB_MARK ? '/' : '' );
					}
				}
			}

			closedir( $_directory );

			if ( !( $flags & GLOB_NOSORT ) )
			{
				sort( $_glob );
			}
		}

		return $_glob;
	}

}
