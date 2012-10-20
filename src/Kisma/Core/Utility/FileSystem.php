<?php
/**
 * FileSystem.php
 */
namespace Kisma\Core\Utility;
use \Kisma\Core\Seed;
use Kisma\Core\Exceptions\UtilityException;

/**
 * FileSystem
 * A quicky-little down and dirty file reading utility object with a sprinkle of awesomeness
 *
 * @property-read $fileHandle The handle of the current file
 * @property-read $fileName   The name of the current file
 */
class FileSystem extends Seed implements \Kisma\Core\Interfaces\SeedUtility, \Kisma\Core\Interfaces\GlobFlags
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
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array|object $fileName
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

	//********************************************************************************
	//* Static Utility Methods
	//********************************************************************************

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
	 * Additional flags: GLOB_NODIR, GLOB_PATH, GLOB_NODOTS, GLOB_RECURSE (not original glob() flags, defined here)
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
				if ( ( $flags & self::GLOB_RECURSE ) && is_dir( $_file ) && ( !in_array( $_file, array( '.', '..' ) ) ) )
				{
					$_glob = array_merge(
						$_glob,
						self::array_prepend(
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

	/**
	 * @static
	 *
	 * @param array  $array
	 * @param string $string
	 * @param bool   $deep
	 *
	 * @return array
	 */
	public static function array_prepend( $array, $string, $deep = false )
	{
		if ( empty( $array ) || empty( $string ) )
		{
			return $array;
		}

		foreach ( $array as $key => $element )
		{
			if ( is_array( $element ) )
			{
				if ( $deep )
				{
					$array[$key] = self::array_prepend( $element, $string, $deep );
				}
				else
				{
					trigger_error( 'array_prepend: array element', E_USER_WARNING );
				}
			}
			else
			{
				$array[$key] = $string . $element;
			}
		}

		return $array;
	}

}
