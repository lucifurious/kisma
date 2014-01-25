<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
 *
 * Kisma(tm) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kisma(tm) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kisma(tm).  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Kisma\Core\Utility;

use Kisma\Core\Enums\GlobFlags;
use Kisma\Core\Exceptions\UtilityException;
use Kisma\Core\Interfaces\UtilityLike;
use Kisma\Core\Seed;

/**
 * FileSystem
 * A quicky-little down and dirty file reading utility object with a sprinkle of awesomeness
 */
class FileSystem extends Seed implements UtilityLike
{
	//********************************************************************************
	//* Members
	//********************************************************************************

	/**
	 * @var string The name of the current file
	 */
	protected $_fileName = false;
	/**
	 * @var \resource The handle of the current file
	 */
	protected $_fileHandle = false;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param array|object $fileName
	 *
	 * @return \Kisma\Core\Utility\FileSystem
	 */
	public function __construct( $fileName )
	{
		parent::__construct();

		$this->_fileName = $fileName;
		$this->open();
	}

	/**
	 * @return bool
	 */
	public function validHandle()
	{
		return ( false !== $this->_fileHandle );
	}

	/**
	 * @return bool
	 */
	public function open()
	{
		if ( file_exists( $this->_fileName ) )
		{
			$this->_fileHandle = @fopen( $this->_fileName, 'r' );
		}

		return $this->validHandle();
	}

	/**
	 * Close the file
	 */
	public function close()
	{
		if ( $this->_fileHandle )
		{
			@fclose( $this->_fileHandle );
		}

		$this->_fileHandle = false;
	}

	/**
	 * @return int|bool
	 */
	public function filesize()
	{
		return $this->validHandle() ? filesize( $this->_fileName ) : false;
	}

	/**
	 * @return int|bool
	 */
	public function atime()
	{
		return $this->validHandle() ? fileatime( $this->_fileName ) : false;
	}

	/**
	 * @return int|bool
	 */
	public function fileowner()
	{
		return $this->validHandle() ? fileowner( $this->_fileName ) : false;
	}

	/**
	 * @return int|bool
	 */
	public function filegroup()
	{
		return $this->validHandle() ? filegroup( $this->_fileName ) : false;
	}

	/**
	 * @param int $offset
	 * @param int $whence
	 *
	 * @return int|bool
	 */
	public function fseek( $offset = 0, $whence = SEEK_SET )
	{
		return $this->validHandle() ? fseek( $this->_fileHandle, $offset, $whence ) : false;
	}

	/**
	 * @return int|bool
	 */
	public function ftell()
	{
		return $this->validHandle() ? ftell( $this->_fileHandle ) : false;
	}

	/**
	 * Retrieves a string from the current file
	 *
	 * @param int $length
	 *
	 * @return string|bool
	 */
	public function fgets( $length = null )
	{
		return $this->validHandle() ? fgets( $this->_fileHandle, $length ) : false;
	}

	/**
	 * @return string
	 */
	public function getFileName()
	{
		return $this->_fileName;
	}

	/**
	 * @return int
	 */
	public function getFileHandle()
	{
		return $this->_fileHandle;
	}

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
		$_validate = $_path = null;

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
			if ( $_validate && !$forceCreate )
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
	 * Additional flags: GlobFlags::GLOB_NODIR, GlobFlags::GLOB_PATH, GlobFlags::GLOB_NODOTS, GlobFlags::GLOB_RECURSE (not original glob() flags, defined here)
	 *
	 * @author BigueNique AT yahoo DOT ca
	 *
	 * @param string $pattern
	 * @param int    $flags
	 *
	 * @return array|bool
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
				if ( ( $flags & GlobFlags::GLOB_RECURSE ) && is_dir( $_file ) && ( !in_array( $_file, array( '.', '..' ) ) ) )
				{
					$_glob = array_merge(
						$_glob,
						Scalar::array_prepend(
							self::glob(
								$_path . '/' . $_file . '/' . $_mask,
								$flags
							),
							( $flags & GlobFlags::GLOB_PATH ? '' : $_file . '/' )
						)
					);
				}

				// Match file mask
				if ( fnmatch( $_mask, $_file ) )
				{
					if ( ( ( !( $flags & GLOB_ONLYDIR ) ) || is_dir( "$_path/$_file" ) ) &&
						 ( ( !( $flags & GlobFlags::GLOB_NODIR ) ) || ( !is_dir( $_path . '/' . $_file ) ) ) &&
						 ( ( !( $flags & GlobFlags::GLOB_NODOTS ) ) || ( !in_array( $_file, array( '.', '..' ) ) ) )
					)
					{
						$_glob[] = ( $flags & GlobFlags::GLOB_PATH ? $_path . '/' : '' ) . $_file . ( $flags & GLOB_MARK ? '/' : '' );
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
